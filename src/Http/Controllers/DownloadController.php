<?php

namespace SinapsTeknologi\GDriveStorage\Http\Controllers;

use Illuminate\Http\Request;
use SinapsTeknologi\GDriveStorage\Services\GDriveService;

class DownloadController
{
    public function download(Request $request, GDriveService $drive)
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $encoded = $request->query('path');

        if (! $encoded) {
            abort(404);
        }

        $path = base64_decode($encoded, true);

        if ($path === false || trim($path) === '') {
            abort(404);
        }

        $name = $request->query('name') ?? basename($path);

        // sanitize filename
        $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);

        return $drive->download($path, $name);
    }
}

