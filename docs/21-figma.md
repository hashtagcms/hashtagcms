# Figma Integration (Enterprise) @todo: Coming soon

The "Design-to-CMS" Workflow allows you to import Figma Designs directly into HashtagCMS as Blade Templates or Static Modules.

## Features
-   **Canvas Inspection**: Point to a Figma Node ID.
-   **Asset Extraction**: Auto-download images to `public/media`.
-   **CSS Extraction**: Converts Flex/Grid layout to Tailwind or CSS.

## Setup
1.  **Figma Token**: Get a Personal Access Token from Figma.
2.  **Env**: `FIGMA_ACCESS_TOKEN=...`
3.  **Command**: `php artisan cms:figma:import {file_key} {node_id}`

## Workflow
1.  Designer finalizes a Component (e.g., "Hero Banner") in Figma.
2.  Developer runs the import command.
3.  HashtagCMS generates:
    -   `resources/views/themes/basic/modules/generated/hero.blade.php`
    -   Associated CSS in `assets/css`.
4.  Developer reviews the code and assigns dynamic variables (e.g., changing "John Doe" to `{{ $user->name }}`).
