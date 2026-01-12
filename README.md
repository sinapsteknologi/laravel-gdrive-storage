# Laravel Google Drive Storage

A Laravel filesystem disk for Google Drive using Service Account,
with full support for Shared Drives and folder-based isolation.

## Installation

```bash
composer require sinapsteknologi/laravel-gdrive-storage
```

---

### Configuration

Publish config:

```bash
php artisan vendor:publish --tag=gdrive-storage-config
```

Set environment variables:
```md
GDRIVE_SERVICE_ACCOUNT_PATH=/absolute/path/service-account.json
GOOGLE_DRIVE_SHARED_FOLDER_ID=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

---

### Filesystem Disk

```php
'gdrive' => [
    'driver' => 'gdrive',
]
```

---

### Filesystem Disk

```php
'gdrive' => [
    'driver' => 'gdrive',
]
```

---

### Usage (Parity Promise)

This package guarantees compatibility with Laravel's
`Storage::disk()` API.

### Upload

```php
Storage::disk('gdrive')->put('docs/readme.txt', 'Hello');
```

### Download

```php
return Storage::disk('gdrive')->download(
    'docs/readme.txt',
    'manual.txt'
);
```

```php
$url = Storage::disk('gdrive')->temporaryUrl(
    'docs/readme.txt',
    now()->addMinutes(5)
);
```

---

### Unsupported Methods

The following methods are intentionally unsupported
because Google Drive does not expose equivalent semantics:

- path()
- visibility()
- setVisibility()
- checksum()

## Design Philosophy

- No Flysystem adapter dependency
- Native Google Drive API
- Shared Drive first-class support
- Zero refactor guarantee
