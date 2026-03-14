# Modules - Complete Guide

Modules are the heart of HashtagCMS content delivery system. They define how data is fetched, processed, and displayed.

## What are Modules?

Modules are reusable components that:
- Fetch data from various sources
- Process and transform data
- Render content using Blade templates
- Can be assigned to categories and positions
- Support platform and site-specific configurations

## Module Types

HashtagCMS supports seven different module types, each designed for specific use cases.

### 1. Static Module

**Purpose**: Display content stored in the CMS database

**Use Cases**:
- Static content blocks
- About us sections
- Terms and conditions
- Footer content
- Announcements

**Configuration**:
```php
'data_type' => 'Static'
'view_name' => '' // Not required for static modules
```

**How it Works**:
- Content is stored in `static_module_contents` table
- Supports multi-language content
- No view name needed (uses default static view)
- Content is managed through admin panel

**Example**:
```php
// Admin Panel → Static Module → Add Content
Title: "Welcome Message"
Content: "<h1>Welcome to our site!</h1><p>We're glad you're here.</p>"
Language: English
```

### 2. Query Module

**Purpose**: Execute custom database queries

**Use Cases**:
- Custom data listings
- Complex joins
- Aggregated data
- Reports and analytics
- Cross-table queries

**Configuration**:
```php
'data_type' => 'Query'
'view_name' => 'modules.custom-list'
'query' => 'SELECT * FROM pages WHERE publish_status = 1 ORDER BY created_at DESC LIMIT 10'
```

**How it Works**:
- Executes raw SQL query
- Returns results as array
- Can access any database table
- Supports different database connections

**Example**:
```php
// Module Configuration
Name: "Latest Posts"
Data Type: Query
Query: "SELECT p.*, pl.title, pl.description 
        FROM pages p 
        JOIN page_langs pl ON p.id = pl.page_id 
        WHERE p.publish_status = 1 
        AND pl.lang_id = 1 
        ORDER BY p.created_at DESC 
        LIMIT 5"
View Name: "modules.latest-posts"
```

**Blade Template** (`modules/latest-posts.blade.php`):
```blade
<div class="latest-posts">
    @foreach($data as $post)
        <article>
            <h3>{{ $post->title }}</h3>
            <p>{{ $post->description }}</p>
            <a href="/{{ $post->link_rewrite }}">Read More</a>
        </article>
    @endforeach
</div>
```

### 3. Service Module

**Purpose**: Fetch data from external APIs or services

**Use Cases**:
- Third-party API integration
- Microservices architecture
- External data sources
- Weather data, stock prices, etc.
- Social media feeds

**Configuration**:
```php
'data_type' => 'Service'
'view_name' => 'modules.api-data'
'service_url' => 'https://api.example.com/data'
'method_type' => 'GET' // or 'POST'
```

**How it Works**:
- Makes HTTP request to external URL
- Supports GET and POST methods
- Returns JSON or HTML
- Can pass parameters and headers
- Caches responses

**Example**:
```php
// Module Configuration
Name: "Weather Widget"
Data Type: Service
Service URL: "https://api.weather.com/v1/current?city=London&apikey=YOUR_KEY"
Method Type: GET
View Name: "modules.weather"
```

**Response Handling**:
```blade
@if(isset($data) && is_array($data))
    <div class="weather">
        <h3>{{ $data['city'] }}</h3>
        <p>Temperature: {{ $data['temp'] }}°C</p>
        <p>Condition: {{ $data['condition'] }}</p>
    </div>
@endif
```

### 4. Custom Module

**Purpose**: Load module without any data processing

**Use Cases**:
- Custom logic in controller
- Client-side data loading
- Placeholder modules
- Third-party integrations

**Configuration**:
```php
'data_type' => 'Custom'
'view_name' => 'modules.custom-widget'
```

**How it Works**:
- No built-in data fetching (SQL/API)
- If a `Data Handler` is provided in the format `Class@method`, it will be executed
- If the `Data Handler` is empty, it just renders the view with an empty dataset
- Data can also be provided by a `ModuleDataModifier` or client-side JavaScript
- Maximum flexibility for complex or one-off logic

**Example**:
```php
// Module Configuration
Name: "Interactive Map"
Data Type: Custom
View Name: "modules.map"
```

**Blade Template**:
```blade
<div id="map-container" data-api-key="{{ config('services.maps.key') }}">
    <!-- Map will be loaded via JavaScript -->
</div>

<script>
    // Load map data via AJAX
    fetch('/api/locations')
        .then(response => response.json())
        .then(data => initMap(data));
</script>
```

### 5. QueryService Module

**Purpose**: Combine database query with external service

**Use Cases**:
- Enrich database data with API data
- Validate data against external service
- Combine local and remote data
- Data synchronization

**Configuration**:
```php
'data_type' => 'QueryService'
'view_name' => 'modules.enriched-data'
'query' => 'SELECT * FROM products WHERE active = 1'
'service_url' => 'https://api.example.com/enrich'
'method_type' => 'POST'
```

**How it Works**:
1. Executes database query
2. Sends query results to service URL
3. Service processes and returns enriched data
4. Combined data passed to view

**Example**:
```php
// Module Configuration
Name: "Product Prices"
Data Type: QueryService
Query: "SELECT id, name, sku FROM products WHERE active = 1"
Service URL: "https://pricing-api.example.com/get-prices"
Method Type: POST
View Name: "modules.products-with-prices"
```

**Service receives**:
```json
{
    "data": [
        {"id": 1, "name": "Product A", "sku": "SKU001"},
        {"id": 2, "name": "Product B", "sku": "SKU002"}
    ]
}
```

**Service returns**:
```json
{
    "data": [
        {"id": 1, "name": "Product A", "sku": "SKU001", "price": 29.99},
        {"id": 2, "name": "Product B", "sku": "SKU002", "price": 39.99}
    ]
}
```

### 6. UrlService Module

**Purpose**: Dynamic service calls with URL parameters

**Use Cases**:
- Dynamic API calls based on page context
- User-specific data
- Parameterized requests
- Real-time data

**Configuration**:
```php
'data_type' => 'UrlService'
'view_name' => 'modules.dynamic-content'
'service_url' => 'https://api.example.com/user/{user_id}/posts?limit=:limit'
'method_type' => 'GET'
```

**How it Works**:
- URL can contain placeholders
- Placeholders replaced with request parameters
- Supports dynamic routing
- Real-time data fetching

**Example**:
```php
// Module Configuration
Name: "User Profile"
Data Type: UrlService
Service URL: "https://api.example.com/users/{id}/profile"
Method Type: GET
View Name: "modules.user-profile"
```

**URL Parameters**:
```
/user/123 → https://api.example.com/users/123/profile
/user/456 → https://api.example.com/users/456/profile
```

## Module Properties

### Core Properties

```php
[
    'id' => 1,
    'name' => 'Module Name',
    'alias' => 'module-alias',
    'data_type' => 'Static|Query|Service|Custom|QueryService|UrlService',
    'view_name' => 'modules.template-name',
    'query' => 'SQL query for Query/QueryService types',
    'service_url' => 'API URL for Service types',
    'method_type' => 'GET|POST',
    'description' => 'Module description',
    'publish_status' => 1, // 0 = Draft, 1 = Published
    'site_id' => 1,
    'insert_by' => 1,
    'update_by' => 1,
]
```

### Module Assignment Properties

```php
[
    'module_id' => 1,
    'category_id' => 1,
    'site_id' => 1,
    'platform_id' => 1,
    'microsite_id' => 0,
    'position' => 1, // Display order
    'cache_module' => 0, // Enable caching
    'publish_status' => 1,
]
```

### 7. ServiceLater Module

**Purpose**: Provide a placeholder for client-side data loading, often including a target URL

**Use Cases**:
- High-latency data that shouldn't block page load
- Client-side dashboards
- Real-time updates via Polling/WebSockets

**Configuration**:
```php
'data_type' => 'ServiceLater'
'view_name' => 'modules.lazy-widget'
'service_url' => 'https://api.example.com/realtime-data'
```

**How it Works**:
- Returns an empty dataset by default
- The `service_url` property is available to the view for AJAX fetching
- Ideal for maintaining page speed (SEO) while loading rich data later

**Example**:
```blade
{{-- modules/lazy-widget.blade.php --}}
<div id="weather-details" data-url="{{ $module->service_url }}">
    Loading live stats...
</div>

<script>
    const el = document.getElementById('weather-details');
    fetch(el.dataset.url)
        .then(response => response.json())
        .then(data => renderWeather(data));
</script>
```

## Module Properties

### Core Properties

```php
[
    'id' => 1,
    'name' => 'Module Name',
    'alias' => 'module-alias',
    'data_type' => 'Static|Query|Service|Custom|QueryService|UrlService|ServiceLater',
    'view_name' => 'modules.template-name',
    'query' => 'SQL query for Query/QueryService types',
    'service_url' => 'API URL for Service types',
    'method_type' => 'GET|POST',
    'description' => 'Module description',
    'publish_status' => 1, // 0 = Draft, 1 = Published
    'site_id' => 1,
    'insert_by' => 1,
    'update_by' => 1,
]
```

### Module Assignment Properties

```php
[
    'module_id' => 1,
    'category_id' => 1,
    'site_id' => 1,
    'platform_id' => 1,
    'microsite_id' => 0,
    'position' => 1, // Display order
    'cache_module' => 0, // Enable caching (requires Redis)
    'publish_status' => 1,
]
```

## Creating Modules

### Via Admin Panel

1. Navigate to **Admin → Layout → Frontend Modules**
2. Click **Add New**
3. Fill in module details
4. Select data type
5. Configure type-specific settings
6. Save module

## Module Views (Blade Templates)

### Basic Module Template

```blade
{{-- resources/views/modules/my-module.blade.php --}}

<div class="module-container">
    @if(isset($data) && count($data) > 0)
        @foreach($data as $item)
            <div class="item">
                <h3>{{ $item->title }}</h3>
                <p>{{ $item->description }}</p>
            </div>
        @endforeach
    @else
        <p>No data available</p>
    @endif
</div>
```

### Accessing Module Data

```blade
{{-- $data contains the module data --}}
@if(isset($data))
    {{-- For array data --}}
    @if(is_array($data))
        @foreach($data as $item)
            {{ $item['field'] }}
        @endforeach
    @endif

    {{-- For object data --}}
    @if(is_object($data))
        {{ $data->field }}
    @endif
@endif
```

### Module with Pagination

```blade
<div class="module-list">
    @foreach($data as $item)
        <article>
            <h2>{{ $item->title }}</h2>
            <p>{{ $item->excerpt }}</p>
        </article>
    @endforeach

    {{-- Pagination --}}
    @if(isset($pagination))
        {{ $pagination->links() }}
    @endif
</div>
```

## Advanced Module Features

### Module Caching (future use)

Enable caching for better performance (coming soon):

```php
// In module assignment
'cache_module' => 1
```

Clear module cache:
```bash
php artisan cache:clear
```

### Module Properties
Add custom properties to modules using Layout/Module Properties

## Module Best Practices

### 1. Naming Conventions

- Use descriptive names
- Use ALL CAPS for aliases
- Prefix custom modules: `MODULE_NAME`

### 2. Performance

- Use caching for static content
- Optimize database queries
- Limit API calls
- Use pagination for large datasets

### 3. Security

- Sanitize query inputs
- Validate API responses
- Use prepared statements
- Check permissions

### 4. Reusability

- Create generic modules
- Use module properties for configuration
- Avoid hardcoding values
- Document module usage

### 5. Error Handling

```blade
@if(isset($error))
    <div class="error">{{ $error }}</div>
@elseif(isset($data))
    {{-- Display data --}}
@else
    <div class="no-data">No content available</div>
@endif
```

## Troubleshooting

### Module Not Displaying

1. Check module is published
2. Verify module assignment
3. Check category is published
4. Clear cache
5. Check view file exists

### Query Module Errors

1. Test query in database
2. Check query syntax
3. Verify table names
4. Check permissions

### Service Module Timeout

1. Increase timeout in config 'hashtagcms.external_service_timeout'
2. Check API availability
3. Implement caching
4. Add error handling
## 🛠️ How to Add a New Module Type

If you need a module type that doesn't fit the built-in patterns (like a specialized `WeatherService`), HashtagCMS provides several primary ways to extend the system.

### Option 1: Using the `Custom` Type (Quickest)
The `Custom` type allows you to define a specific class and method to handle data fetching.

a.  **Code**: Create a class (e.g., `App\Services\WeatherService`) and a method.
    ```php
    namespace App\Services;
    class WeatherService {
        public function getWeather($module, $args) {
            // Fetch your specialized weather data
            return ['items' => [...]]; 
        }
    }
    ```
b.  **Admin Panel**:
    - **Data Type**: Select `Custom`.
    - **Data Handler**: Enter `App\Services\WeatherService@getWeather`.
    - **Service Params**: (Optional) pass query string parameters like `lat=52.52&long=13.41`.

---

### Option 2: Automatic Discovery via "Unknown Module Type" (Recommended)
HashtagCMS provides a powerful discovery hook for any module type that is NOT built into the core. This is the cleanest way to add types like `WeatherService`.

a.  **Admin Panel**: Set **Data Type** to your custom name (e.g., `WeatherService`).
b.  **Code**: Create a class in `App\Services` named after your **Data Type** (e.g., `App\Services\WeatherService`).
c.  **Implementation**: Implement a `handle($moduleInfo, $params)` method.
    ```php
    namespace App\Services;

    use Illuminate\Support\Facades\Http;

    class WeatherService
    {
        private $data = [];
        /**
        * Create a new class instance.
        */
        public function __construct()
        {
            //
        }

        public function handle($moduleInfo, $params)
        {        
            $dataHandler = $moduleInfo["data_handler"]; //it's url        
            $dataKayMap = $moduleInfo["data_key_map"]; //it's mapping what to replace in url 
            $lat = $params['lat'] ?? 52.52; //coming from params
            $long = $params['long'] ?? 13.41; //coming from params
            $dataHandler = str_replace(":lat", $lat, $dataHandler);
            $dataHandler = str_replace(":long", $long, $dataHandler);
            
            try {
                $response = Http::get($dataHandler);
                $this->data = $response->json();
                info("Calling WeatherService handle");            
            } catch (\Exception $e) {
                info("Error calling api: $dataHandler " . $e->getMessage());
            }
        }

        public function getResult()
        {
            return $this->data;
        }
    }
    ```
d.  **Result**: When the CMS encounters an unknown `Data Type`, it first checks `App\Services\{Data Type}`. If found, it executes your class. If not, it falls back to the `ModuleParser` (Option 3).

---

### Option 3: Implement Global Module Parser (Advanced)
If you want to add a brand new type (e.g., `WeatherService`) available in the "Data Type" dropdown across the entire system, just enter the name in the "Data Type" field in the admin panel or create it from **Admin → Settings → Module Types**.

#### 1. Implement the Module Parser if you have smaller use case
The `ModuleLoader` automatically looks for a class named `App\Parser\ModuleParser`. If it exists, it will call a method matching your type name (with `get` prefix and `Module` suffix).

Create `app/Parser/ModuleParser.php`:
```php
namespace App\Parser;

class ModuleParser {
    /**
     * Handle the "WeatherService" data type
     */
    public function getWeatherServiceModule($module) {
        // $module contains the database record
        $handler = $module->data_handler; // e.g., 'london-weather'
        
        // Custom logic to fetch weather
        return [
            'location' => 'London',
            'forecast' => \App\Models\Weather::where('identifier', $handler)->get()
        ];
    }
}
```

---

### Option 4: Data Manipulation (`ModuleDataModifier`)
Sometimes you don't need to change *how* data is fetched, but you need to *transform* it before it reaches the Blade view. The `manipulateModuleData` hook is designed exactly for this.

#### When to Use:
- Adding calculated fields (e.g., "Reading Time") to dynamic data.
- Formatting strings or dates globally for a specific module type.
- Injecting additional context (like user-specific data) into a shared module.

#### How it Works:
The `ModuleLoader` automatically looks for `App\Parser\ModuleDataModifier`. It attempts to call methods in this order of priority:
1.  **By Alias**: A camelCase version of the module's alias (e.g., `topBanner`).
2.  **By Type**: A camelCase version of the module's data type (e.g., `queryService`).

#### Implementation Example:
Create `app/Parser/ModuleDataModifier.php`:

```php
namespace App\Parser;

class ModuleDataModifier {
    /**
     * Specifically for a module with alias 'latest-news'
     */
    public function latestNews($data, $module_obj) {
        foreach ($data as &$item) {
            $item['reading_time'] = ceil(str_word_count($item['content']) / 200) . ' min';
        }
        return $data;
    }

    /**
     * Globally for ALL 'WeatherService' modules
     */
    public function weatherService($data, $module_obj) {
        $data['processed_at'] = now()->format('Y-m-d H:i:s');
        return $data;
    }
}
```

---

## Next Steps
- [Custom Modules](14-custom-modules.md) - Deep dive into custom logic
- [Themes](11-themes.md) - Create templates for your new module data
- [API Reference](30-api-reference.md) - Module API documentation
