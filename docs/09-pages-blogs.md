# Pages & Blog Posts

In HashtagCMS, "Pages" differ from "Categories".
-   **Category**: A structural URL (e.g., `/blog`). It typically displays a **List of items** (via a Module).
-   **Page**: An individual content item (e.g., `/blog/my-first-post`). It hangs **under** a Category.

## Content Fields
-   **Title**: The H1.
-   **Sub Title**: Optional catchphrase.
-   **Content**: The main body (WYSIWYG HTML).
-   **Category**: Which bucket does this belong to?
-   **Tags**: For searching and grouping.

## The Relationship
To display a list of pages:
1.  Create a **Query Module**.
2.  SQL: `select * from pages where category_id = ? ...`
3.  Assign this Module to the `Category` view.

To display the page itself:
1.  The system uses the `Page Details` logic.
2.  When a user visits `/category/page-slug`, the CMS loads the `Category`'s layout but injects the `Page`'s content into the page context.
3.  You typically assign a **"Page Details" Static Module** to the Category that merely outputs the dynamic `{!! $data['page_content'] !!}`.

## Micro-Data (Schema)
Pages support extended schema fields for SEO:
-   Meta Title/Description/Keywords.
-   OpenGraph Image (defaults to the Page's main image).
-   Canonical URL.
