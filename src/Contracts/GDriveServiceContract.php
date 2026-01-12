<?php

namespace SinapsTeknologi\GDriveStorage\Contracts;

interface GDriveServiceContract
{
    public function exists(string $path): bool;
    public function put(string $path, string $contents): void;
    public function get(string $path): string;
    public function delete(string $path): bool;
    public function files(string $path = ''): array;
    public function directories(string $path = ''): array;
    public function size(string $path): int;
    public function download(string $path, ?string $name = null);
    public function temporaryUrl(string $path, $expiration, array $options = []): string;
}
