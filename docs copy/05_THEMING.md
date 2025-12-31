# Theming & Views

HashtagCMS theming is built on standard Laravel Blade, enhanced with the concept of **Zones**.

## Theme Structure

Themes are located in `resources/views/themes/{theme_name}/`.

Typical structure:
```
themes/basic/
├── layouts/
│   └── app.blade.php  (Main Master Layout)
├── partials/
│   ├── header.blade.php
│   └── footer.blade.php
├── modules/
│   ├── hero/
│   │   └── index.blade.php
│   └── blog/
│       └── list.blade.php
└── pages/
    └── home.blade.php (Optional, if overriding generic route)
```

## How Page Rendering Works

1.  **Skeleton**: The CMS loads the `skeleton` (Layout) defined for the Category (Page). E.g. `layouts.app`.
2.  **Zones**: Inside `app.blade.php`, you don't write content. You define **Zones**.

### Defining a Zone in Blade

```php
// In layouts/app.blade.php

<header>
    @include('hashtagcms::fe.zone', ['zone' => 'Header'])
</header>

<main>
    @include('hashtagcms::fe.zone', ['zone' => 'Content'])
</main>

<footer>
    @include('hashtagcms::fe.zone', ['zone' => 'Footer'])
</footer>
```

When this runs:
1.  The CMS looks up the current Zone ("Header").
2.  It finds all Modules assigned to "Header" for the current Page.
3.  It loops through them, renders their HTML, and injects it here.

## Creating a New Theme

1.  Create folder `resources/views/themes/my-theme`.
2.  Create `layouts/app.blade.php`.
3.  Register the theme in the table `themes` (or via Admin UI).
4.  Switch your Site to use `my-theme`.

## CMS Frontend Kit

For advanced asset management (SCSS/JS compilation), we recommend using **HashtagCMS Frontend Kit** (`@hashtagcms/frontend-kit`). It provides Webpack configurations tailored for multi-theme development.

See `cms-frontend-kit/docs` for details.
