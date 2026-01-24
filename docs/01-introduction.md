# Introduction to HashtagCMS

## What is HashtagCMS?

HashtagCMS is a powerful, flexible, and modern Content Management System built on the Laravel framework. Far from being "just another CMS", it is a **Headless-First**, **Module-Based** platform designed for scalability and multi-tenancy.

It decouples content management (Admin) from presentation (Frontend), enabling you to work both as a traditional CMS using standard Laravel Blade or as a headless CMS powering mobile applications and modern JavaScript frameworks (React, Vue, etc.) via its robust API. This makes it perfect for complex, multi-platform projects that demand flexibility.

## Key Features

### 🌐 Multi-Everything Architecture

#### Multi-Site (Multi-Tenancy)
- Manage multiple websites from a single installation
- Each site can have its own configuration, content, categories, and modules etc
- Shared database tables per site
- Perfect for managing multiple brands or client websites

#### Multi-Platform
- Support for web, mobile (iOS/Android), and custom platforms
- Platform-specific content and layouts
- Different themes per category (/about-us)
- API-first design for mobile apps (paltform wise)

#### Multi-Language
- Built-in internationalization (i18n) support
- Manage content in multiple languages
- Language-specific URLs and SEO
- RTL (Right-to-Left) language support

### 📱 Headless CMS Capabilities

- **RESTful API**: Complete API for frontend operations
- **Mobile-Ready**: Optimized endpoints for mobile applications
- **Flexible Content Delivery**: JSON responses for any frontend framework
- **Authentication**: Token-based authentication with Laravel Sanctum
- **API Versioning**: Built-in API versioning support

### 🎨 Content Management

#### Categories
- Hierarchical category structure
- Platform and site-specific categories
- Custom category properties
- SEO-friendly URLs with link rewriting

#### Pages & Blog Posts
- Rich content editor support
- Draft and publish workflow
- Scheduled publishing
- Read count tracking
- Gallery attachments

#### Modules
- **Static Modules**: Content from CMS database (table: static_module_contents)
- **Query Modules**: Execute custom database queries
- **Service Modules**: Fetch data from external APIs
- **Custom Modules**: Your own module logic
- **QueryService**: Combine query and service data
- **UrlService**: Dynamic service calls with parameters

### 🎭 Themes & Templates

- Blade template engine
- Theme inheritance
- Platform-specific themes
- Asset management (CSS, JS, images)
- Layout manager with sections
- View composers for shared data

### 👥 User Management

- Role-based access control (RBAC)
- Fine-grained permissions
- User profiles
- Site-specific user access
- Admin panel with full CRUD operations

### 🔌 Extensibility

- **Hooks System**: Event-driven architecture
- **Middleware**: Custom request processing
- **Service Providers**: Extend core functionality
- **Traits**: Reusable functionality
- **Console Commands**: Custom artisan commands

### 🔒 Security Features

- Laravel Sanctum authentication
- CSRF protection
- XSS prevention
- SQL injection protection
- Role-based authorization
- Middleware-based security

### 📊 Advanced Features

- **Query Logger**: Track and optimize database queries
- **CMS Logs**: Audit trail for all operations
- **Analytics**: Built-in analytics support
- **Comments**: Comment system for content
- **Subscribers**: Newsletter subscription management
- **Contacts**: Contact form management
- **Galleries**: Image gallery management with tags

## Premium Features

HashtagCMS offers premium features through a flexible licensing system:

### 🎯 License Tiers

| Tier | Features | Max Users |
|------|----------|-----------|
| **Free** | Core CMS functionality | 5 |
| **Starter** | + MongoDB Support | 100 |
| **Enterprise** | + SSO Login, Figma Integration | Unlimited |

### Premium Capabilities

1. **MongoDB Support** (Starter+)
   - Use MongoDB as your database backend
   - NoSQL flexibility for content storage
   - Better performance for large datasets

2. **SSO Login** (Enterprise)
   - Single Sign-On with SAML
   - OAuth integration
   - Enterprise authentication
3. **Figma Integration** (Enterprise)
   - Import designs directly from Figma
   - Design-to-CMS workflow
   - Automated component generation

## Architecture Highlights

### MVC Pattern
- **Models**: Eloquent ORM with relationships
- **Views**: Blade templates with layouts
- **Controllers**: RESTful controllers for admin and frontend

### Service-Oriented Architecture
- **InfoLoader**: Loads site and context information
- **LayoutManager**: Manages page layouts and sections
- **ModuleLoader**: Loads and processes modules
- **DataLoader**: Fetches and processes data

### Database Design
- **Normalized Structure**: Efficient relational design
- **Pivot Tables**: Many-to-many relationships
- **Language Tables**: Separate tables for translations
- **Site Scopes**: Global scopes for multi-tenancy

## Use Cases

### Traditional Website
- Corporate websites
- Blogs and news sites
- E-commerce content management
- Portfolio sites

### Headless CMS
- Mobile applications (iOS/Android)
- Single Page Applications (React, Vue, Angular)
- Static site generators (Next.js, Gatsby)
- IoT devices and digital signage

### Multi-Site Platform
- Agency managing multiple client sites
- Multi-brand corporate websites
- SaaS platforms with customer sites
- Educational institutions with department sites

### Multi-Platform Content
- Website + mobile app with shared content
- Different content for different devices
- Platform-specific features and layouts
- Unified content management

## Technology Stack

- **Framework**: Laravel 10+
- **PHP**: 8.2+
- **Database**: MySQL/MariaDB (MongoDB with premium license)
- **Authentication**: Laravel Sanctum
- **Frontend**: Blade templates, Vue.js support
- **API**: RESTful JSON API
- **Assets**: Webpack for compilation

## What Makes HashtagCMS Different?

1. **True Multi-Tenancy**: Not just multi-site, but true isolation with shared infrastructure
2. **Platform Agnostic**: Built for web, mobile, and any platform from day one
3. **Flexible Module System**: Six different module types for any use case
4. **API-First Design**: Every feature available via API
5. **Laravel Foundation**: Built on a solid, modern framework
6. **Production Ready**: Used in real-world applications
7. **Developer Friendly**: Clean code, well-documented, extensible

## Next Steps

- [Installation Guide](02-installation.md) - Install HashtagCMS
- [Quick Start](03-quick-start.md) - Get started quickly
- [Architecture Overview](04-architecture.md) - Understand the system

## Version Information

**Current Version**: 1.6.0  
**Release Date**: November 2025  
**Minimum PHP**: 8.2  
**Minimum Laravel**: 10.0

## Support & Community

- **GitHub**: https://github.com/hashtagcms/hashtagcms
- **Packagist**: https://packagist.org/packages/hashtagcms/hashtagcms
- **Author**: Marghoob Suleman
- **Email**: marghoobsuleman@gmail.com
- **Website**: https://www.marghoobsuleman.com
