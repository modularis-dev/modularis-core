# Contributing to Modularis

We’re thrilled that you want to contribute to **Modularis** — an open-source, self-hosted Discord moderation platform combining **Node.js (Discord.js)** with a **Laravel + Filament** backend.  
Your contributions help make Modularis more modular, reliable, and community-driven.

This repository contains the backend - a Laravel API, and the management interface using Filament.

---

## Table of Contents
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Branching Strategy](#branching-strategy)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)
- [Issue Reporting](#issue-reporting)
- [Style Guidelines](#style-guidelines)
- [License](#license)

---

## Getting Started

1. **Fork the repository** and **clone** your fork locally:
```
git clone https://github.com/your-username/modularis.git
cd modularis
```

2. **Add the upstream remote** to stay synced with the main project:
```
git remote add upstream https://github.com/modularis/modularis.git
```

3. Before coding, ensure your environment is working properly.

---

## Development Setup

A devcontainer is included, it contains the application, pgsql, valkey, typesense and mailpit.

---

## Branching Strategy

- **main** – production-ready code  
- **develop** – integration branch for new features  
- **feat/*** – for new features or enhancements  
- **fix/*** – for bug fixes  
- **docs/*** – for documentation updates

Laravel migrations are subject to change _until_ they reach the `main` branch.

Example:
```
git checkout -b feat/moderation-rules
```

---

## Commit Guidelines

We use [Conventional Commits](https://www.conventionalcommits.org/) for clarity and automation.

Format:
```
type(scope): short description
```

Examples:
- `feat: add kick command`
- `fix: resolve user role sync bug`
- `docs: improve setup instructions`

Common types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`

Review the `git-conventional-commits.yaml` for rules.

---

## Pull Request Process

1. Sync with upstream before submitting:
```
git fetch upstream
git rebase upstream/develop
```
2. Ensure your code passes linting/tests:
```
php artisan test
vendor/bin/pint
vendor/bin/phpstan
```
3. Open a Pull Request targeting the **develop** branch.
4. Include a clear description of your changes and any relevant issue numbers.
5. Engage in code review discussions until approved for merge.

---

## Issue Reporting

Before submitting a new issue:
- Search existing issues to avoid duplicates.
- Use clear, descriptive titles.
- Provide reproducible steps, expected behavior, actual behavior, and environment details.
- Use appropriate labels: `bug`, `enhancement`, `question`, etc.

---

## Style Guidelines

**PHP (Laravel):**
- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) standards.
- Run `./vendor/bin/pint` for formatting.

**UI (Filament + Blade):**
- Keep components simple and consistent with Filament best practices.
- Use Tailwind CSS utility-first principles.

---

## License

By contributing, you agree that your contributions will be licensed under the project’s [BSD 3-Clause License](LICENSE).

---

Thank you for helping make Modularis better!
