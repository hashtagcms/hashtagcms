# HashtagCMS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marghoobsuleman/hashtagcms.svg?style=flat-square)](https://packagist.org/packages/marghoobsuleman/hashtagcms)
[![Total Downloads](https://img.shields.io/packagist/dt/marghoobsuleman/hashtagcms.svg?style=flat-square)](https://packagist.org/packages/marghoobsuleman/hashtagcms)
[![License](https://img.shields.io/packagist/l/marghoobsuleman/hashtagcms.svg?style=flat-square)](https://packagist.org/packages/marghoobsuleman/hashtagcms)

HashtagCMS is a powerful, headless-ready, and module-based Content Management System built on Laravel. It separates the "Frontend/Headless" logic from the "Backend/Admin" logic, allowing you to manage multiple sites, platforms (Web, Mobile), and languages from a single installation.

## 🚀 Key Features

-   **Multi-Tenancy**: Manage multiple sites from one admin panel.
-   **Headless Ready**: Robust API for consuming content on React/Vue/Mobile.
-   **Everything is a Module**: drag-and-drop module placement for any part of the page.
-   **Smart Queries**: Fetch data from SQL or MongoDB using JSON configuration (no code needed).
-   **Premium Features**: MongoDB support, SSO, and Figma Integration.

## 📚 Documentation

We have comprehensive documentation available in the `docs/` directory.

-   [**Start Here: Documentation Index**](docs/00-index.md)
-   [Installation Guide](docs/02-installation.md)
-   [Quick Start](docs/03-quick-start.md)
-   [API Reference](docs/13-api-headless.md)
-   [Feature List](docs/features.md)

## ⚡ Fast Installation

```bash
composer create-project laravel/laravel mysite
cd mysite
composer require marghoobsuleman/hashtagcms
php artisan cms:install
```

After installation, visit:
-   **Frontend**: `http://your-domain.com`
-   **Admin**: `http://your-domain.com/admin` (Default: `admin@example.com` / `password`)

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## 📄 License

The core of HashtagCMS is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
Premium features (MongoDB, SSO) require a commercial license. See [Licensing](docs/18-licensing.md).
