# Multi-Platform Support

In today's world, content needs to be everywhere: Desktop, Mobile, Smart Watch, or Kiosk. HashtagCMS handles this via **Platforms**.

## What is a Platform?
A "Platform" is a delivery target. Default platforms included are:
1.  **Web** (Desktop/Responsive)
2.  **Android**
3.  **iOS**
4.  **API** (Generic)

## Why differentiate?
You might want your "Home Page" to look completely different on a Mobile App versus the Website.
- **Website**: Uses a robust "Mega Menu" module.
- **Mobile App**: Uses a lightweight "Hamburger API" module.

In HashtagCMS, you assign modules to a **(Site, Category, Platform)** tuple.
This means for the **Same Category** (e.g., "Home"), you can have:
- **Web Platform**: Assigned Modules A, B, C.
- **iOS Platform**: Assigned Modules X, Y, Z.

## How Detection Works

The CMS detects the platform via:
1.  **Header**: `x-platform=android`
2.  **GET Parameter**: `?platform=ios`
3.  **User Agent** (optional fallback)

## Developing for Platforms
When calling the API:
```
GET /api/v1/load-data?platform=android
```
The system will strictly return the modules assigned to the "Android" platform for that page. This keeps your mobile payloads small and relevant, avoiding the "over-fetching" problem common in other headless CMSs.
