# Module Types Deep Dive

HashtagCMS offers powerful module types that determine **how** data is fetched. This guide explains how to configure each type in the Admin Panel.

## 1. Static Module
Displays static content managed via the CMS editor.

-   **Data Handler**: The **Alias** of the static content block.
-   **Description**: Not used for logic.
-   **Use Case**: FAQs, Privacy Policy, Terms, About Us text.

## 2. Query Module
Fetches data directly from the local database using raw SQL or Eloquent logic.

-   **Data Handler**: Your SQL Query.
    -   Example: `select * from products where is_active=1 limit 5`
-   **Method Type**: `GET` (default).
-   **Use Case**: Showing latest users, simple reports, data not available via API.

## 3. Service Module
Fetches data from an **external API**.

-   **Data Handler**: The API Endpoint URL.
    -   Example: `https://api.example.com/v1/weather`
-   **Method Type**: `GET`, `POST`, `PUT`, etc.
-   **Headers**: JSON object for headers.
    -   Example: `{"Authorization": "Bearer token", "Accept": "application/json"}`
-   **Service Params**: Query parameters string.
    -   Example: `limit=5&sort=desc`
-   **Use Case**: Fetching weather, stock prices, or data from a separate microservice.

## 4. Custom Module
Executes a specific method in a PHP class within your codebase.

-   **Data Handler**: `Namespace\Class@method`.
    -   Example: `App\Http\Controllers\MyCustomController@getData`
-   **Use Case**: Complex logic, combining multiple data sources, business logic requiring PHP code.

### Example Code
```php
namespace App\Http\Controllers;
class MyCustomController {
    public function getData($module, $args = []) {
        return ['name' => 'Custom Data', 'items' => [1,2,3]];
    }
}
```

## 5. QueryService Module
Combines a Database Query AND a Service Call.

-   **Query Statement**: The SQL query.
-   **Data Handler**: The Service URL.
-   **Query As**: Determines how query results are passed to the service.
    -   `param`: Query columns replace `{placeholders}` in the Service URL.
    -   `data`: Query results are sent as POST body to the Service.
-   **Use Case**: Fetching a list of User IDs from DB, then calling an API to get their statuses.

## 6. UrlService Module
Similar to Service Module but automatically appends current URL query parameters to the Service URL.

-   **Data Handler**: Base API URL.
-   **Use Case**: Filtering grids where frontend filters (e.g. `?price_min=10`) need to be passed to the backend API.

## 7. ServiceLater Module
Returns the Service URL strings to the view instead of fetching the data immediately.

-   **Use Case**: When you want the **Frontend** (React/JS) to fetch the data directly from the API (Client-side fetching), but you want the CMS to manage *which* API content is loaded.
