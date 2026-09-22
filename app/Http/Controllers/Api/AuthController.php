<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->verifyCaptcha($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'operator',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $this->verifyCaptcha($request);

        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Berhasil logout']);
    }

    private function verifyCaptcha(Request $request): void
    {
        $request->validate([
            'captcha_token' => 'required|string',
        ]);

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('captcha_token'),
            'remoteip' => $request->ip(),
        ]);

        $result = $response->json();

        Log::info('reCAPTCHA verification result', [
            'success' => $result['success'] ?? false,
            'hostname' => $result['hostname'] ?? null,
            'error_codes' => $result['error-codes'] ?? [],
        ]);

        if (! ($result['success'] ?? false)) {
            Log::warning('reCAPTCHA verification failed', [
                'error_codes' => $result['error-codes'] ?? [],
                'hostname' => $result['hostname'] ?? null,
            ]);
            throw ValidationException::withMessages([
                'captcha_token' => ['Verifikasi captcha gagal, silakan coba lagi.'],
            ]);
        }
    }
}