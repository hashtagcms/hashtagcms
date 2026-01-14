# API & Headless CMS Guide

HashtagCMS is built with an API-first approach, making it perfect for use as a headless CMS for mobile apps, SPAs, and any frontend framework.

## API Overview

### Base URL Structure

```
http://your-domain.com/api/hashtagcms/
```

### API Versions

- **v1**: Current stable version
- All endpoints are versioned: `/v1/endpoint`

### Authentication

HashtagCMS uses Laravel Sanctum for API authentication.

### 1. API Secret (Public Endpoints)
Most "Public" endpoints (like fetching site data) still require a basic security check to prevent unauthorized cross-origin use or scraping.
-   **Header**: `api_key: YOUR_SECRET`
-   **Query Param**: `?api_secret=YOUR_SECRET`

This secret is defined in your `.env` as `API_SECRET` and mapped in `config/hashtagcms.php`. The system validates that the provided secret matches the one configured for the current **Site Context**.

### 2. Sanctum Token (User Endpoints)
Protected endpoints (User Profile, Orders) require a standard Bearer Token.

#### Public Endpoints
No authentication required:
- Health check
- User registration
- User login

#### Protected Endpoints
Require authentication token:
- User profile
- Protected content
- User-specific data

## Available API Endpoints

### 1. Health Check

**Endpoint**: `GET /api/hashtagcms/health-check`

**Purpose**: Check if API is running

**Authentication**: None

**Request**:
```bash
curl -X GET "http://your-domain.com/api/hashtagcms/health-check"
```

**Response**:
```json
{
    "result": "okay"
}
```

### 2. Site Configuration

**Endpoint**: `GET /api/hashtagcms/public/configs/v1/site-configs`

**Purpose**: Get site configuration and metadata

**Authentication**: API Secret **Site Context** (query parameter)

**Parameters**:
- `site` (required): Site context key
- `api_secret` (required): API secret key
- `lang_id` (optional): Language ID (default: 1)
- `platform_id` (optional): Platform ID (default: 1)

**Request**:
```bash
curl -X GET "http://your-domain.com/api/hashtagcms/public/configs/v1/site-configs?site=htcms&api_secret=your_secret"
```

**Response**:
```json
{
    "site": {
        "id": 1,
        "name": "My Site",
        "domain": "example.com",
        "context": "htcms",
        "active": 1
    },
    "languages": [
        {
            "id": 1,
            "name": "English",
            "code": "en",
            "locale": "en_US"
        }
    ],
    "platforms": [
        {
            "id": 1,
            "name": "Web"
        },
        {
            "id": 2,
            "name": "Mobile"
        }
    ],
    "theme": {
        "id": 1,
        "name": "Default Theme",
        "alias": "default"
    }
}
```

### 3. Load Page Data

**Endpoint**: `GET /api/hashtagcms/public/sites/v1/load-data`

**Purpose**: Load complete page data including modules and content

**Authentication**: API Secret

**Parameters**:
- `site` (required): Site context
- `api_secret` (required): API secret
- `link_rewrite` (optional): Page URL slug
- `lang_id` (optional): Language ID
- `platform_id` (optional): Platform ID
- `category_id` (optional): Category ID

**Request**:
```bash
curl -X GET "http://your-domain.com/api/hashtagcms/public/sites/v1/load-data?site=htcms&link_rewrite=about&api_secret=your_secret"
```

**Response**:
```json
{
    "category": {
        "id": 1,
        "name": "About",
        "link_rewrite": "about",
        "parent_id": null
    },
    "modules": [
        {
            "id": 1,
            "name": "About Content",
            "alias": "about-content",
            "data_type": "Static",
            "position": 1,
            "data": {
                "title": "About Us",
                "content": "<p>Welcome to our site...</p>"
            }
        }
    ],
    "meta": {
        "title": "About Us - My Site",
        "description": "Learn more about us",
        "keywords": "about, company, information"
    },
    "breadcrumbs": [
        {"name": "Home", "url": "/"},
        {"name": "About", "url": "/about"}
    ]
}
```

### 4. Load Data for Mobile

**Endpoint**: `GET /api/hashtagcms/public/sites/v1/load-data-mobile`

**Purpose**: Optimized endpoint for mobile applications

**Authentication**: API Secret

**Parameters**: Same as load-data

**Request**:
```bash
curl -X GET "http://your-domain.com/api/hashtagcms/public/sites/v1/load-data-mobile?site=htcms&link_rewrite=blog&api_secret=your_secret"
```

**Response**: Optimized JSON structure for mobile apps

### 5. User Registration

**Endpoint**: `POST /api/hashtagcms/public/user/v1/register`

**Purpose**: Register new user

**Authentication**: None

**Request Body**:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secure_password",
    "password_confirmation": "secure_password"
}
```

**Request**:
```bash
curl -X POST "http://your-domain.com/api/hashtagcms/public/user/v1/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secure_password",
    "password_confirmation": "secure_password"
  }'
```

**Response**:
```json
{
    "success": true,
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token": "1|abc123def456..."
}
```

### 6. User Login

**Endpoint**: `POST /api/hashtagcms/public/user/v1/login`

**Purpose**: Authenticate user and get access token

**Authentication**: None

**Request Body**:
```json
{
    "email": "john@example.com",
    "password": "secure_password"
}
```

**Request**:
```bash
curl -X POST "http://your-domain.com/api/hashtagcms/public/user/v1/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "secure_password"
  }'
```

**Response**:
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token": "2|xyz789abc123..."
}
```

### 7. Get User Profile

**Endpoint**: `GET /api/hashtagcms/user/v1/me`

**Purpose**: Get authenticated user's profile

**Authentication**: Bearer Token (required)

**Request**:
```bash
curl -X GET "http://your-domain.com/api/hashtagcms/user/v1/me" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Response**:
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "profile": {
        "bio": "Software developer",
        "avatar": "/storage/avatars/user1.jpg"
    }
}
}
```

### 8. Publish Analytics (KPI)

**Endpoint**: `POST /api/hashtagcms/public/kpi/v1/publish`

**Purpose**: Publish page view or category view analytics data securely.

**Authentication**: Public (Protected by server-side sanity checks and logic)

**Request Body**:
```json
{
    "site": "htcms",
    "categoryId": 2, 
    "pageId": 10
}
```
*(Note: At least one of `categoryId` or `pageId` should be provided)*

**Request**:
```bash
curl -X POST "http://your-domain.com/api/hashtagcms/public/kpi/v1/publish" \
  -H "Content-Type: application/json" \
  -d '{
    "site": "htcms",
    "categoryId": 1
  }'
```

**Response**:
```json
{
    "success": true,
    "message": "Analytics published"
}
```
```

### 9. Get Latest Blogs

**Endpoint**: `GET /api/hashtagcms/public/sites/v1/blog/latests`

**Purpose**: Fetch latest blog posts.

**Authentication**: Public (Depends on implementation, may require `api_key` header for rate limiting/logging)

**Request Parameters**:
- `site` (string, required): Site context (e.g., 'htcms')
- `lang` (string, optional): Language code (e.g., 'en')
- `category` (string, optional): Category slug (e.g., 'blog')
- `limit` (int, optional): Number of posts to return (default: 10)

**Request**:
```bash
curl -X GET "http://your-domain.com/api/hashtagcms/public/sites/v1/blog/latests?site=htcms&category=blog&limit=5" \
  -H "Content-Type: application/json"
```

**Response**:
```json
{
    "data": [
        {
            "id": 1,
            "title": "My Blog Post",
            "linkRewrite": "my-blog-post",
            "link_rewrite": "my-blog-post",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "media": {
               "icon": "path/to/icon.png"
            }
        },
        ...
    ]
}
```

## API Configuration

### Setting Up API Secrets

Edit `config/hashtagcms.php`:

```php
'api_secrets' => [
    'htcms' => env('API_SECRET', 'your_random_secret_key'),
    'site2' => env('API_SECRET_SITE2', 'another_secret_key'),
],
```

In `.env`:
```env
API_SECRET=your_random_secret_key_here
API_SECRET_SITE2=another_secret_key_here
```

### Endpoint Configuration

HashtagCMS allows you to define custom endpoints for external API calls in your `.env` file (or `config/hashtagcms.php`). This is useful if your API is hosted on a different domain or path.

**Available Environment Variables**:

```env
# Base URL for all external calls (Used as default prefix)
HASHTAG_CMS_EXTERNAL_API_BASE_URL=http://your-api-domain.com

# Specific Endpoint Overrides (Optional)
HASHTAG_CMS_DATA_API=                   # Default: {BASE}/api/hashtagcms/public/sites/v1/load-data
HASHTAG_CMS_LOGIN_API=                  # Default: {BASE}/api/hashtagcms/public/sites/v1/login
HASHTAG_CMS_LOGOUT_API=                 # Default: {BASE}/api/hashtagcms/public/user/v1/logout
HASHTAG_CMS_USER_ME_API=                # Default: {BASE}/api/hashtagcms/user/v1/me
HASHTAG_CMS_USER_PROFILE_UPDATE_API=    # Default: {BASE}/api/hashtagcms/user/v1/profile
HASHTAG_CMS_PUBLISH_API=                # Default: {BASE}/api/hashtagcms/public/kpi/v1/publish
```

### Domain Mapping


```php
'domains' => [
    'api.example.com' => 'htcms',
    'api.site2.com' => 'site2',
],
```

## Using as Headless CMS

### React Example

```javascript
import React, { useState, useEffect } from 'react';

function Page({ slug }) {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetch(`http://api.example.com/api/hashtagcms/public/sites/v1/load-data?site=htcms&link_rewrite=${slug}&api_secret=your_secret`)
            .then(response => response.json())
            .then(data => {
                setData(data);
                setLoading(false);
            });
    }, [slug]);

    if (loading) return <div>Loading...</div>;

    return (
        <div>
            <h1>{data.meta.title}</h1>
            {data.modules.map(module => (
                <div key={module.id}>
                    <h2>{module.name}</h2>
                    <div dangerouslySetInnerHTML={{ __html: module.data.content }} />
                </div>
            ))}
        </div>
    );
}
```

### Vue.js Example

```vue
<template>
    <div v-if="loading">Loading...</div>
    <div v-else>
        <h1>{{ data.meta.title }}</h1>
        <div v-for="module in data.modules" :key="module.id">
            <h2>{{ module.name }}</h2>
            <div v-html="module.data.content"></div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            data: null,
            loading: true
        }
    },
    mounted() {
        fetch(`http://api.example.com/api/hashtagcms/public/sites/v1/load-data?site=htcms&link_rewrite=${this.$route.params.slug}&api_secret=your_secret`)
            .then(response => response.json())
            .then(data => {
                this.data = data;
                this.loading = false;
            });
    }
}
</script>
```

### Mobile App (React Native)

```javascript
import React, { useState, useEffect } from 'react';
import { View, Text, ScrollView, ActivityIndicator } from 'react-native';

const API_BASE = 'http://api.example.com/api/hashtagcms/public';
const API_SECRET = 'your_secret';
const SITE = 'htcms';

function PageScreen({ route }) {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const { slug } = route.params;

    useEffect(() => {
        fetch(`${API_BASE}/sites/v1/load-data-mobile?site=${SITE}&link_rewrite=${slug}&api_secret=${API_SECRET}`)
            .then(response => response.json())
            .then(data => {
                setData(data);
                setLoading(false);
            })
            .catch(error => {
                console.error(error);
                setLoading(false);
            });
    }, [slug]);

    if (loading) {
        return <ActivityIndicator size="large" />;
    }

    return (
        <ScrollView>
            <Text style={{ fontSize: 24 }}>{data.meta.title}</Text>
            {data.modules.map(module => (
                <View key={module.id}>
                    <Text style={{ fontSize: 18 }}>{module.name}</Text>
                    <Text>{module.data.content}</Text>
                </View>
            ))}
        </ScrollView>
    );
}
```

## Authentication Flow

### 1. Register User

```javascript
async function register(name, email, password) {
    const response = await fetch('http://api.example.com/api/hashtagcms/public/user/v1/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            name,
            email,
            password,
            password_confirmation: password
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        // Store token
        localStorage.setItem('token', data.token);
        return data.user;
    }
}
```

### 2. Login User

```javascript
async function login(email, password) {
    const response = await fetch('http://api.example.com/api/hashtagcms/public/user/v1/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    
    if (data.success) {
        localStorage.setItem('token', data.token);
        return data.user;
    }
}
```

### 3. Make Authenticated Requests

```javascript
async function getProfile() {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://api.example.com/api/hashtagcms/user/v1/me', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
}
```

## API Best Practices

### 1. Error Handling

```javascript
async function fetchData(url) {
    try {
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}
```

### 2. Caching

```javascript
const cache = new Map();

async function fetchWithCache(url, ttl = 300000) { // 5 minutes
    const cached = cache.get(url);
    
    if (cached && Date.now() - cached.timestamp < ttl) {
        return cached.data;
    }
    
    const data = await fetch(url).then(r => r.json());
    cache.set(url, { data, timestamp: Date.now() });
    
    return data;
}
```

### 3. Rate Limiting

Implement client-side rate limiting to avoid overwhelming the API.

### 4. Security

- Always use HTTPS in production
- Store API secrets securely
- Never expose secrets in client-side code
- Use environment variables
- Implement token refresh
## Session Handling in Standalone Mode

When running in "External API" or "Standalone" mode (especially in Docker environments without a local database), the default `database` session driver will fail because there is no local database connection to store session data.

To ensure sessions (login state) persist correctly:

1.  **Use `cookie` driver (Recommended for Zero-DB)**:
    This stores the session data encrypted in the user's browser cookie. It works perfectly with load balancers and container restarts without needing external services.
    
    Update your `.env`:
    ```env
    SESSION_DRIVER=cookie
    ```

2.  **Use `redis` driver (Good for Production)**:
    If you have a Redis service available, this offers high performance and persistence.
    
    Update your `.env`:
    ```env
    SESSION_DRIVER=redis
    ```

3.  **Avoid `file` driver in Docker**:
    The `file` driver stores sessions on the container's filesystem. These are lost if the container restarts and are not shared between replicas.


## CORS Configuration

If using API from different domain, configure CORS in Laravel:

```php
// config/cors.php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['https://your-frontend.com'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

## API Response Formats

### Success Response

```json
{
    "success": true,
    "data": { ... },
    "message": "Operation successful"
}
```

### Error Response

```json
{
    "success": false,
    "error": "Error message",
    "code": "ERROR_CODE"
}
```

## Next Steps

- [Custom Modules](14-custom-modules.md) - Create API endpoints
- [Configuration](26-configuration.md) - API configuration
- [Security](25-middleware-security.md) - Secure your API
