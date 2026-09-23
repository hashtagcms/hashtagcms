# Creating a Package for HashtagCMS

This guide is the reference standard for building a distributable HashtagCMS
package — one that ships its own admin screens, menu entries, views, migrations,
and optional headless API. It follows the conventions used by the first-party
packages (`hashtagcms/workflows`, `hashtagcms/extended`).

> **Package vs. custom module?** If you only need a new *data type* for a
> front-end module, use [Custom Modules](14-custom-modules.md) (service discovery)
> instead. Build a **package** when you want self-contained admin functionality
> (CRUD screens, menu items, an API) that installs with one `composer require`.

---

## Core principles

1. **A package owns its own routing.** Core's dynamic admin router only resolves
   controllers in the `App\` and `HashtagCms\` namespaces. Your controllers live
   in *your* namespace, so you ship explicit, self-owned routes — you never copy
   core's route dispatcher.
2. **Menu entries live in `cms_modules`, seeded with dynamic ids.** Never hardcode
   a `cms_modules.id`.
3. **Views resolve through core**, driven by the module's `package` column.
4. **Nothing in core changes** to add your package.

---

## 1. Directory layout

```
your-package/
├── composer.json
├── config/
│   └── your-package.php
├── routes/
│   ├── web.php          # admin routes (self-owned)
│   └── api.php          # headless endpoints (optional)
├── resources/
│   └── views/           # loaded under a package view namespace
├── src/
│   ├── YourPackageServiceProvider.php
│   ├── Http/Controllers/Admin/     # admin CRUD controllers
│   ├── Models/
│   ├── Support/ModuleRegistry.php   # menu definitions
│   └── Database/
│       ├── Migrations/
│       └── Seeders/
└── tests/
```

---

## 2. `composer.json`

Auto-register the provider and any facade alias via Laravel package discovery:

```json
{
    "name": "vendor/your-package",
    "type": "library",
    "require": {
        "php": "^8.3",
        "illuminate/support": "^10.0 || ^11.0 || ^12.0 || ^13.0",
        "hashtagcms/hashtagcms": "^2.0.6 || ^3.0"
    },
    "autoload": {
        "psr-4": { "Vendor\\YourPackage\\": "src/" }
    },
    "extra": {
        "laravel": {
            "providers": ["Vendor\\YourPackage\\YourPackageServiceProvider"]
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

---

## 3. Service provider

The provider wires everything up. Note that **core owns admin routing** — you
just load your own route files; there is no registration call into core.

```php
namespace Vendor\YourPackage;

use Illuminate\Support\ServiceProvider;

class YourPackageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/your-package.php', 'your-package');
        // bind services / singletons here
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Self-owned routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // IMPORTANT: the view namespace MUST equal the `package` value you store
        // in cms_modules (see §6) so core can resolve your views.
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'your-package');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/your-package.php' => config_path('your-package.php'),
            ], 'your-package-config');
        }
    }
}
```

---

## 4. Config

Keep options env-driven and publishable. Two options every package should expose:

```php
return [
    // Fallback site id used when a site-specific record is not found.
    'master_site_id' => (int) env('YOURPKG_MASTER_SITE_ID', 1),

    // The admin middleware stack (kept configurable for host apps).
    'middleware' => ['web', 'auth:sanctum', 'cmsModuleInfo', 'cmsInterceptor'],

    // Optional explicit admin route prefix; null = {admin_base}/your-package
    'route_prefix' => env('YOURPKG_ROUTE_PREFIX', null),
];
```

---

## 5. Admin controllers

Extend core's `BaseAdminController` (directly or via a thin package base) to get
the generic CRUD engine. Declare the list fields and data source; override
`store()` for validation.

```php
namespace Vendor\YourPackage\Http\Controllers\Admin;

use HashtagCms\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;
use Vendor\YourPackage\Models\Thing;

class ThingController extends BaseAdminController
{
    protected $dataFields   = ['id', 'name', 'updated_at']; // list columns
    protected $dataSource   = Thing::class;
    protected $actionFields = ['edit', 'delete'];

    public function store(Request $request)
    {
        // validate, then $this->saveData(['model' => $this->dataSource, 'data' => $data], $id);
    }
}
```

The base provides these actions (from the `HasCrudOperations` trait), all with
plain signatures: `index($more=null)`, `create()`, `edit($id=0,$param1=0)`,
`store(Request)`, `destroy($id)`, `publish($id=0,$status=0)`, `search()`,
`show($id)`.

---

## 6. Admin routes — self-owned, explicit, named

Because core cannot resolve your namespace, **you** register the routes. Since
the CRUD actions have plain signatures, Laravel binds `{id}` and injects
`Request` natively — no dynamic dispatcher needed. Name your routes.

```php
// routes/web.php
use Illuminate\Support\Facades\Route;
use Vendor\YourPackage\Http\Controllers\Admin\ThingController;

$adminBasePath = trim(config('hashtagcmsadmin.cmsInfo.base_path', 'admin'), '/');
$routePrefix   = trim(config('your-package.route_prefix') ?: ($adminBasePath . '/your-package'), '/');
$middleware    = config('your-package.middleware', ['web', 'auth:sanctum', 'cmsModuleInfo', 'cmsInterceptor']);

Route::prefix($routePrefix)->middleware($middleware)->name('yourpkg.')->group(function () {

    Route::controller(ThingController::class)->prefix('things')->name('things.')->group(function () {
        Route::match(['get', 'post'], '/', 'index')->name('index');
        Route::match(['get', 'post'], 'search', 'search')->name('search');
        Route::match(['get', 'post'], 'create', 'create')->name('create');
        Route::match(['get', 'post'], 'edit/{id?}/{param1?}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::match(['get', 'post'], 'publish/{id?}/{status?}', 'publish')->name('publish');
        Route::match(['get', 'post', 'delete'], 'destroy/{id}', 'destroy')->name('destroy');
    });
});
```

> The admin UI generates action URLs as `{admin}/{controller_name}/{action}/{id}`
> (e.g. `.../things/edit/5`, `.../things/store`). The routes above match that
> scheme, so the generic list/edit UI works out of the box.

---

## 7. Admin menu — `cms_modules` with **dynamic ids**

Menu entries are rows in `cms_modules`. **Never hardcode ids** — they collide
with core and other packages. Define modules by slug, reference parents by slug,
and let the database assign the id.

### The registry

```php
namespace Vendor\YourPackage\Support;

class ModuleRegistry
{
    const PACKAGE_NAME = 'your-package'; // MUST match the loadViewsFrom() namespace

    public static function definitions(): array
    {
        return [
            'group' => [
                'name' => 'Your Package', 'controller_name' => 'your-package/home',
                'parent' => null, 'icon' => 'fa fa-cube', 'position' => 70,
                'list_view_name' => null, 'edit_view_name' => null,
            ],
            'things' => [
                'name' => 'Things', 'controller_name' => 'your-package/things',
                'parent' => 'group', 'icon' => 'fa fa-list', 'position' => 71,
                // Relative name — core prefixes it with the `package` column.
                'list_view_name' => null,
                'edit_view_name' => 'your-package/things/addedit',
            ],
        ];
    }
}
```

### The seeding migration (id-agnostic, idempotent)

```php
public function up(): void
{
    if (!Schema::hasColumn('cms_modules', 'package')) {
        Schema::table('cms_modules', fn (Blueprint $t) => $t->string('package', 100)->nullable()->after('icon_css'));
    }

    $modules = ModuleRegistry::definitions();
    $slugToId = [];

    foreach ($modules as $slug => $m) {
        $parentId = empty($m['parent']) ? 0
            : ($slugToId[$m['parent']] ?? (int) DB::table('cms_modules')
                ->where('controller_name', $modules[$m['parent']]['controller_name'] ?? '')->value('id'));

        $data = [
            'name' => $m['name'], 'display_name' => $m['name'],
            'controller_name' => $m['controller_name'], 'parent_id' => $parentId,
            'icon_css' => $m['icon'], 'list_view_name' => $m['list_view_name'] ?? null,
            'edit_view_name' => $m['edit_view_name'] ?? null,
            'package' => ModuleRegistry::PACKAGE_NAME, 'updated_at' => now(),
        ];

        // Upsert by controller_name (natural key); id stays dynamic.
        $existing = DB::table('cms_modules')->where('controller_name', $m['controller_name'])->first();
        if ($existing) {
            DB::table('cms_modules')->where('id', $existing->id)->update($data);
            $id = $existing->id;
        } else {
            $data['position'] = $m['position'] ?? 0;
            $data['created_at'] = now();
            $id = DB::table('cms_modules')->insertGetId($data);
        }
        $slugToId[$slug] = $id;

        DB::table('cms_permissions')->updateOrInsert(['module_id' => $id, 'user_id' => 1], ['readonly' => 0]);
    }
}

public function down(): void
{
    $names = array_column(ModuleRegistry::definitions(), 'controller_name');
    $ids = DB::table('cms_modules')->whereIn('controller_name', $names)->pluck('id')->all();
    if ($ids) DB::table('cms_permissions')->whereIn('module_id', $ids)->delete();
    DB::table('cms_modules')->whereIn('controller_name', $names)->delete();
}
```

---

## 8. Views — resolved through core

Place views under `resources/views/` (registered as the `your-package` namespace
in the provider). **The magic:** the `cmsModuleInfo` middleware loads the current
module's `cms_modules` row into `request()->module_info`, and core's
`getViewNames($moduleInfo, $type)` builds the view name from the row's
`list_view_name` / `edit_view_name` and prefixes it with the **`package`
column** → `your-package::things.addedit`.

Because the `package` value equals your `loadViewsFrom()` namespace, it just
works — no package-specific view helper needed.

**Generic CRUD** does this automatically. For a **custom page**, render the same
way:

```php
public function index($more = null)
{
    return htcms_admin_view(
        $this->getViewNames(request()->module_info, 'listing'),
        ['data' => $yourData]
    );
}
```

### View conventions

```blade
@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="Things"></title-bar>

    {{-- Wrap any subtree you drive with your own JS in v-pre so the admin's
         Vue runtime leaves it alone. --}}
    <div v-pre class="max-w-6xl mx-auto">
        {{-- ... --}}
    </div>
@endsection

@push('scripts')
<script> /* vanilla JS / fetch */ </script>
@endpush
```

- Use `FormHelper`, `htcms_admin_path()`, `htcms_get_save_path()` for form actions.
- The admin Tailwind build is purged — **not every utility exists**. Stick to
  classes used elsewhere in the admin (e.g. `bg-white`, `rounded-2xl`,
  `text-slate-*`, `grid`) and use **inline styles** for anything exotic (e.g.
  gradients). White text on a missing-gradient background renders invisible.

---

## 9. Headless / public API (optional)

Register public endpoints in `routes/api.php` under the `api` middleware group
(no CSRF). **Harden errors** — never leak raw exception messages in production.

```php
public function execute(Request $request)
{
    $alias = $request->input('action');
    if (empty($alias)) {
        return response()->json(['success' => false, 'message' => 'Missing: action'], 400);
    }

    try {
        return response()->json($this->service->run($alias, $request->input('payload', [])));
    } catch (\Throwable $e) {
        report($e);
        $expose = config('your-package.expose_error_details') ?? (bool) config('app.debug');
        return response()->json([
            'success' => false,
            'message' => $expose ? $e->getMessage() : 'Request failed. Please try again later.',
        ], 500);
    }
}
```

---

## 10. Seeders & demo data

Ship idempotent example seeders (upsert by a natural key) and an aggregator so
installers can `db:seed --class=...` and immediately have something to look at.
Consider a small **Playground** admin page that lists your seeded records and
exercises your API live — the fastest way for an installer to see value.

---

## 11. Tests & release hygiene

- **Tests**: PHPUnit + `orchestra/testbench`. A test bootstrap can alias core's
  `AdminBaseModel` to a plain Eloquent model so unit tests run without a full
  CMS install.
- **CI**: run the suite across the PHP versions you support.
- Ship `LICENSE`, `CHANGELOG.md`, a `.gitignore` (ignore `/vendor`,
  `composer.lock`), a README, and stable composer constraints.

---

## Checklist

- [ ] Provider registered via `extra.laravel.providers`
- [ ] `loadViewsFrom` namespace **==** `cms_modules.package` value
- [ ] Admin routes are **explicit, named, self-owned** (no copied dispatcher)
- [ ] Controllers extend `BaseAdminController`; `store()` validates
- [ ] `cms_modules` seeded with **dynamic ids** (no hardcoded ids), upsert by `controller_name`
- [ ] Views resolve via core (`getViewNames` / `htcms_admin_view`), namespaced by the `package` column
- [ ] Public API validates input and hides error detail in production
- [ ] Idempotent seeders + (optional) a Playground demo page
- [ ] Tests, CI, LICENSE, CHANGELOG, README

---

## Reference implementation

The `hashtagcms/workflows` package implements every pattern in this guide and is
the canonical example to copy from.

---

**Previous:** [Backend Development](24-backend-dev.md) · **Index:** [Documentation Home](00-index.md)
