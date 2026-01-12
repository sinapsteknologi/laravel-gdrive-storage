<?php

namespace SinapsTeknologi\GDriveStorage\Facades;

use Illuminate\Support\Facades\Facade;

class GDriveStorage extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SinapsTeknologi\GDriveStorage\Services\GDriveService::class;
    }
}
