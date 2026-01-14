# API Reference

HashtagCMS provides a robust REST API for Headless implementations (React, Vue, Mobile Apps).

## Overview
-   **Base URL**: `https://your-domain.com`
-   **Authentication**:
    -   **Public APIs**: Require `api_secret` (via query param or header).
    -   **User APIs**: Require Bearer Token (Sanctum).

### Common Headers
Most public endpoints accept these context headers (or query params):
-   `x-site` (or `site`): Site Context (e.g., `web`).
-   `x-lang` (or `lang`): Language Code (e.g., `en`).
-   `x-platform` (or `platform`): Platform Key (e.g., `web`).
-   `x-api-secret` (or `api_secret`): **Required**. Must match the secret defined in `config/hashtagcms.api_secrets.{site_context}`.

---

## Public Content

### `GET /api/hashtagcms/public/sites/v1/load-data`
Fetches the complete content structure for a specific category/page.

**Parameters**:
-   `category`: (Required) Category link rewrite or ID.
-   `microsite`: (Optional) Microsite ID.
-   *(Plus Common Headers)*

**Response**:
```json
{
    "meta": { "title": "Home", "keywords": "..." },
    "layout": { "name": "basic", "body_content": "..." },
    "zones": [...],
    "modules": [...],
    "html": "<body>...</body>" // it has all the hooks and theme
}
```

### `GET /api/hashtagcms/public/sites/v1/load-data-mobile`
Same as `/load-data`, but excludes the `html` field to reduce payload size.

### `GET /api/hashtagcms/public/sites/v1/blog/latests`
Fetches a list of the latest blog posts.

**Parameters**:
-   `category`: (Optional) Category slug (default: 'blog').
-   `limit`: (Optional) Number of items (default: 10).
-   *(Plus Common Headers)*

**Response**:
```json
{
    "data": [
        { "id": 1, "title": "Post Title", ... }
    ]
}
```

---

## Configuration

### `GET /api/hashtagcms/public/configs/v1/site-configs`
Returns global site settings required for app initialization.
-   **Response**: Languages, Currencies, Zones, Country list, etc.

---

## User Authentication

### `POST /api/hashtagcms/public/user/v1/login`
**Body**:
```json
{
    "email": "user@res.com",
    "password": "password"
}
```
**Response**:
```json
{
    "user": { ... },
    "token": {
        "access_token": "...",
        "expires_at": "..."
    }
}
```

### `POST /api/hashtagcms/public/user/v1/register`
**Body**:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password"
}
```

---

## Protected User Routes
Requires Header: `Authorization: Bearer <access_token>`

### `GET /api/hashtagcms/user/v1/me`
Returns the authenticated user's profile data.

---

### `POST /api/hashtagcms/user/v1/profile`
Updates the authenticated user's profile information.

**Authentication**: Bearer Token Required

**Body**:
```json
{
    "name": "Jane Doe",
    "mobile": "+1234567890",
    "father_name": "John Doe Sr.",
    "gender": "Male"
}
```
**Response**:
```json
{
    "message": "Profile updated successfully",
    "user": { ... }
}
```

---

### `POST /api/hashtagcms/public/kpi/v1/publish`
Publishes user analytics data (visits/views).

**Authentication**: Public (Server-side validation)

**Body**:
```json
{
    "site": "htcms",
    "categoryId": 1,
    "pageId": 10
}
```
**Response**: `{"success": true}`

---

## System

### `GET /api/hashtagcms/health-check`
**Response**: `{"result": "okay"}`
