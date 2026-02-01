<?php

namespace SinapsTeknologi\GDriveStorage\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SinapsTeknologi\GDriveStorage\GDriveStorageServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            GDriveStorageServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('filesystems.disks.gdrive', [
            'driver' => 'gdrive',
        ]);

        $app['config']->set('gdrive-storage', [
            'service_account_path' => __DIR__.'/fixtures/fake.json',
            'root_folder_id' => 'FAKE_ROOT_ID',
        ]);
    }
}
