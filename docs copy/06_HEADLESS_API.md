# Headless & API

HashtagCMS is designed to be consumed by any client (SPA, Mobile App, IoT) via its robust JSON API.

## The "Everything is Data" Endpoint

The primary endpoint for fetching a page is usually:
`GET /api/v1/load/{category-slug}?site_id=1&platform=api&lang=en`

### Response Structure

The API returns a JSON object mirroring the Page > Zone > Module structure.

```json
{
    "meta": {
        "title": "Home Page",
        "meta_description": "..."
    },
    "zones": [
        {
            "name": "Header",
            "modules": [
                {
                    "alias": "LogoModule",
                    "data": { "src": "logo.png" }
                },
                {
                    "alias": "MenuModule",
                    "data": [ ...links... ]
                }
            ]
        },
        {
            "name": "Content",
            "modules": [
                {
                    "alias": "HeroBanner",
                    "data": { "title": "Welcome", "cta": "Click Me" }
                }
            ]
        }
    ]
}
```

## How It Works

1.  **Frontend Loader**: The same loader that renders Blade views also handles API requests.
2.  **Transformation**: Instead of returning `view('...')`, service modules return `arrays` or `Resources`.
3.  **Automatic serialization**: The CMS orchestrator recursively collects data from all modules and serializes it to JSON.

## Consuming with JS Kit

We provide a **HashtagCMS JS SDK** (`@hashtagcms/core` in `cms-js-kit`) that handles:
-   Fetching this JSON.
-   Routing.
-   Mapping JSON Modules to React/Vue Components.

For example, if the API returns `{ "alias": "HeroBanner" }`, the JS SDK looks for a component registered as `HeroBanner` and renders it with the provided `data`.

See `cms-js-kit/README.md` for more details.
