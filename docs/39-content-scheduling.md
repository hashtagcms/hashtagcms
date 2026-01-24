# Content Scheduling

HashtagCMS allows you to schedule the publication and expiration of Pages and Categories. This ensures content is only visible to users within a specific time window.

---

## 📅 Overview

Scheduling is controlled by two fields found in the "Publishing Options" section of the Page and Category management screens:

1.  **Publish At**: The date and time when the content should *start* being visible.
    *   If left empty, the content is visible immediately (provided `Publish Status` is active).
2.  **Expire At**: The date and time when the content should *stop* being visible.
    *   If left empty, the content will never expire.

**Note:** The `Publish Status` toggle must always be **ON** (Published) for the scheduling dates to take effect. If `Publish Status` is OFF, the content will be hidden regardless of the dates.

---

## 🌍 Timezone Handling

To ensure scheduling works correctly across different server environments and user locations, it is critical to configure Timezones correctly.

### 1. Application Timezone (`APP_TIMEZONE`)
Define your application's "local" time in your `.env` file. This is the timezone you "think" in when entering dates in the admin panel.

**File:** `.env`
```env
# Example: India Standard Time
APP_TIMEZONE=Asia/Kolkata

# Example: UTC (Default)
# APP_TIMEZONE=UTC

# Example: US Eastern Time
# APP_TIMEZONE=America/New_York
```

### 2. Database Timezone (`DB_TIMEZONE`)
Your database connection must be aware of the timezone to accurately compare the "current time" (`NOW()`) against your scheduled dates.

**File:** `.env`
```env
# Must match your APP_TIMEZONE offset or region
DB_TIMEZONE=+05:30 
```

**Why is this important?**
*   If your App is in `Asia/Kolkata` (IST), "Now" might be **14:00**.
*   If your Database is in `UTC`, "Now" behaves as **08:30**.
*   If you schedule a post for **10:00 AM**, the database will think it's still **08:30 AM** (future) and hide the post, even though it's **02:00 PM** for you.

**Syncing them ensures:** `14:00 (App)` == `14:00 (Database)`, so your scheduled content appears exactly when you expect it.

---

## 🛠️ Configuration Steps for Developers

If you are setting up a new environment, ensure your `config/database.php` passes the timezone config to the connection:

```php
// config/database.php

'mysql' => [
    // ...
    'timezone' => env('DB_TIMEZONE', '+00:00'), // Add this line
    // ...
],
```

Then, strictly define your environment variables:

```bash
# .env
APP_TIMEZONE=Asia/Kolkata
DB_TIMEZONE=+05:30
```

And clear your config cache:
```bash
php artisan config:clear
```

---

## 🔍 Troubleshooting

**Problem:** "I scheduled a post for right now, but it's not showing up."

**Checklist:**
1.  Is `Publish Status` enabled?
2.  Is `Publish At` set to a time in the past (or now)?
3.  Is `Expire At` set to a time in the future (or empty)?
4.  **Timezone Check:** Does your Database time match your Real time?
    *   Run `php artisan tinker`.
    *   `DB::select('SELECT NOW()');`
    *   Does the output match your current watch time? If not, check `DB_TIMEZONE` in your `.env`.

---
