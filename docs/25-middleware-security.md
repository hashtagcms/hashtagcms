# Middleware & Security

## Core Middleware
HashtagCMS ships with critical middleware in `src/Http/Middleware`.

### `Interceptor`
The most important one. It:
1.  Detects current **Site** from domain.
2.  Detects **Language** from URL/Header.
3.  Detects **Platform**.
4.  Binds these to the Service Container.

### `AdminMiddleware`
Ensures the user has the right permissions to access backend routes.



## CSRF
Standard Laravel CSRF protection is active for Web routes.
-   **API Requests**: Must perform Stateless Auth (Sanctum), bypassing CSRF.

## XSS & Sanitization
The helper `sanitize()` is available to clean user input (removes `script` tags).
Rich Text content (WYSIWYG) is stored as raw HTML, so outputting it requires `{!! $content !!}`. ensure you trust your content editors!
