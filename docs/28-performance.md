# Performance Optimization

## 1. Module Caching (Critical)
HashtagCMS allows you to cache individual modules to drastically reduce database queries.

**Where is it?**
Go to **Admin > Site > Site Settings (Module Assignment)**. When you assign a module to a Category/Hook, you will see a field for **Cache Group** (or Cache).

**When to use it?**
-   **✅ Cache It**: For content that looks the same for everyone (e.g., **Footer**, **Header**, **Hero Banner**, **Static HTML**). Use a group like `global` or `static`.
-   **❌ Don't Cache**: For content that changes per user or per second (e.g., **Cart**, **User Profile**, **Randomized Lists**).

**Why?**
If a module is cached, the CMS skips the database queries and PHP logic for that module and serves the pre-rendered HTML instantly.
For full-page API caching details, see [38-caching.md](./38-caching.md).

## 2. CDN Configuration
To serve assets and media from a CDN:
1.  Open `config/hashtagcms.php`.
2.  Update `media.http_path` for uploaded files.
3.  Update `info.assets_path.base_url` for theme assets (CSS/JS/Images).
