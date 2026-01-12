<?php

namespace SinapsTeknologi\GDriveStorage\Contracts;

interface GDriveServiceContract
{
    public function put(string $path, string $contents): void;

    public function get(string $path): string;

    public function exists(string $path): bool;

    public function delete(string $path): bool;

    public function files(string $path = ''): array;

    public function directories(string $path = ''): array;
}
