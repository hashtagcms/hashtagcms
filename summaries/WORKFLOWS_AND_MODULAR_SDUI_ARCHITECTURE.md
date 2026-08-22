# HashtagCMS Workflows & Modular Mobile SDUI Architecture

> **AI Session Reference Document**  
> **Last Updated:** August 22, 2026  
> **Ecosystem Scope:** `hashtagcms/hashtagcms`, `hashtagcms/hashtagcms-workflows`, `hashtagcms/hashtagcms-extended`, `hashtagcms-app`

---

## 1. Executive Summary & Ecosystem Overview

HashtagCMS is designed as a decoupled, multi-site, multi-platform content and application management ecosystem. Over the recent sessions, the ecosystem was expanded with two major architectural pillars:

1. **`hashtagcms/hashtagcms-workflows` (Generic Config-Driven Workflow Engine)**:  
   Transforms server-driven actions (e.g. Add to Cart, Apply Coupon, Checkout, Submit Lead, Book Appointment) from hardcoded static PHP classes into an organization-agnostic, declarative workflow orchestrator. It allows any business to define input contracts, route requests to external/internal target services (REST APIs, microservices, Laravel services, events), and emit dynamic Server-Driven UI (SDUI) client directives.

2. **`hashtagcms-app` (Modular UI Kits & Binary Size Optimization)**:  
   Refactors the Kotlin Multiplatform (Android, iOS, Desktop/Web) mobile client from a monolithic UI bundle into an extensible, tree-shakable **Core Engine + Pluggable Domain UI Kits** architecture. Apps only import and bundle the specific UI kits they need (`FoodUiKitPlugin`, `ContentUiKitPlugin`), while supporting zero-binary UI layout updates from the CMS via `DynamicPrimitiveViewModule`.

---

## 2. HashtagCMS Workflows Engine (`hashtagcms/hashtagcms-workflows`)

### Architecture Pipeline

```
+----------------------------------------------------------------------------------------------------+
|                                    HASHTAGCMS WORKFLOWS ENGINE                                      |
+----------------------------------------------------------------------------------------------------+
                                                  │
                                                  ▼
                          +-----------------------------------------------+
                          |            1. Payload Validator               |
                          |   Validates required keys, types & rules      |
                          +-----------------------------------------------+
                                                  │
                                                  ▼
                          +-----------------------------------------------+
                          |      2. Variable Interpolator / Context       |
                          |  Resolves {{ payload.* }}, {{ user.* }}, etc. |
                          +-----------------------------------------------+
                                                  │
                                                  ▼
                          +-----------------------------------------------+
                          |          3. Target Action Execution           |
                          | ┌───────────────────────────────────────────┐ |
                          | │ • HttpTargetAdapter (REST APIs / Microsvc)│ |
                          | │ • ServiceTargetAdapter (Laravel Container)│ |
                          | │ • EventTargetAdapter (Events & Queues)    │ |
                          | │ • CustomClassAdapter (PHP Handlers)       │ |
                          | └───────────────────────────────────────────┘ |
                          +-----------------------------------------------+
                                                  │
                                                  ▼
                          +-----------------------------------------------+
                          |        4. Directives Compiler & Rules         |
                          |  Evaluates success/failure conditions & maps  |
                          |  directives (toast, mutate_cart, sheet, etc.) |
                          +-----------------------------------------------+
                                                  │
                                                  ▼
                          +-----------------------------------------------+
                          |            5. Client JSON Response            |
                          |      + Audit Log (Latency, Payload, Dirs)     |
                          +-----------------------------------------------+
```

### Key Engine Components

- **`VariableInterpolator.php` (`src/Engine/VariableInterpolator.php`)**:
  - Recursively resolves variables across strings, objects, and nested arrays.
  - Supported placeholders:
    - `{{ payload.product_id }}` (Client request input)
    - `{{ user.id }}`, `{{ user.email }}` (Authenticated user context)
    - `{{ site.id }}` (Current HashtagCMS site context)
    - `{{ env.INVENTORY_SERVICE_URL }}` (Environment variables)
    - `{{ response.body.order_id }}` (Data returned from target service)
    - `{{ response.status }}` (HTTP status code)
    - `{{ variable | default: 'value' }}` (Default fallback filters)
  - Preserves native data types (integers, booleans, arrays).

- **Pluggable Target Service Adapters (`src/Engine/TargetAdapters/`)**:
  - `HttpTargetAdapter`: Forwards requests to external REST APIs (ERP, Shopify, Stripe, SAP, microservices) with dynamic headers, auth tokens, body payload, and timeouts.
  - `ServiceTargetAdapter`: Calls internal container services (`App\Services\CartService@addItem`).
  - `EventTargetAdapter`: Fires Laravel Events or pushes to queues.
  - `CustomClassAdapter`: Delegates to custom PHP classes implementing `WorkflowHandlerInterface`.

- **`GenericWorkflowEngine.php` (`src/Engine/GenericWorkflowEngine.php`)**:
  - Validates payload against Laravel validation rules.
  - Executes target adapters with interpolated context.
  - Compiles dynamic client directives (`mutate_cart`, `toast`, `navigate`, `open_sheet`, `trigger_haptic`) based on execution results.

- **Admin Panel Management**:
  - Module ID `60` (`workflows/home`): Category redirect.
  - Module ID `61` (`workflows/manage`): Workflow Manager (CRUD & JSON Config Builder).
  - Module ID `62` (`workflows/logs`): Audit Logs (Execution latency in ms, payload, and directives).
  - Route reflection dispatcher matching core HashtagCMS web routes.
  - Dual-path view support (`workflows.manage.addedit` & `be.workflows.addedit`).

---

## 3. Modular Mobile SDUI Architecture (`hashtagcms-app`)

### Design Philosophy
Prevent binary bloat (APK / IPA size) and build-time degradation as new domain UI components (Food Delivery, E-commerce, Media, Articles) are created.

```
                                  +---------------------------------------+
                                  |         hashtagcms-sdui-core          |
                                  | ┌───────────────────────────────────┐ |
                                  | │ • LayoutTransformer & Parser      │ |
                                  | │ • ViewModuleRegistry              │ |
                                  | │ • SduiUiPlugin Contract           │ |
                                  | │ • DynamicPrimitiveViewModule      │ |
                                  | └───────────────────────────────────┘ |
                                  +---------------------------------------+
                                                      ▲
                                                      │ (implements SduiUiPlugin)
                 ┌────────────────────────────────────┼────────────────────────────────────┐
                 │                                    │                                    │
  +-----------------------------+      +-----------------------------+      +-----------------------------+
  |     FoodUiKitPlugin         |      |     ContentUiKitPlugin      |      |    EcommerceUiKitPlugin     |
  | ┌─────────────────────────┐ |      | ┌─────────────────────────┐ |      | ┌─────────────────────────┐ |
  | │ • ProductViewModule     │ |      | │ • HeroViewModule        │ |      | │ • ProductGridViewModule │ |
  | │ • RankedItemViewModule  │ |      | │ • ArticleViewModule     │ |      | │ • FilterDrawerViewModule│ |
  | │ • PromoBannerViewModule │ |      | │ • CategoryNavViewModule │ |      | │ • AddressPickerModule   │ |
  | │ • RewardCardViewModule  │ |      | │ • CommentViewModule     │ |      | │ • OrderSummaryModule    │ |
  | └─────────────────────────┘ |      | └─────────────────────────┘ |      | └─────────────────────────┘ |
  +-----------------------------+      +-----------------------------+      +-----------------------------+
```

### Core Mechanisms

1. **`SduiUiPlugin` Contract (`org.hashtagcms.app.sdui.modules.SduiUiPlugin`)**:
   - Zero-reflection, tree-shakable plugin interface.
   - Applications only include required UI dependencies in `build.gradle.kts` and install them in one line (`FoodUiKitPlugin.install()`).
   - Unreferenced UI kits are 100% stripped by R8 / Proguard during release builds.

2. **Dynamic JSON Primitives Engine (`DynamicPrimitiveViewModule`)**:
   - Renders arbitrary composable layouts (`Row`, `Column`, `Text`, `Image`, `Badge`, `Button`, `Spacer`) declared purely in HashtagCMS JSON.
   - Allows promotional banners, announcement ribbons, and simple cards to be created dynamically without shipping a new app release.

3. **Graceful Fallback**:
   - If an unimported `viewType` is received from the server, `ViewModuleRegistry` gracefully routes it to `FallbackViewModule` without crashing.

---

## 4. Current Verification State

| Feature Area | Component / Endpoint | Verification Method | Status |
|---|---|---|---|
| **Admin Panel** | `/admin/workflows/manage` | Tinker / Curl Simulation | **200 OK** |
| **Admin Panel** | `/admin/workflows/manage/create` | Tinker / View Renderer | **200 OK** |
| **Admin Panel** | `/admin/workflows/manage/edit/1` | Tinker / View Renderer | **200 OK** |
| **Admin Panel** | `/admin/workflows/logs` | Tinker / View Renderer | **200 OK** |
| **Admin Panel** | `/admin/workflows/logs/show/1` | Tinker / View Renderer | **200 OK** |
| **Workflow API** | `POST /api/hashtagcms/public/workflows/v1/execute` | Curl HTTP Request | **200 OK (Directives Emitted)** |
| **Validation** | Missing required payload attributes | Curl HTTP Request | **400 / Validation Error Directive** |
| **Audit Logs** | `workflow_logs` DB Table | MySQL Inspection | **Recorded with Execution Latency (ms)** |
| **Mobile App** | KMP Core Metadata | `./gradlew compileKotlinMetadata` | **BUILD SUCCESSFUL** |
| **Mobile App** | Android Debug Target | `./gradlew compileDebugKotlinAndroid` | **BUILD SUCCESSFUL** |

---

## 5. Directory Reference & Documentation Map

### `hashtagcms-workflows/`
- `src/Engine/GenericWorkflowEngine.php`: Central workflow execution orchestrator.
- `src/Engine/VariableInterpolator.php`: Recursive variable and template expression parser.
- `src/Engine/TargetAdapters/`: Pluggable adapters (`Http`, `Service`, `Event`, `CustomClass`).
- `src/Http/Controllers/Admin/WorkflowController.php`: Admin CRUD controller for workflows.
- `src/Http/Controllers/Admin/WorkflowLogController.php`: Audit log controller.
- `resources/views/workflows/manage/addedit.blade.php`: Admin JSON Schema Editor.
- `docs/01-architecture-overview.md` to `07-audit-logging-and-analytics.md`: Full technical guides.

### `hashtagcms-app/`
- `composeApp/src/commonMain/kotlin/org/hashtagcms/app/sdui/modules/SduiUiPlugin.kt`: Plugin installer contract.
- `composeApp/src/commonMain/kotlin/org/hashtagcms/app/sdui/modules/CmsViewModule.kt`: Pluggable registry.
- `composeApp/src/commonMain/kotlin/org/hashtagcms/app/sdui/modules/plugins/FoodUiKitPlugin.kt`: Food domain UI kit.
- `composeApp/src/commonMain/kotlin/org/hashtagcms/app/sdui/modules/plugins/ContentUiKitPlugin.kt`: Content domain UI kit.
- `composeApp/src/commonMain/kotlin/org/hashtagcms/app/sdui/modules/impl/DynamicPrimitiveViewModule.kt`: Zero-binary JSON primitive renderer.
- `docs/10-modular-ui-kits-and-size-optimization.md`: Technical documentation for modular UI kits.

---

## 6. Recommendations for Next Session

1. **Visual Drag & Drop Workflow Builder**:
   - Provide a visual node-based UI in the Admin Panel (`hashtagcms-workflows`) to connect client actions to target REST APIs with dropdown variable pickers.
2. **Additional Domain UI Kits for Mobile**:
   - `EcommerceUiKitPlugin` (Product grids, filter drawers, shipping/address pickers, checkout summary).
   - `MediaUiKitPlugin` (Video player, audio streaming card, interactive story reels).
3. **Workflow Retry & Circuit Breaker**:
   - Add configurable retry counts (`retry: 3`) and timeout thresholds for external microservice HTTP targets.
