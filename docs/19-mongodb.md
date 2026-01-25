# MongoDB Support (Starter+)

HashtagCMS Pro allows you to store your content in MongoDB instead of MySQL. This is ideal for high-volume content sites where schema flexibility is key.

## Requirements
-   Starter License or higher.
-   `mongodb/laravel-mongodb` package.

## Setup
1.  **Install Driver**: `composer require mongodb/laravel-mongodb`
2.  **Config**: Update `config/database.php` to include your Mongo connection.
3.  **Env**:
    ```ini
    DB_CONNECTION=mongodb
    ```

## Hybrid Mode (Smart Query)
Even if your core tables (Users, Sites) are in MySQL, you can fetch content from MongoDB using **Smart Query Modules**.
-   Set your Module Data Type to `Query`.
-   Use the JSON Query Syntax to target the Mongo connection.
    ```json
    { "connection": "mongodb", "collection": "logs", "limit": 50 }
    ```
