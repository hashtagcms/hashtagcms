# CMS Conventions and Placeholders

HashtagCMS uses a set of special placeholders and conventions to handle dynamic path resolution and content injection within themes, modules, and configurations.

## 1. Asset & Resource Path Conventions
These placeholders are used to link assets (CSS, JS, Images) within your themes without hardcoding the paths. They are dynamically resolved based on the active theme's directory.

| Placeholder | Description | Resolved Example |
| :--- | :--- | :--- |
| `%{resource_path}%` | Root directory of the active theme assets. | `/assets/hashtagcms/fe/modern` |
| `%{css_path}%` | Shortcut to the theme's CSS directory. | `/assets/hashtagcms/fe/modern/css` |
| `%{js_path}%` | Shortcut to the theme's JS directory. | `/assets/hashtagcms/fe/modern/js` |
| `%{image_path}%` | Shortcut to the theme's Image directory. | `/assets/hashtagcms/fe/modern/img` |

**Example Usage in Header Injection:**
```html
<link rel="stylesheet" href="%{css_path}%/style.css">
<script src="%{js_path}%/main.js"></script>
```

---

## 2. Content Injection (Hooks & Modules)
These placeholders define where dynamic content from the CMS should be placed within a theme or another module.

| Placeholder | Description |
| :--- | :--- |
| `%{cms.hook.[ALIAS]}%` | Renders all modules attached to the specified **Hook Alias**. Use this to define layout regions like headers, sidebars, etc. |
| `%{cms.module.[ALIAS]}%` | Renders a specific **Module** identified by its alias. Useful for embedding a module at a precise location. |

**Example Usage in Theme Skeleton:**
```html
<div class="header">
    %{cms.hook.HOOK_HEADER}%
</div>
<div class="main-content">
    %{cms.hook.HOOK_MAIN}%
</div>
<div class="footer">
    %{cms.hook.HOOK_FOOTER}%
</div>
```

---

## 3. Dynamic URL Tokens
Used in category configurations (Link Rewrite Pattern) to capture dynamic segments of the URL.

| Token | Description |
| :--- | :--- |
| `{link_rewrite}` | Mandatory dynamic segment (e.g., for blog post slugs or product IDs). |
| `{link_rewrite?}` | Optional dynamic segment. |

---

## 4. Admin Helpers and Internal Functions
When working within the Admin panel or custom modules, use these helpers for consistency.

- `htcms_get_resource(string $resource)`: Returns the full URL to a theme resource.
- `htcms_get_path(string $path)`: Returns the current site-aware path for a category link.
- `htcms_trans(string $key)`: Translates a string with fallback to module and language files.
