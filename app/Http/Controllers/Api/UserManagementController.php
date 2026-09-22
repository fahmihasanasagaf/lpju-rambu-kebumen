<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    private function output(User $user): array
    {
        return $user->only(['id', 'name', 'email', 'role', 'created_at', 'updated_at']);
    }

    public function index()
    {
        return response()->json(User::query()->latest()->get()->map(fn (User $user) => $this->output($user)));
    }

    public function show(string $id)
    {
        return response()->json($this->output(User::findOrFail($id)));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'operator'])],
        ]);
        $data['password'] = Hash::make($data['password']);
        return response()->json($this->output(User::create($data)), 201);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', Rule::in(['admin', 'operator'])],
        ]);
        if (isset($data['role']) && $user->role === 'admin' && $data['role'] === 'operator'
            && User::where('role', 'admin')->count() === 1) {
            return response()->json(['message' => 'Admin terakhir tidak dapat diturunkan menjadi operator.'], 422);
        }

        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']); else unset($data['password']);
        $user->update($data);
        return response()->json($this->output($user->fresh()));
    }

    public function destroy(Request $request, string $id)
    {
        if ((int) $request->user()->id === (int) $id) {
            return response()->json(['message' => 'Akun yang sedang digunakan tidak dapat dihapus.'], 422);
        }

        $user = User::findOrFail($id);
        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            return response()->json(['message' => 'Admin terakhir tidak dapat dihapus.'], 422);
        }

        $user->delete();
        return response()->json(['message' => 'Pengguna berhasil dihapus.']);
    }
}
