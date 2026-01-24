# Custom Modules (Advanced)

HashtagCMS supports two powerful ways to create custom modules with PHP logic:
1.  **Class-Based Modules** (Type: `Custom` or `Service`)
2.  **Dynamic Modules** (Custom Data Types)

## 1. Class-Based Custom Modules
This is the standard way to execute custom logic.

### Steps:
1.  **Create your Class**: 
    Create a class (e.g., in `App\Http\Controllers` or `App\Services`) with a public method.
    
    ```php
     namespace App\Services;
     
     class WeatherService 
     {
         public function getForecast($module, $params = [])
         {
             // $module contains module info
             // $params contains parsed query string from 'Service Params'
             
             return ['temp' => 25, 'condition' => 'Sunny'];
         }
     }
    ```

2.  **Register in Admin**:
    *   **Module Name**: Weather Widget
    *   **Data Type**: `Custom`
    *   **Data Handler**: `App\Services\WeatherService@getForecast`
    *   **Service Params** (Optional): `city=London&unit=metric`

The CMS will resolve the class, inject dependencies (if using `app()`), and call your method.

---

## 2. Dynamic Modules (Custom Data Types)
If you want to define a completely new **Data Type** (e.g., `GraphQL`, `MyWidget`) appearing in the Admin dropdown (or just manually typed), you can handle it via the `ModuleParser`.

### Steps:
1.  **Define Type**: In Admin, set **Data Type** to `MyWidget`.
2.  **Create Parser**:
    Create a file `app/Parser/ModuleParser.php`.
    
    ```php
    namespace App\Parser;
    
    class ModuleParser
    {
        /**
         * magic method for 'MyWidget' type
         * Pattern: get{DataType}Module
         */
        public function getMyWidgetModule($module)
        {
             return ['message' => 'Hello from MyWidget!'];
        }
    }
    ```

The CMS detects the unknown type `MyWidget`, looks for `getMyWidgetModule` in `ModuleParser`, and executes it.
