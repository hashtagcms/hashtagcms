# Media Management

HashtagCMS includes a Gallery/Media Manager for handling file uploads.

## Storage Location
By default, files are stored in `storage/app/public/media` and symlinked to `public/media`.
- **Config**: Check `config/hashtagcms.php` under `media` key.
- **CDN**: You can change the `http_path` to point to an S3 bucket or CloudFront URL if you offload media.

## The Gallery Concept
Instead of just "uploading an image to a post", HashtagCMS uses **Galleries**.
- You create a gallery (e.g., "Summer Event 2024").
- You upload 20 photos to it, depends on the memory limit.
- You assign this Gallery to a **Page** or **Category**.

This makes it easy to manage reusable assets (like Logo packs, Banner sets).

## Helper Functions

### `htcms_get_media($path)`
Pass the relative DB path, get the full absolute URL.
```php
<img src="{{ htcms_get_media($page->image) }}" />
```
It automatically handles checking if the URL is local or remote (http/https).

### Supported Types
- Images (JPG, PNG, WEBP, SVG, GIF)
- Documents (PDF, DOCX) - *If configured*

## Image Resizing
HashtagCMS does not currently enforce strict image resizing on upload (to preserve quality), but it is recommended to use an image processing service (like Cloudinary or a custom Intervention Image middleware) if you need dynamic thumbnails on the frontend.
