<?php

namespace App\Http\Controllers;

use App\Models\Lpju;
use App\Models\Rambu;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\Response;

class QrCodeController extends Controller
{
    public function lpju(string $id): Response
    {
        $asset = Lpju::findOrFail($id);

        return $this->render(route('assets.lpju.show', $asset->id));
    }

    public function rambu(string $id): Response
    {
        $asset = Rambu::findOrFail($id);

        return $this->render(route('assets.rambu.show', $asset->id));
    }

    private function render(string $url): Response
    {
        $svg = (new QRCode)->render($url);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
            'Content-Disposition' => 'inline',
        ]);
    }
}
