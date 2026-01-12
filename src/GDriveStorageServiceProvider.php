<?php

namespace SinapsTeknologi\GDriveStorage;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class GDriveStorageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/gdrive-storage.php',
            'gdrive-storage'
        );

        $this->app->singleton(
            Services\GDriveService::class
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/gdrive-storage.php' => config_path('gdrive-storage.php'),
        ], 'gdrive-storage-config');

        Storage::extend('gdrive', function ($app, $config) {
            return new Filesystems\GDriveFilesystem(
                $app->make(Services\GDriveService::class)
            );
        });

        if (config('gdrive-storage.routes.enabled', true)) {
            Route::middleware('web')
                ->name('gdrive-storage.')
                ->group(function () {
                    Route::get(
                        '/_gdrive/download',
                        [Http\Controllers\DownloadController::class, 'download']
                    )->name('download')->middleware('signed');
                });
        }
    }
}

