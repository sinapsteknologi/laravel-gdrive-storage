<?php

namespace SinapsTeknologi\GDriveStorage\Tests;

use Illuminate\Support\Facades\Storage;

class SmokeTest extends TestCase
{
    public function test_gdrive_disk_is_registered(): void
    {
        $disk = Storage::disk('gdrive');

        $this->assertNotNull($disk);
    }
}
