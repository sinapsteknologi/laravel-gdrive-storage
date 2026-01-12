<?php

namespace SinapsTeknologi\GDriveStorage\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class GDriveService
{
    protected Drive $drive;

    protected string $rootFolderId;

    public function __construct()
    {
        $client = new Client;

        $authPath = config('gdrive-storage.service_account_path');

        if (! $authPath || ! file_exists($authPath)) {
            throw new \RuntimeException(
                'Google Drive service account file not found.
                Set GDRIVE_SERVICE_ACCOUNT_PATH or publish config.'
            );
        }

        $client->setAuthConfig($authPath);

        $client->setScopes([
            Drive::DRIVE,
        ]);

        $this->drive = new Drive($client);

        $this->rootFolderId = config('gdrive-storage.root_folder_id');

        if (! $this->rootFolderId) {
            throw new \RuntimeException(
                'GDrive root folder ID is not configured. Set GDRIVE_ROOT_FOLDER_ID.'
            );
        }
    }

    /* =========================================================
     |  CORE RESOLVER
     ========================================================= */

    protected function resolvePath(string $path): ?array
    {
        $path = trim($path, '/');

        if ($path === '' || $path === '.') {
            return [
                'id' => $this->rootFolderId,
                'type' => 'folder',
            ];
        }

        $segments = explode('/', $path);
        $parentId = $this->rootFolderId;

        foreach ($segments as $index => $segment) {
            $result = $this->drive->files->listFiles([
                'q' => sprintf(
                    "'%s' in parents and name = '%s' and trashed = false",
                    $parentId,
                    addslashes($segment)
                ),
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true,
                'fields' => 'files(id, mimeType)',
            ]);

            if (count($result->files) === 0) {
                return null;
            }

            $file = $result->files[0];
            $parentId = $file->id;

            // last segment → determine type
            if ($index === count($segments) - 1) {
                return [
                    'id' => $file->id,
                    'type' => $file->mimeType === 'application/vnd.google-apps.folder'
                        ? 'folder'
                        : 'file',
                ];
            }
        }

        return null;
    }

    protected function ensureFolder(string $name, string $parentId): string
    {
        $existing = $this->drive->files->listFiles([
            'q' => sprintf(
                "'%s' in parents and name = '%s' and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
                $parentId,
                addslashes($name)
            ),
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true,
            'fields' => 'files(id)',
        ]);

        if (count($existing->files)) {
            return $existing->files[0]->id;
        }

        $folder = new DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId],
        ]);

        $created = $this->drive->files->create($folder, [
            'supportsAllDrives' => true,
        ]);

        return $created->id;
    }

    protected function ensurePath(array $segments): string
    {
        $parentId = $this->rootFolderId;

        foreach ($segments as $segment) {
            $parentId = $this->ensureFolder($segment, $parentId);
        }

        return $parentId;
    }

    /* =========================================================
     |  STORAGE-LIKE METHODS
     ========================================================= */

    public function exists(string $path): bool
    {
        return $this->resolvePath($path) !== null;
    }

    public function put(string $path, string $contents): void
    {
        $segments = explode('/', trim($path, '/'));
        $filename = array_pop($segments);
        $parentId = $this->ensurePath($segments);

        $resolved = $this->resolvePath($path);

        if ($resolved && $resolved['type'] === 'file') {
            $this->drive->files->update(
                $resolved['id'],
                new DriveFile,
                [
                    'data' => $contents,
                    'uploadType' => 'multipart',
                    'supportsAllDrives' => true,
                ]
            );

            return;
        }

        $this->drive->files->create(
            new DriveFile([
                'name' => $filename,
                'parents' => [$parentId],
            ]),
            [
                'data' => $contents,
                'uploadType' => 'multipart',
                'supportsAllDrives' => true,
            ]
        );
    }

    public function get(string $path): string
    {
        $resolved = $this->resolvePath($path);

        if (! $resolved || $resolved['type'] !== 'file') {
            throw new \RuntimeException("File not found: {$path}");
        }

        $response = $this->drive->files->get($resolved['id'], [
            'alt' => 'media',
            'supportsAllDrives' => true,
        ]);

        return $response->getBody()->getContents();
    }

    public function size(string $path): int
    {
        [$fileId] = $this->resolvePath($path);

        $file = $this->drive->files->get($fileId, [
            'fields' => 'size',
            'supportsAllDrives' => true,
        ]);

        return (int) $file->size;
    }

    public function files(string $path = ''): array
    {
        if ($path === '' || $path === '.') {
            $parentId = $this->rootFolderId;
        } else {
            $resolved = $this->resolvePath($path);

            if (! $resolved || $resolved['type'] !== 'folder') {
                return [];
            }

            $parentId = $resolved['id'];
        }

        $result = $this->drive->files->listFiles([
            'q' => "'{$parentId}' in parents and mimeType != 'application/vnd.google-apps.folder' and trashed = false",
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true,
            'fields' => 'files(name)',
        ]);

        return collect($result->files)->pluck('name')->all();
    }

    public function directories(string $path = ''): array
    {
        if ($path === '' || $path === '.') {
            $parentId = $this->rootFolderId;
        } else {
            $resolved = $this->resolvePath($path);

            if (! $resolved || $resolved['type'] !== 'folder') {
                return [];
            }

            $parentId = $resolved['id'];
        }

        $result = $this->drive->files->listFiles([
            'q' => "'{$parentId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true,
            'fields' => 'files(name)',
        ]);

        return collect($result->files)->pluck('name')->all();
    }

    public function delete(string $path): bool
    {
        $resolved = $this->resolvePath($path);

        if (! $resolved) {
            return false;
        }

        $this->drive->files->delete($resolved['id'], [
            'supportsAllDrives' => true,
        ]);

        return true;
    }

    public function copy(string $from, string $to): bool
    {
        $resolved = $this->resolvePath($from);
        if (! $resolved) {
            return false;
        }

        [$sourceFileId] = $resolved;

        $segments = explode('/', trim($to, '/'));
        $filename = array_pop($segments);
        $parentId = $this->ensurePath($segments);

        $file = new DriveFile([
            'name' => $filename,
            'parents' => [$parentId],
        ]);

        $this->drive->files->copy($sourceFileId, $file, [
            'supportsAllDrives' => true,
        ]);

        return true;
    }

    public function move(string $from, string $to): bool
    {
        $resolved = $this->resolvePath($from);
        if (! $resolved) {
            return false;
        }

        [$fileId] = $resolved;

        $segments = explode('/', trim($to, '/'));
        $filename = array_pop($segments);
        $newParentId = $this->ensurePath($segments);

        $file = $this->drive->files->get($fileId, [
            'fields' => 'parents',
            'supportsAllDrives' => true,
        ]);

        $oldParents = implode(',', $file->parents ?? []);

        $this->drive->files->update(
            $fileId,
            new DriveFile([
                'name' => $filename,
            ]),
            [
                'addParents' => $newParentId,
                'removeParents' => $oldParents,
                'supportsAllDrives' => true,
            ]
        );

        return true;
    }

    public function url(string $path): string
    {
        $resolved = $this->resolvePath($path);

        if (! $resolved || $resolved['type'] !== 'file') {
            throw new \RuntimeException("File not found: {$path}");
        }

        return "https://drive.google.com/file/d/{$resolved['id']}/view";
    }

    public function download(string $path, ?string $name = null)
    {
        $resolved = $this->resolvePath($path);

        if (! $resolved || $resolved['type'] !== 'file') {
            abort(404, 'File not found');
        }

        $response = $this->drive->files->get($resolved['id'], [
            'alt' => 'media',
            'supportsAllDrives' => true,
        ]);

        return response()->streamDownload(
            fn () => print ($response->getBody()->getContents()),
            $name ?? basename($path)
        );
    }

    public function readStream(string $path)
    {
        [$fileId] = $this->resolvePath($path);

        $response = $this->drive->files->get($fileId, [
            'alt' => 'media',
            'supportsAllDrives' => true,
        ]);

        return $response->getBody()->detach();
    }

    public function makeDirectory(string $path): bool
    {
        $segments = array_filter(explode('/', trim($path, '/')));

        if (empty($segments)) {
            return true;
        }

        $this->ensurePath($segments);

        return true;
    }

    public function deleteDirectory(string $path): bool
    {
        $resolved = $this->resolvePath($path);

        if (! $resolved) {
            return true;
        }

        [$folderId] = $resolved;

        $this->deleteRecursively($folderId);

        $this->drive->files->delete($folderId, [
            'supportsAllDrives' => true,
        ]);

        return true;
    }

    protected function getDriveId(): string
    {
        static $driveId = null;

        if ($driveId) {
            return $driveId;
        }

        $file = $this->drive->files->get($this->rootFolderId, [
            'fields' => 'driveId',
            'supportsAllDrives' => true,
        ]);

        return $driveId = $file->driveId;
    }

    protected function deleteRecursively(string $folderId): void
    {
        do {
            $response = $this->drive->files->listFiles([
                'q' => "'{$folderId}' in parents and trashed = false",
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true,
                'corpora' => 'drive',
                'driveId' => $this->getDriveId(),
                'fields' => 'nextPageToken, files(id, mimeType)',
            ]);

            foreach ($response->files as $item) {
                if ($item->mimeType === 'application/vnd.google-apps.folder') {
                    $this->deleteRecursively($item->id);
                }

                $this->drive->files->delete($item->id, [
                    'supportsAllDrives' => true,
                ]);
            }

            $pageToken = $response->nextPageToken ?? null;

        } while ($pageToken);
    }

    public function lastModified(string $path): int
    {
        [$fileId] = $this->resolvePath($path);

        $file = $this->drive->files->get($fileId, [
            'fields' => 'modifiedTime',
            'supportsAllDrives' => true,
        ]);

        return strtotime($file->modifiedTime);
    }

    public function mimeType(string $path): string
    {
        [$fileId] = $this->resolvePath($path);

        $file = $this->drive->files->get($fileId, [
            'fields' => 'mimeType',
            'supportsAllDrives' => true,
        ]);

        return $file->mimeType ?? 'application/octet-stream';
    }

    public function temporaryUrl(string $path, $expiration, array $options = []): string
    {
        $expires = $expiration instanceof Carbon
            ? $expiration->timestamp
            : Carbon::parse($expiration)->timestamp;

        return URL::temporarySignedRoute(
            'gdrive-storage.download',
            $expires,
            [
                'path' => base64_encode($path),
                'name' => $options['name'] ?? null,
            ]
        );
    }
}
