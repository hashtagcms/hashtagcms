# Custom Modules & Extensions

HashtagCMS is designed to be highly extensible. While the built-in module types (Static, Query, Service, etc.) cover most use cases, you can easily implement custom logic using any of the following four patterns.

---

## 1. Automatic Service Discovery (Recommended)
This is the cleanest and most modular way to add a completely new data type (e.g., `WeatherService`, `GraphService`).

### How it Works:
When the CMS encounters a **Data Type** it doesn't recognize (e.g., `WeatherService`), it automatically looks for a corresponding class in your `App\Services` namespace.

### Implementation:
1.  **Admin Panel**: Set **Data Type** to `WeatherService`.
2.  **Code**: Create `app/Services/WeatherService.php`.
3.  **Required Interface**: Implement `handle($moduleInfo, $params)` and `getResult()`.

```php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    private $data = [];

    /**
     * @param array $moduleInfo Database record of the module
     * @param array $params Parsed query string/route parameters
     */
    public function handle($moduleInfo, $params)
    {
        $lat = $params['lat'] ?? 52.52;
        $long = $params['long'] ?? 13.41;
        
        // Example: Fetching from an API defined in 'Data Handler'
        $apiUrl = str_replace([':lat', ':long'], [$lat, $long], $moduleInfo['data_handler']);
        
        $this->data = Http::get($apiUrl)->json();
    }

    public function getResult()
    {
        return $this->data;
    }
}
```

---

## 2. Using the "Custom" Type (Class@method)
Use this when you want to use an existing class or a specific method that doesn't follow the `handle/getResult` pattern.

### Implementation:
1.  **Admin Panel**:
    - **Data Type**: Select `Custom`.
    - **Data Handler**: Enter the fully qualified method name (e.g., `App\Services\WeatherService@getWeather`).
2.  **Code**:
```php
namespace App\Services;

class WeatherService {
    public function getWeather($module, $args) {
        // Your logic here
        return ['items' => [...]]; 
    }
}
```

---

## 3. Global Module Parser
Use this for smaller projects or when you want to centralize many custom types in a single class.

### Implementation:
1.  **Admin Panel**: Set **Data Type** to your custom name (e.g., `OldWeather`).
2.  **Code**: Create `app/Parser/ModuleParser.php`.
3.  **Pattern**: Create a method named `get{DataType}Module`.

```php
namespace App\Parser;

class ModuleParser
{
    public function getOldWeatherModule($module)
    {
         return [
             'title' => 'Vintage Weather',
             'data' => \App\Models\Weather::all()
         ];
    }
}
```

---

## 4. Data Manipulation (`ModuleDataModifier`)
Sometimes you don't need to change *how* data is fetched, but you need to *transform* it (e.g., adding calculated fields) before it reaches the Blade view.

### Implementation:
Create `app/Parser/ModuleDataModifier.php`. The loader looks for methods named after the **Module Alias** (camelCase) or the **Data Type**.

```php
namespace App\Parser;

class ModuleDataModifier {
    /**
     * Automatically called for any module using 'WeatherService' type
     */
    public function weatherService($data, $module_obj) {
        $data['generated_at'] = now()->toDateTimeString();
        return $data;
    }
}
```

---

## Summary Comparison

| Feature | Service Discovery | Custom Type | Module Parser |
| :--- | :--- | :--- | :--- |
| **Best For** | New standard types | Specific logic/methods | Centralized small types |
| **Location** | `App\Services\{Name}` | Anywhere | `App\Parser\ModuleParser` |
| **Flexibility** | High (Modular) | High (Specific) | Medium (Centralized) |
| **Recommended** | Yes (Cleanest) | Yes (For overrides) | No (Legacy/Small use case) |
