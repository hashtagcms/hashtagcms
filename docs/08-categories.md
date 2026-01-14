# Category Management

Categories are the backbone of your site's structure in HashtagCMS. Unlike simple "blog tags", Categories here represent the **Site Map** and **Page Hierarchy**.

## Concept
A Category acts as a container for:
1.  **Modules**: Which widgets appear on this URL?
2.  **Theme**: Which layout should be used?
3.  **Pages**: Is this a listing of articles (e.g., /blog)?

## Creating a Category
1.  **Name**: "Services"
2.  **Parent**: "Home" (Root) or null
3.  **Link Rewrite**: `services` (The URL slug)
4.  **Template/Theme**: Choose `basic` (or your custom theme).

## Important Properties
-   **Is New/Open in New Tab**: Menu properties.
-   **Show in Menu**: Toggle visibility in `htcms_get_header_menu()`.
-   **Publish Status**: Draft/Published.
-   **Required Login?**: Restrict access to logged in users (e.g., "Members Only" section).


## API Response
When fetching a category via API (`/load-data`), you get:
-   `category`: Metadata (id, name, link_rewrite).
-   `modules`: Array of assigned modules for the current platform.
-   `meta`: SEO tags defined for this category.
