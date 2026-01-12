# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added

* Google Drive filesystem disk for Laravel using Service Accounts
* Support for Shared Drives
* Signed temporary download URLs via Laravel signed routes
* Recursive directory creation and deletion
* Streamed file downloads

### Changed

* N/A

### Deprecated

* N/A

### Removed

* N/A

### Fixed

* N/A

### Security

* N/A

---

## [1.0.0] - 2026-01-12

### Added

* Initial stable release
* `gdrive` filesystem disk integration
* Service Account authentication
* Configurable root folder via `GOOGLE_DRIVE_SHARED_FOLDER_ID`
* Support for common filesystem operations:

  * put / get / delete
  * files / directories
  * copy / move
  * makeDirectory / deleteDirectory
  * size / mimeType / lastModified
  * temporaryUrl / download / readStream

### Notes

* All paths are relative to the configured root folder
* Does not require public file sharing
* Designed for backend storage and controlled distribution

---

[Unreleased]: https://github.com/sinapsteknologi/laravel-gdrive-storage/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/sinapsteknologi/laravel-gdrive-storage/releases/tag/v1.0.0
