<?php

namespace SinapsTeknologi\GDriveStorage\Http\Controllers;

use Illuminate\Http\Request;
use SinapsTeknologi\GDriveStorage\Services\GDriveService;

class DownloadController
{
    public function download(Request $request, GDriveService $drive)
    {
        $path = base64_decode($request->query('path'));
        $requestedName = $request->query('name');

        if ($requestedName) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            if ($ext && ! str_ends_with($requestedName, '.'.$ext)) {
                $requestedName .= '.'.$ext;
            }
        }

        return $drive->download($path, $requestedName);
    }
}

