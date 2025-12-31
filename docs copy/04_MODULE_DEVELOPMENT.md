# Module Development

The "Module" is the heart of HashtagCMS. Mastering modules allows you to build anything.

## Types of Frontend Modules

There are three ways to define a module's logic:

### 1. Static Module
The simplest type. It has no dynamic data fetching.
-   **Use Case**: Hardcoded HTML content, simple text blocks.
-   **Implementation**: Create a record in `cms_modules` with `type='Static'`. The content is typically entered directly in the "View" field or loaded from a simple Blade file.

### 2. Query Module (Smart Query)
Fetches data directly from the database without writing a PHP controller.

-   **Old Way (SQL)**: Writing raw `SELECT * FROM table` in the Admin UI. (Works only on SQL).
-   **New Way (Smart Query)**: Defining a JSON configuration. (Works on SQL and MongoDB).

**JSON Example:**
```json
{
    "from": "blogs",
    "select": ["id", "title", "slug", "image"],
    "where": [
        ["status", "=", "published"],
        ["featured", "=", 1]
    ],
    "orderBy": ["created_at", "desc"],
    "limit": 5
}
```
**Benefits**:
-   No deployment required (logic stored in DB).
-   Driver-agnostic.
-   Safe (No SQL Injection risk).

### 3. Service Module (Controller Method)
The most powerful type. Calls a specific method in a PHP Class.

-   **Use Case**: Complex logic, API calls, manipulating data before view, Form handling.
-   **Implementation**: pointing the module to a Class and Method (e.g. `App\Http\Controllers\Frontend\BlogController@index`).

#### Example Controller

```php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * @param array $data Context data (zone info, arguments)
     */
    public function index($data = [])
    {
        $posts = Blog::with('author')->paginate(10);
        
        // Return view with data
        return view('themes.basic.modules.blog.index', compact('posts'));
    }
}
```

---

## Creating an Admin Module

Admin modules allow you to manage content in the backend.

### 1. Generate via Artisan
Coming soon: `php artisan cms:make:module`

### 2. Manual Creation
1.  **Create Model**: `App\Models\Blog.php`. Extend `MarghoobSuleman\HashtagCms\Models\AdminBaseModel` to get free features (Audit logs, etc).
2.  **Create Controller**: `App\Http\Controllers\Admin\BlogController.php`.
    -   Extend `MarghoobSuleman\HashtagCms\Http\Controllers\Admin\BaseAdminController`.
    -   Implementing `index`, `store`, `update`, `destroy` automatically handles basic CRUD if you follow the naming conventions!
3.  **Register in CMS**:
    -   Go to **Admin > Modules**.
    -   Add New Module: "Blog Manager".
    -   Is Admin: Yes.
    -   Route: `blog`. (Maps to `/admin/blog`).

---

## Module Properties

When creating a module in the Admin UI, you define:
-   **Name**: Human readable name.
-   **Alias**: Unique identifier (used in code).
-   **Linked Table**: (Optional) For automated CRUD in Admin.
-   **View Name**: The blade file to render (e.g. `modules.blog.list`).
-   **Data Type**: Static, Query, Service.
