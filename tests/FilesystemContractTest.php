<?php

namespace SinapsTeknologi\GDriveStorage\Tests;

use Illuminate\Support\Facades\Storage;

class FilesystemContractTest extends TestCase
{
    public function test_methods_exist(): void
    {
        $disk = Storage::disk('gdrive');

        $this->assertTrue(method_exists($disk, 'put'));
        $this->assertTrue(method_exists($disk, 'exists'));
        $this->assertTrue(method_exists($disk, 'files'));
        $this->assertTrue(method_exists($disk, 'directories'));
        $this->assertTrue(method_exists($disk, 'delete'));
        $this->assertTrue(method_exists($disk, 'temporaryUrl'));
    }
}
