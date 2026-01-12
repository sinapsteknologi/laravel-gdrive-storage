# Laravel Google Drive Storage

A Laravel filesystem disk implementation for Google Drive using **Google Service Accounts**.
Designed for backend storage, media distribution, and controlled file access.

---

## Why use this package
Unlike Flysystem adapters, this package is designed for
Laravel-first usage with signed temporary URLs, Shared Drive support,
and minimal refactoring from existing Storage-based code.

## Requirements

- PHP >= 8.1
- Laravel >= 10
- Google Drive API enabled

## Installation

### 1. Create Google Service Account & Download Credentials

This package uses **Google Service Account authentication**, not OAuth user login.

#### a. Open Google Cloud Console

* [https://console.cloud.google.com/](https://console.cloud.google.com/)
* Select or create a **Project**

#### b. Enable Google Drive API

* Navigate to **APIs & Services → Library**
* Search for **Google Drive API**
* Click **Enable**

#### c. Create Service Account

* Go to **APIs & Services → Credentials**
* Click **Create Credentials → Service Account**
* Fill in:

  * Service account name (e.g. `laravel-gdrive-storage`)
* Click **Done**

#### d. Generate JSON Key

* Open the created service account
* Go to **Keys** tab
* Click **Add Key → Create new key**
* Select **JSON**
* A file will be downloaded:

```
service-account.json
```

### Security Notes

- Never commit `service-account.json` to version control
- Store credentials outside the public directory
- Rotate service account keys periodically

---

### 2. Share Target Google Drive Folder

This package **does not operate on the root Drive**.
You must use a **specific folder**. This folder may exist in **My Drive or a Shared Drive**.


1. Open Google Drive
2. Create a folder (example: `Laravel Storage`)
3. Right-click → **Share**
4. Add the service account email
   (format: `xxxx@xxxx.iam.gserviceaccount.com`)
5. Permission: **Editor**
6. Copy the **Folder ID**

Example URL:

```
https://drive.google.com/drive/u/2/folders/1AbCDefGhIJkLmNop 
                                            ↑
                                        folder_id
```

---

### 3. Install Package via Composer

```bash
composer require sinapsteknologi/laravel-gdrive-storage
```

---

### 4. Publish Configuration

```bash
php artisan vendor:publish --tag=gdrive-storage-config
```

This will create:

```bash
config/gdrive-storage.php
```

---

### 5. Configure Environment Variables

By default, the package looks for the service account file at:

`storage/app/google/service-account.json`

If you want to use a different location, specify the path in your `.env` file.


Example:

Place the `service-account.json` file in `storage/service-account.json`, then add the path information to your `.env` file.

```env
GDRIVE_SERVICE_ACCOUNT_PATH=storage/service-account.json
```

Then add the ```folder_id``` information to your `.env` file:

```env
GOOGLE_DRIVE_SHARED_FOLDER_ID=1AbCDefGhIJkLmNop
```

---

### 6. Configure Filesystem Disk

Add the disk configuration to `config/filesystems.php`:

```php
'disks' => [

    'gdrive' => [
        'driver' => 'gdrive',
    ],

],
```

---

### 7. Basic Usage

```php
use Illuminate\Support\Facades\Storage;

Storage::disk('gdrive')->put('hello.txt', 'Hello Google Drive');

Storage::disk('gdrive')->files();

Storage::disk('gdrive')->exists('hello.txt');
```

---

### 8. Temporary Download URL

```php
$url = Storage::disk('gdrive')->temporaryUrl(
    'hello.txt',
    now()->addMinutes(5)
);
```

The generated URL is signed and expires automatically. 
The URL points to a signed Laravel route and will return a streamed response
from Google Drive when accessed.


---
## Available Filesystem Methods

The `gdrive` disk is designed to be **API-compatible with Laravel's filesystem**, so it can be used as a drop-in replacement for other cloud disks (such as S3 or Dropbox) with minimal or no code changes.

| Method | Description |
|------|------------|
| `exists($path)` | Check whether a file or directory exists |
| `put($path, $contents)` | Create or overwrite a file |
| `putFileAs($directory, $file, $name)` | Upload a file with a specific name |
| `get($path)` | Get file contents |
| `size($path)` | Get file size in bytes |
| `files($directory = '')` | List files in a directory |
| `directories($directory = '')` | List subdirectories |
| `delete($path)` | Delete a file |
| `copy($from, $to)` | Copy a file |
| `move($from, $to)` | Move or rename a file |
| `makeDirectory($path)` | Create directories recursively |
| `deleteDirectory($path)` | Delete a directory recursively |
| `lastModified($path)` | Get last modified timestamp |
| `mimeType($path)` | Get MIME type |
| `url($path)` | Get Google Drive viewer URL |
| `download($path, $name = null)` | Stream file as HTTP download |
| `readStream($path)` | Get PHP stream resource |
| `temporaryUrl($path, $expiration, $options = [])` | Generate signed temporary download URL |

### Notes

- All paths are **relative to the configured root folder ID**
- Fully supports **Shared Drives**
> If the folder is located inside a **Shared Drive**, make sure the service account
> is added as a **member of the Shared Drive**, not only to the folder itself.
- Directory deletion is **recursive**
- `temporaryUrl()` does **not** require public file sharing; it uses Laravel signed routes



---

## Important Notes

* ✅ Uses **Google Drive API directly**
* ✅ Service Account based (no user OAuth)
* ✅ Suitable for:

  * Media storage
  * Backup
  * Controlled file distribution
* ❌ Not suitable for:

  * Realtime filesystem mounting
  * Extremely high write frequency workloads

---

## License

MIT
