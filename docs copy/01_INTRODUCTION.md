# Introduction & Core Concepts

**HashtagCMS** is not just another CMS; it is a **Headless-First**, **Module-Based** Content Management Platform designed for scalability and multi-tenancy. It decouples the content management logic (Admin) from the content presentation (Frontend), allowing you to build robust applications using standard Laravel Blade or modern JavaScript Frameworks (React, Vue, etc.) via API.

---

## 🏗 Key Philosophy: "Everything is a Module"

Unlike traditional CMSs that are "Page-based" (where unique code is tied to a specific URL), HashtagCMS is **Module-based**.

- A **Page** (Category) is simply a container.
- **Content** is rendered by **Modules**.
- **Modules** are assigned to **Zones** (Theme regions).

This allows you to update a single "Header Module" and have it reflect across 100 different pages instantly, or have completely different modules load on the same URL based on the **Platform** (Mobile App vs Desktop Web).

---

## 🧩 Core Concepts

To master HashtagCMS, you must understand these 5 entities:

### 1. Site (`sites`)
Top-level container. You can host multiple websites (multitenancy) from a single HashtagCMS installation.
*   **Example**: `example.com`, `blog.example.com`.
*   Each site has its own configuration, languages, and theme.

### 2. Platform (`platforms`)
Defines the consumption channel.
*   **Example**: `public` (Desktop Web), `ios` (Mobile App), `android`, `api`.
*   **Power**: You can link different Modules to the same Zone for different Platforms. The API response automatically adjusts based on the requested platform.

### 3. Language (`langs`)
Native Multi-language support. Content can be served in multiple languages (en, es, fr) based on user preference or URL prefixes.

### 4. Hook (`hooks`)
A region (also known as a Zone) defined in your Theme where Modules are placed.
*   **Visual**: Think of your website layout. It has `Header`, `Footer`, `LeftSidebar`, `Content`, `RightSidebar`. These are Hooks.
*   **Logic**: Hooks are the glue that connects a **Module** to a **Theme Region** for a specific **Category**. You "hook" modules into these regions.

### 5. Category (`categories`)
Often synonymous with "Pages" or "Routes".
*   **Example**: `/about-us`, `/contact`, `/products/shoes`.
*   A Category defines the URL.
*   A Category is assigned a **Theme**.
*   Modules are assigned to Categories.

### 6. Module (`cms_modules`)
The worker. A module contains the logic to fetch and display data.
*   **Frontend Module**: Fetches data (Article, weather, banner) to show to users.
*   **Admin Module**: Provides the UI for creating/editing that data in the backend.

---

## 🔄 The Request Lifecycle

1.  **User Visits URL**: `example.com/about-us`
2.  **Loader**: The `FrontendLoader` intercepts the request.
3.  **Identify Platform/Site**: Determines Site ID and Platform based on domain/header.
4.  **Find Category**: Looks up `/about-us` in `categories` table.
5.  **Fetch Theme**: Loads the theme associated with the category (e.g., `basic`).
6.  **Load Modules**: Queries the `hooks` table: "What modules are assigned to Zone `Content` for Category `about-us`?"
7.  **Execute Modules**: Runs the PHP logic for each module found (Static, Query, or Service).
8.  **Render**: Returns the final HTML (or JSON for API).

---

## 🚀 Why Use HashtagCMS?
-   **Bundled API**: Zero-config API for all your content.
-   **Database Agnostic**: Supports MySQL, MariaDB, and MongoDB (via Pro).
-   **Multi-Site**: Run your Corp Site, Blog, and Intranet from one Dashboard.
-   **Granular Permissions**: Role-Based Access Control (RBAC).
