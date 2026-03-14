# HashtagCMS v3.0.0 Release Notes 🚀

We are excited to announce the release of **HashtagCMS v3.0.0**! This version represents a massive leap forward in performance, developer experience, and design aesthetics.

---

## 🎨 Design & Frontend
- **Tailwind CSS Integration**: The entire frontend has been migrated to Tailwind CSS, providing a modern, sleek, and highly customizable UI.
- **New Tailwind Theme**: A brand new default theme built from the ground up with Tailwind CSS.
- **Bug Fixes**: Numerous frontend stability fixes and cross-browser compatibility improvements.

---

## 🛠️ Admin Panel (admin-ui-kit)
- **Vue 3 Composition API**: The entire admin dashboard has been rewritten using the Vue 3 Composition API, resulting in a significantly faster and more reactive experience.
- **Rewritten Module Creator**: The module scaffolding wizard is now more powerful and intuitive, allowing for complex module generation in seconds.
- **Module Type Management**: A new dedicated module to manage "Module Types" dynamically, removing hardcoded types and enabling custom behavior registration.

---

## 🏗️ Core Architecture & Performance
- **Site & Language Cloner**: Re-implemented as a powerful **Service-based architecture** (`SiteClonerService`). Easily replicate sites or specific language configurations.
- **Unified Resolvers**: Introduced a new pipeline-based resolution system (Site, Language, Platform, and Route resolvers) for centralized and efficient request context handling.
- **Optimized Middleware**: Fixed and streamlined internal middleware to reduce request overhead and enhance security.
- **ViewComposer Optimization**: Enhanced `ViewComposer` for better performance when passing global data to templates.
- **Queue-based Long Tasks**: Long-running operations like site cloning, data exports, and complex imports are now handled via **Laravel Queues/Jobs**, ensuring a responsive UI.

---

## 🔌 Custom Modules & Extensions
- **Improved Module Loader Support**: Enhanced the custom module loading mechanism with better fallback handling.
- **Service Discovery**: Support for automated service discovery using the [handle($moduleInfo, $params)](file:///Users/marghoobsuleman/www/suleman/opensourced/focused/hashtagcms/hashtagcms/src/Console/Commands/CmsModuleCreateCommand.php#35-144) and [getResult()](file:///Users/marghoobsuleman/www/suleman/opensourced/focused/hashtagcms/hashtagcms/src/Core/Main/QueryModuleLoader.php#24-28) interface.

---

## 🛡️ Security & Permissions
- **Improved CMS Policies**: Refined Role-Based Access Control (RBAC) with better site-wise permission enforcement.
- **Enhanced Logging**: Comprehensive system and audit logs for better traceability of administrative actions.

---

## 📚 Documentation
- **Comprehensive Technical Audit**: Every documentation file (00-43) has been audited and updated to match the latest v3.0.0 implementation.
- **New Guides**: Enhanced guides for Custom Modules, Service Discovery, and Zero-Database Auth.

---

**Upgrading**: Please refer to the [Installation Guide](docs/02-installation.md) for upgrade instructions. v3.0.0 may require fresh asset publishing and database migrations.
