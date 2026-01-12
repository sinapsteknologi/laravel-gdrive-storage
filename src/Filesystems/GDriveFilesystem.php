<?php

namespace SinapsTeknologi\GDriveStorage\Filesystems;

use SinapsTeknologi\GDriveStorage\Contracts\GDriveServiceContract;

class GDriveFilesystem
{
    public function __construct(
        protected GDriveServiceContract $drive
    ) {}

    public function put(string $path, $contents): bool
    {
        $this->drive->put($path, $contents);

        return true;
    }

    public function putFileAs(string $path, $file, string $name): bool
    {
        $fullPath = $path
            ? trim($path, '/').'/'.$name
            : $name;

        $this->drive->put($fullPath, file_get_contents($file->getRealPath()));

        return true;
    }

    public function exists(string $path): bool
    {
        return $this->drive->exists($path);
    }

    public function delete(string $path): bool
    {
        return $this->drive->delete($path);
    }

    public function files(string $path = ''): array
    {
        return $this->drive->files($path);
    }

    public function directories(string $path = ''): array
    {
        return $this->drive->directories($path);
    }

    public function size(string $path): int
    {
        return $this->drive->size($path);
    }

    public function get(string $path): string
    {
        return $this->drive->get($path);
    }

    public function download(string $path, ?string $name = null)
    {
        return $this->drive->download($path, $name);
    }

    public function temporaryUrl(string $path, $expiration, array $options = []): string
    {
        return $this->drive->temporaryUrl($path, $expiration, $options);
    }

    public function copy(string $from, string $to): bool
{
    return $this->drive->copy($from, $to);
}

public function move(string $from, string $to): bool
{
    return $this->drive->move($from, $to);
}

public function makeDirectory(string $path): bool
{
    return $this->drive->makeDirectory($path);
}

public function deleteDirectory(string $path): bool
{
    return $this->drive->deleteDirectory($path);
}

public function url(string $path): string
{
    return $this->drive->url($path);
}

public function lastModified(string $path): int
{
    return $this->drive->lastModified($path);
}

public function mimeType(string $path): string
{
    return $this->drive->mimeType($path);
}

}
