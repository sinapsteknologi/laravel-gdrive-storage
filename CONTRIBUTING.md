# Contributing Guide

Thank you for considering contributing to **Laravel Google Drive Storage**.

This project aims to provide a **stable, Laravel-first Google Drive filesystem implementation** with clean architecture, predictable behavior, and production-ready defaults.

We welcome contributions in the form of:
- Bug reports
- Bug fixes
- Documentation improvements
- New features (with prior discussion)

---

## Code of Conduct

Be respectful, professional, and constructive.

This project follows common open-source etiquette:
- No harassment or abusive language
- Assume good intent
- Focus on technical merit

---

## Getting Started

### Requirements

- PHP >= 8.1
- Laravel >= 10
- Composer
- Google Drive API enabled

---

## Development Setup

### 1. Fork & Clone

Fork the repository, then clone your fork:

```bash
git clone https://github.com/your-username/laravel-gdrive-storage.git
cd laravel-gdrive-storage
```

---

### 2. Install Dependencies

```bash
composer install
```

---

### 3. Local Testing

This package is best tested inside a Laravel application.

You may:
- Use a **local Laravel test app**, or
- Use the package via a **path repository** in `composer.json`

Example:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../laravel-gdrive-storage"
    }
  ]
}
```

---

## Branching Strategy

- `main` → stable, production-ready code
- `develop` → active development (if present)
- Feature branches:

```text
feature/<short-description>
fix/<short-description>
```

Examples:
- `feature/shared-drive-support`
- `fix/files-listing-bug`

---

## Coding Standards

- Follow **PSR-12** coding standards
- Use **typed properties and return types** where possible
- Avoid breaking Laravel filesystem contracts
- Prefer clarity over cleverness

---

## Commit Message Guidelines

Use clear, descriptive commit messages:

```text
feat: add shared drive root support
fix: prevent empty fileId request
refactor: simplify path resolver logic
```

---

## Pull Request Process

1. Create a feature or fix branch
2. Make your changes
3. Ensure code is formatted and readable
4. Update documentation if necessary
5. Submit a Pull Request to `main`

### Pull Request Checklist

- [ ] Code compiles without errors
- [ ] No breaking changes (or clearly documented)
- [ ] Documentation updated (if applicable)
- [ ] Changes are scoped and focused

---

## Reporting Bugs

When reporting a bug, please include:

- Laravel version
- PHP version
- Package version
- Error message / stack trace
- Minimal reproduction steps

Incomplete reports may be closed.

---

## Feature Requests

Feature requests are welcome, but please:

- Open an issue **before** submitting a PR
- Clearly explain the use case
- Avoid large architectural changes without discussion

---

## Security Issues

If you discover a security vulnerability:

- **Do not** open a public issue
- Contact the maintainer directly

---

## License

By contributing, you agree that your contributions will be licensed under the **MIT License**.

