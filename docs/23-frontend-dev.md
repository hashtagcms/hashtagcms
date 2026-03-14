# Frontend Development

While API/Headless is popular, HashtagCMS excels at traditional Server-Side Rendering (SSR) with Blade.

## The Frontend Controller
The `FrontendController` is unique. It doesn't have hardcoded methods like `about()` or `contact()`. Instead, it uses a generic `index(Request $request)` method that:
1.  Parses the URL path.
2.  Queries the `categories` or `pages` table to find a match.
3.  Loads the assigned Theme and Layout.
4.  Delegates content rendering to the **LayoutManager**.

## Custom Routes
If you need a specific route logic (e.g., a checkout flow that isn't content-driven), simply define it in `routes/web.php` **before** the HashtagCMS catch-all route.

```php
// routes/web.php
Route::get('/checkout', [CheckoutController::class, 'index']);
// ... HashtagCMS routes load last
```

## Asset Management
We recommend using **Webpack**.
The `hashtagcms/fe` folder structure supports separate CSS/JS per theme.
-   `public/assets/hashtagcms/fe/basic/css/app.css`
-   `public/assets/hashtagcms/fe/dark/css/app.css`

## Frontend Helpers
Use the helpers provided in [Chapter 31](31-helper-functions.md) specifically:
-   `htcms_get_header_menu()`
-   `htcms_get_module('alias')`
-   `htcms_get_site_info()`
