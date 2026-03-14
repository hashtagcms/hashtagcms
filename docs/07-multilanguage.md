# Multi-Language & Localization

HashtagCMS treats language as a first-class citizen. Almost every primary database table (Categories, Pages, Sites) has a corresponding `_langs` table (e.g., `category_langs`).

## Supported Features
- **URL Prefixes**: `/en/about`, `/es/about-us`.
- **Domain-based Language**: `es.example.com` (requires custom routing config).
- **RTL Support**: Built-in flags for Right-To-Left languages (Arabic, Hebrew).

## Setup
1.  **Admin Panel**: Go to **Languages**.
2.  **Add New**:
    - Name: "Spanish"
    - Code: `es`
    - ISO Code: `es_ES`
    - Direction: LTR
3.  **Assign to Site**: Go to **Sites**, edit your site, and check "Spanish".

## Content Translation
When you edit a Page or Category:
- You will see tabs for each active language (e.g., "English", "Spanish").
- You **must** populate the "Link Rewrite" (slug) for each language.
  - EN: `about-us`
  - ES: `about-us`

## Developer Usage

### Getting Current Language
```php
$lang = htcms_get_language(); // Returns array with id, code, iso...
$code = htcms_get_language_code(); // 'en'
```

### Translation Helper
Use `htcms_trans()` instead of Laravel's `trans()` to leverage CMS-managed translations (if configured), though standard Laravel language files are also fully supported.

```php
// In Blade
{{ htcms_trans('messages.welcome') }}
```

### Switching Languages
The API automatically handles content selection based on the requested language.
```
GET /api/v1/load-data?lang=es
```
Returns all titles, content, and module data in Spanish. If a translation is missing, it falls back to the site's default language.
