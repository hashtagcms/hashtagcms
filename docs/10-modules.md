# Modules - Complete Guide

Modules are the heart of HashtagCMS's content delivery system. They define how data is fetched, processed, and displayed.

## What are Modules?

Modules are reusable components that:
- Fetch data from various sources
- Process and transform data
- Render content using Blade templates
- Can be assigned to categories and positions
- Support platform and site-specific configurations

## Module Types

HashtagCMS supports six different module types, each designed for specific use cases.

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
- No automatic data loading
- Just renders the view
- Data must be provided by controller or JavaScript
- Maximum flexibility

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

**Purpose**: Load module without any data processing but return url in view

**Use Cases**:
- Custom logic in controller
- Client-side data loading and you have url
- Placeholder modules
- Third-party integrations

**Configuration**:
```php
'data_type' => 'ServiceLater'
'view_name' => 'modules.custom-widget'
```

**How it Works**:
- No automatic data loading
- Just renders the view and you have the url to fetch the data
- Data must be provided by controller or JavaScript
- Maximum flexibility

**Example**:
```php
// Module Configuration
Name: "Interactive Map"
Data Type: ServiceLater
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

## Creating Modules

### Via Admin Panel

1. Navigate to **Admin → Module**
2. Click **Add New Module**
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

### Module Caching

Enable caching for better performance:

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


### Module Hooks (@todo: not yet done)

Execute code before/after module loading:

```php
// In service provider
Event::listen('module.loading', function($module) {
    // Before module loads
});

Event::listen('module.loaded', function($module, $data) {
    // After module loads
});
```

## Module Best Practices

### 1. Naming Conventions

- Use descriptive names
- Use kebab-case for aliases
- Prefix custom modules: `custom-module-name`

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

## Next Steps

- [Custom Modules](14-custom-modules.md) - Create custom module types
- [Themes](11-themes.md) - Create module templates
- [API Reference](30-api-reference.md) - Module API documentation
