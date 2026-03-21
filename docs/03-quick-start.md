# Quick Start Guide

This guide will help you get started with HashtagCMS quickly by walking through common tasks.

## First Login

After installation, access the admin panel:

```
http://your-app-url/admin
```

Use the credentials you created during installation.

## Understanding the Dashboard

The admin dashboard provides:

- **Quick Stats**: Overview of content, users, and activity
- **Recent Activity**: Latest changes and updates
- **Quick Actions**: Common tasks and shortcuts
- **System Status**: Health checks and notifications

## Creating Your First Content

### Step 1: Create a Category

Categories organize your content and define URL structure.

1. Navigate to **Admin → Category**
2. Click **Add New Category**
3. Fill in the details:
   - **Name**: Your category name (e.g., "Blog")
   - **Link Rewrite**: URL-friendly slug (e.g., "blog")
   - **Parent Category**: Select if this is a sub-category
   - **Publish Status**: Set to "Published"
4. Click **Save**

### Step 2: Assign a Theme to Category

1. In the category edit page, choose the **Platform** if you have more than one platforms enabled. 
2. Choose a theme
3. Set the position if needed(order)
4. Click **Save**

### Or Category listing page, choose settings from the action bar

### Step 3: Create a Module

Modules display content within categories.

1. Navigate to **Admin → Module**
2. Click **Add New Module**
3. Configure the module:
   - **Name**: Module name (e.g., "Blog List")
   - **Alias**: Unique identifier (e.g., "blog-list")
   - **Data Type**: Choose module type:
     - **Static**: Content from CMS
     - **Query**: Database query
     - **Service**: External API
     - **Custom**: Custom logic
   - **View Name**: Blade template name (you need to create view manually)
4. Click **Save**

### Step 4: Assign Module to Category

1. Navigate to **Admin → Page Manager** (or Module Assignment)
2. Select your **Site**, **Platform**, and **Category**
3. Click **Add Module**
4. Select the module you created
5. Set the position
6. Click **Save**

### Step 5: Create Content (Page/Blog)

1. Navigate to **Admin → Page** or **Admin → Blog**
2. Click **Add New**
3. Fill in the content:
   - **Title**: Content title
   - **Link Rewrite**: URL slug
   - **Category**: Select category
   - **Content**: Your content (supports HTML)
   - **Meta Description**: SEO description
   - **Publish Status**: Set to "Published"
4. Click **Save**

### Step 6: View Your Content

Visit your frontend:
```
http://your-app-url/blog
```

## Common Tasks

### Adding a New Language

1. Navigate to **Admin → Language**
2. Click **Add New Language**
3. Fill in details:
   - **Name**: Language name (e.g., "Spanish")
   - **Code**: ISO code (e.g., "es")
   - **Locale**: Locale code (e.g., "es_ES")
   - **Active**: Yes
4. Click **Save**
5. Navigate to **Admin → Site**
6. Edit your site and assign the new language

### Creating a New User

1. Navigate to **Admin → Author** (Users)
2. Click **Add New User**
3. Fill in user details:
   - **Name**: User's full name
   - **Email**: User's email
   - **Password**: Secure password
   - **Role**: Select user role
4. Click **Save**

### Managing Roles & Permissions

1. Navigate to **Admin → Role**
2. Click **Add New Role** or edit existing
3. Set role name and description
4. Navigate to **Admin → Rolesright**
5. Assign permissions to the role

### Uploading Media

1. Navigate to **Admin → Gallery**
2. Click **Upload**
3. Select files to upload
4. Add tags and descriptions
5. Click **Save**

### Creating a Gallery

1. Navigate to **Admin → Gallery**
2. Click **Create Gallery**
3. Name your gallery
4. Add images to the gallery
5. Assign gallery to pages or categories

## Working with Themes

### Understanding Theme Structure

Themes are located in:
```
resources/views/vendor/hashtagcms/fe/{theme-name}/
```

Basic theme structure:
```
fe/
└── default/
    ├── index.blade.php          # Main layout
    ├── header.blade.php         # Header section
    ├── footer.blade.php         # Footer section
    ├── modules/                 # Module templates
    │   ├── blog-list.blade.php
    │   └── content.blade.php
    └── pages/                   # Page templates
        └── detail.blade.php
```

### Customizing a Theme

1. Publish views:
```bash
php artisan vendor:publish --tag=hashtagcms.views.frontend
```

2. Edit theme files in:
```
resources/views/vendor/hashtagcms/fe/default/
```

3. Clear view cache:
```bash
php artisan view:clear
```

## Using the API

### Getting Site Configuration

```bash
curl -X GET "http://your-app-url/api/hashtagcms/public/configs/v1/site-configs?site=htcms&api_secret=your_api_secret"
```

### Loading Page Data

```bash
curl -X GET "http://your-app-url/api/hashtagcms/public/sites/v1/load-data?site=htcms&link_rewrite=blog&api_secret=your_api_secret"
```

### User Registration

```bash
curl -X POST "http://your-app-url/api/hashtagcms/public/user/v1/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secure_password"
  }'
```

### User Login

```bash
curl -X POST "http://your-app-url/api/hashtagcms/public/user/v1/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "secure_password"
  }'
```

## Multi-Site Setup

### Creating a Second Site

1. Navigate to **Admin → Site**
2. Click **Add New Site**
3. Configure site:
   - **Name**: Site name
   - **Domain**: Domain name
   - **Context**: Unique context key
   - **Active**: Yes
4. Click **Save**

# you can clone site using copy button from the action bar after creating a site

### Configure Domain Mapping without new installation

Edit `config/hashtagcms.php`:

```php
'domains' => [
    'site1.com' => 'context',
    'site2.com' => 'context',
],

'api_secrets' => [
    'context' => 'secret_key_1',
],
```

## Console Commands

### Export Data (not for mongo)

```bash
php artisan cms:exportdata
```

### Import Data

```bash
php artisan cms:importdata (not for mongo)
```

### Check Version

```bash
php artisan cms:version
```

### Generate Module Controller

```bash
php artisan cms:module-controller ModuleName
```

### Generate Module Model

```bash
php artisan cms:module-model ModelName
```

## Blade Helpers in Templates

These helpers are available in your Blade views. First retrieve the Layout Manager instance, then call its methods:

```blade
@php
    $layoutManager = app('HashtagCmsLayoutManager');
@endphp
```

### Get Header Menu

```blade
{!! $layoutManager->getHeaderContent() !!}
```

### Get Body Content

```blade
{!! $layoutManager->getBodyContent() !!}
```

### Get Footer Content

```blade
{!! $layoutManager->getFooterContent() !!}
```

### Get Meta Tags

```blade
{!! $layoutManager->getMetaContent() !!}
```

### Get Page Title

```blade
<title>{!! $layoutManager->getTitle() !!}</title>
```


## Layout Manager in Views

All rendering in HashtagCMS themes goes through the **Layout Manager**, retrieved via:

```blade
@php
    $layoutManager = app('HashtagCmsLayoutManager');
@endphp
```

This is typically done once at the top of your `index.blade.php` layout file.

### Common Layout Manager Methods

```blade
{{-- Page title --}}
<title>{!! $layoutManager->getTitle() !!}</title>

{{-- Head: CSS links, meta tags etc. --}}
{!! $layoutManager->getHeaderContent() !!}

{{-- All SEO meta tags (title, description, OG, etc.) --}}
{!! $layoutManager->getMetaContent() !!}

{{-- Rendered CSS stack (collected via @push('styles')) --}}
{!! $layoutManager->renderStack('styles') !!}

{{-- All body/module content --}}
{!! $layoutManager->getBodyContent() !!}

{{-- Footer scripts / content --}}
{!! $layoutManager->getFooterContent() !!}

{{-- Rendered JS stack (collected via @push('scripts')) --}}
{!! $layoutManager->renderStack('scripts') !!}
```

### Additional Helpers

```blade
{{-- Festival/event CSS class for <body> --}}
@php $bodyCss = $layoutManager->getFestivalCss(); @endphp
<body class="{!! $bodyCss !!}">

{{-- Body background image (if configured) --}}
@php $bodyBg = $layoutManager->getBodyBackgroundImage(); @endphp

{{-- RTL direction for <html> tag --}}
<html dir="{{ $layoutManager->isRtl() }}">

{{-- Current theme view folder (for @include paths) --}}
@php $themeFolder = $layoutManager->getViewThemeFolder(); @endphp
```

### Push to a Stack

```blade
@push('scripts')
    <script src="/js/custom.js"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="/css/custom.css">
@endpush
```

## Configuration Tips

### Set Default Language

Edit `.env`:
```env
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

### Set Timezone

Edit `.env`:
```env
APP_TIMEZONE=UTC
```

### Configure Media Path

Edit `config/hashtagcms.php`:
```php
'media' => [
    'upload_path' => 'public/media',
    'http_path' => '/storage/media',
],
```

### Set Records Per Page

Edit `config/hashtagcmsadmin.php`:
```php
'cmsInfo' => [
    'records_per_page' => 20,
],
```

## Best Practices

### 1. URL Structure

Use SEO-friendly URLs:
- Good: `/blog/my-first-post`
- Bad: `/page?id=123`

### 2. Category Organization

Plan your category structure:
```
Home
├── Blog
│   ├── Technology
│   └── Lifestyle
├── About
└── Contact
```

### 3. Module Naming

Use descriptive, unique aliases:
- Good: `blog-list`, `featured-posts`, `contact-form`
- Bad: `module1`, `test`, `new`

### 4. Content Management

- Use drafts for work in progress
- Set proper meta descriptions for SEO
- Use categories to organize content
- Tag content for better discovery

### 5. Performance

- Enable caching in production
- Optimize images before upload
- Use CDN for static assets
- Monitor query performance

## Next Steps

Now that you're familiar with the basics:

- [Architecture Overview](04-architecture.md) - Understand the system
- [Modules](10-modules.md) - Deep dive into modules
- [API & Headless CMS](13-api-headless.md) - Use as headless CMS
- [Custom Modules](14-custom-modules.md) - Create custom functionality

## Quick Reference

### Admin URLs

- Dashboard: `/admin`
- Categories: `/admin/category`
- Pages: `/admin/page`
- Modules: `/admin/module`
- Users: `/admin/author`
- Settings: `/admin/site`

### API Endpoints

- Health Check: `/api/hashtagcms/health-check`
- Site Config: `/api/hashtagcms/public/configs/v1/site-configs`
- Load Data: `/api/hashtagcms/public/sites/v1/load-data`
- Register: `/api/hashtagcms/public/user/v1/register`
- Login: `/api/hashtagcms/public/user/v1/login`

### Important Files

- Main Config: `config/hashtagcms.php`
- Admin Config: `config/hashtagcmsadmin.php`
- Common Config: `config/hashtagcmscommon.php`
- Routes: `vendor/hashtagcms/hashtagcms/src/routes/`
- Views: `resources/views/vendor/hashtagcms/`

## Getting Help

- [FAQ](33-faq.md) - Common questions
- [Troubleshooting](29-troubleshooting.md) - Problem solving
- [API Reference](30-api-reference.md) - Complete API docs
