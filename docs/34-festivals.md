# Festivals Feature

The **Festivals** feature in HashtagCMS allows standard, seasonal, or event-based overlays and styles to be automatically applied to your site based on date ranges. This is perfect for celebrating holidays (Christmas, Diwali, New Year) or special events without deploying code changes.

## Overview

A "Festival" consists of:
1.  **Date Range**: Start and End dates for when it should be active.
2.  **Assets**: An Image or a Lottie Animation (JSON).
3.  **Styling**: Custom CSS classes for the `<body>` tag.
4.  **Positioning**: settings for where the asset should appear (overlay, background, etc.).

When a request is made, the CMS checks the current date against all active festivals for the current site. If a match is found, the festival data is injected into the response.

## Admin Configuration

To create a festival:
1.  Navigate to **Admin Panel > CMS > Festivals**.
2.  Click **Add New**.
3.  Fill in the details:
    -   **Name**: Internal name (e.g., "Christmas 2025").
    -   **Start/End Date**: The period this festival is active.
    -   **Image/Lottie**: Upload your asset.
    -   **Body CSS**: A class to add to the `<body>` (e.g., `theme-christmas`).
    -   **Positioning**: Top, Left, Z-Index.
    -   **Lottie Settings**: Autoplay, Loop, Play Mode (if using Lottie).
4.  Set **Publish Status** to Active.

## Technical Implementation

### Backend Logic
The `DataLoader` automatically fetches festivals associated with the current `Site`.
The `Site` model has a relationship that filters these records:

```php
// src/Models/Site.php
public function festival()
{
    return $this->hasMany(Festival::class)
        ->where([
            ['start_date', '<=', date('Y-m-d')],
            ['end_date', '>=', date('Y-m-d')],
            ['publish_status', '=', 1],
        ])->orderBy('position', 'asc');
}
```

This ensures only currently active festivals are loaded.

### API Response
In the `/load-data` endpoint, the `festivals` key provides the array of active festivals.

```json
{
    "festivals": [
        {
            "id": 1,
            "name": "Christmas",
            "image": "festivals/santa.png",
            "lottie": null,
            "body_css": "theme-snow",
            "start_date": "2025-12-20",
            "end_date": "2025-12-30",
            "position": 1,
            "top": "0",
            "left": "0",
            "z_index": 999
        }
    ]
    // ...
}
```

### Frontend Usage (Blade)

HashtagCMS `LayoutManager` provides helpers to use this data in Blade templates.

```php
@php
    $layout = app()->HashtagCms->layoutManager();
    $festivalCss = $layout->getFestivalCss(); // e.g., "theme-snow"
    $festivals = $layout->getFestivalObject();
@endphp

<body class="{{ $festivalCss }}">
    @if($festivals)
        @foreach($festivals as $festival)
            @if($festival['lottie'])
                <lottie-player src="{{ $festival['lottie'] }}" ...></lottie-player>
            @elseif($festival['image'])
                 <img src="{{ htcms_get_media($festival['image']) }}" 
                      style="position:absolute; top:{{ $festival['top'] }}px; left:{{ $festival['left'] }}px; z-index:{{ $festival['z_index'] }};">
            @endif
        @endforeach
    @endif
    
    <!-- Site Content -->
</body>
```

### Frontend Usage (Headless/React/Vue)

1.  Read the `festivals` array from the `load-data` API response.
2.  If the array is not empty:
    -   Apply `body_css` to the document body.
    -   Render the overlay component using `lottie-web` or a standard `<img>` tag based on the provided fields.
    -   Use `hide_on_complete` logic to dismiss the overlay if configured.
