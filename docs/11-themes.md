# Theme Development

A Theme in HashtagCMS controls the visual presentation. 
- **Theme Assets**: `hashtagcms-git/resources/assets/fe/{theme_name}` (JS, CSS, Images, etc.)
- **Theme Views**: `hashtagcms-git/resources/views/fe/{theme_name}` (Blade templates)

## Directory Structure
### Views (`resources/views/fe/{theme_name}/`)
```text
.
├── _layout_/
│   └── index.blade.php    # Master layout
├── _services_/
├── auth/
├── example/              # Example modules
├── header.blade.php      # Header module
├── footer.blade.php      # Footer module
├── hero.blade.php        # Hero module
├── blog.blade.php
├── story.blade.php
└── ... (other module templates)
```

### Assets (`resources/assets/fe/{theme_name}/`)
```text
.
├── js/
├── sass/
├── fonts/
└── img/
```

## Creating a Theme
1.  **Duplicate**: Copy `basic` theme to `awesome`.
2.  **Register**: Add `awesome` to the `themes` table in the database (or via Admin).
3.  **Activate**: In **Site Settings**, select "awesome" as the active theme.

## The Layout Manager
The core magic in `_layout_/index.blade.php`:
```blade
<!DOCTYPE html>
<html>
<head>
    {!! app()->HashtagCms->layoutManager()->getHeaderContent(); !!}
    {!! app()->HashtagCms->layoutManager()->getMetaContent(); !!}
    <title>{!! app()->HashtagCms->layoutManager()->getTitle(); !!}</title>
</head>
<body>
    
    {!! app()->HashtagCms->layoutManager()->getBodyContent(); !!}

    {!! app()->HashtagCms->layoutManager()->getFooterContent(); !!}
</body>
</html>
```

## Module Templates
When you create a module in Admin, you define its "View Name", e.g., `hero`.
The CMS looks for this file in `resources/views/fe/{current_theme}/hero.blade.php`.


This means you can have the **same module** assigned in Backend, but render it completely differently by just switching the Theme.

## Asset Compilation (Optional)
To help you manage stylesheets and scripts (SCSS/JS), we provide sample configuration files.
You can find a sample `webpack.config.js` and `package.json` in:
`hashtagcms-git/resources/support`

You can copy these to your project root to set up a build pipeline.

### Understanding `webpack.config.js`
The sample config is designed to handle multiple themes efficiently. It uses a configuration array to define themes and their assets.

**To add your theme:**
1.  Open `webpack.config.js`.
2.  Locate `themesForFrontend` array.
3.  Add your new theme entry:

```javascript
let themesForFrontend = [
    {
        theme: { source: 'basic', type: 'theme' }, 
        assets: [
            { source: 'js/app.js', target: 'js/app', type: 'js' }, // Builds /js/app.js
            { source: 'sass/app.scss', target: 'css/app', type: 'css' }, // Compiles SCSS to CSS
            { source: 'img', target: 'img', type: 'copy' }, // Copies folder
            { source: 'fonts', target: 'fonts', type: 'copy' } // Copies folder
        ]
    },
    // Add your new theme here
    {
        theme: { source: 'my-new-theme', type: 'theme' }, 
        assets: [
            { source: 'js/main.js', target: 'js/main', type: 'js' },
            { source: 'sass/style.scss', target: 'css/style', type: 'css' }
        ]
    }
];
```

**Building:**
- `npm run dev` (if configured in package.json)
- Or manually: `npx webpack --mode=development`

This setup supports **Vue.js** components and **SCSS** compilation out of the box.
