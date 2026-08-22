# Master Plan & Architecture Specification: White-Label Multi-Tenant Salon Platform (`xSalon`)

> **Master Engineering & Product Specification**  
> **Ecosystem Scope:** `hashtagcms/hashtagcms`, `hashtagcms-salon`, `hashtagcms/hashtagcms-workflows`, `hashtagcms-app`  
> **Platform Model:** Multi-Tenant White-Label Salon SaaS powered by HashtagCMS

---

## 1. Executive Summary & Vision

The goal of the **xSalon Platform** is to provide a commercial-grade, multi-tenant appointment booking, membership, and rewards mobile platform powered by **HashtagCMS**. The architecture allows selling the platform to **hundreds of independent salon brands** (e.g. *xSalon Atelier, Glow & Co., Barber Republic, Pastel Nails*) with:

1. **Zero-Code Custom Theming**: Each salon can customize their primary colors, secondary accents, backgrounds, typography, corner radii, and logos dynamically from the HashtagCMS Admin Panel without mobile app rebuilds.
2. **Multi-Tenant Data Isolation**: Complete separation of services, stylists, rosters, appointments, promo codes, and payment gateways per salon using HashtagCMS `sites` context.
3. **Server-Driven Booking Funnel**: Dynamic 2-to-5 step booking flows (Service $\rightarrow$ Stylist $\rightarrow$ Date/Time $\rightarrow$ Review $\rightarrow$ Confirmation) adaptable per salon business model.
4. **Zero-Binary Dynamic Primitives**: Marketing teams can publish promotional banners and seasonal offer cards directly from CMS JSON.

---

## 2. Dynamic Server-Driven Theming Schema

Every visual element in the mobile app is parameterized through the HashtagCMS `theme` payload:

```json
{
  "theme": {
    "colors": {
      "primary": "#E11D48",
      "primaryVariant": "#BE123C",
      "secondary": "#1A1024",
      "background": "#FAF7F2",
      "surface": "#FFFFFF",
      "surfaceMuted": "#F4EFEA",
      "accent": "#EA580C",
      "onPrimary": "#FFFFFF",
      "onSecondary": "#FFFFFF",
      "textPrimary": "#1A1024",
      "textSecondary": "#71717A",
      "border": "#E4E4E7",
      "success": "#16A34A",
      "warning": "#F59E0B",
      "error": "#DC2626"
    },
    "typography": {
      "headlineFont": "PlayfairDisplay",
      "bodyFont": "Inter",
      "monoFont": "JetBrainsMono"
    },
    "shapes": {
      "cardCornerRadius": 16,
      "buttonCornerRadius": 12,
      "chipCornerRadius": 8,
      "style": "rounded"
    },
    "branding": {
      "logoUrl": "https://cdn.hashtagcms.org/tenants/xsalon/logo.png",
      "appName": "xSalon Atelier",
      "tagline": "Hair, skin and nails, held to one standard"
    }
  }
}
```

---

## 3. Database Schema Design (Multi-Tenant by `site_id`)

```
1. salon_services
   ├── id (BIGINT PK)
   ├── site_id (BIGINT FK -> sites.id)
   ├── category_id (BIGINT FK -> salon_categories.id)
   ├── name (VARCHAR: "Balayage + Gloss")
   ├── description (TEXT)
   ├── duration_minutes (INT: 120)
   ├── base_price (DECIMAL: 180.00)
   ├── image_url (VARCHAR)
   ├── is_signature (BOOLEAN: true)
   ├── publish_status (TINYINT: 1)
   └── timestamps

2. salon_stylists
   ├── id (BIGINT PK)
   ├── site_id (BIGINT FK -> sites.id)
   ├── user_id (BIGINT NULLABLE FK -> users.id)
   ├── name (VARCHAR: "Juno Okafor")
   ├── title (VARCHAR: "Senior Colourist")
   ├── bio_quote (TEXT: "Trained in Paris and Tokyo...")
   ├── experience_years (INT: 9)
   ├── rating (DECIMAL: 4.9)
   ├── review_count (INT: 284)
   ├── starting_price (DECIMAL: 180.00)
   ├── photo_url (VARCHAR)
   ├── specialties (JSON: ["Balayage", "Color correction", "Gloss"])
   ├── publish_status (TINYINT: 1)
   └── timestamps

3. salon_schedules
   ├── id (BIGINT PK)
   ├── site_id (BIGINT FK -> sites.id)
   ├── stylist_id (BIGINT FK -> salon_stylists.id)
   ├── day_of_week (TINYINT: 1=Mon, 7=Sun)
   ├── shift_start (TIME: 09:00:00)
   ├── shift_end (TIME: 19:00:00)
   ├── slot_interval_minutes (INT: 30)
   ├── is_active (BOOLEAN: true)
   └── timestamps

4. salon_bookings
   ├── id (BIGINT PK)
   ├── site_id (BIGINT FK -> sites.id)
   ├── confirmation_code (VARCHAR: "#A48521")
   ├── user_id (BIGINT FK -> users.id)
   ├── stylist_id (BIGINT NULLABLE FK -> salon_stylists.id)
   ├── service_id (BIGINT FK -> salon_services.id)
   ├── location_id (BIGINT FK -> salon_locations.id)
   ├── booking_date (DATE: 2026-04-30)
   ├── start_time (TIME: 14:00:00)
   ├── end_time (TIME: 15:40:00)
   ├── total_amount (DECIMAL: 240.00)
   ├── discount_amount (DECIMAL: 11.00)
   ├── final_amount (DECIMAL: 229.00)
   ├── status (ENUM: pending, confirmed, completed, cancelled, rescheduled)
   ├── promo_code (VARCHAR NULLABLE)
   ├── payment_status (ENUM: unpaid, deposit_paid, paid_full)
   └── timestamps

5. salon_memberships
   ├── id (BIGINT PK)
   ├── site_id (BIGINT FK -> sites.id)
   ├── name (VARCHAR: "Atelier")
   ├── slug (VARCHAR: "atelier")
   ├── monthly_price (DECIMAL: 129.00)
   ├── annual_price (DECIMAL: 1315.00)
   ├── perks (JSON: ["2 signature services/mo", "15% off everything", "Early access to new stylists"])
   ├── is_most_chosen (BOOLEAN: true)
   ├── publish_status (TINYINT: 1)
   └── timestamps

6. salon_offers
   ├── id (BIGINT PK)
   ├── site_id (BIGINT FK -> sites.id)
   ├── title (VARCHAR: "First Visit")
   ├── promo_code (VARCHAR: "XSALON15")
   ├── discount_type (ENUM: percentage, fixed)
   ├── discount_value (DECIMAL: 15.00)
   ├── how_it_works (JSON: ["1. Copy code", "2. Choose service", "3. Paste at checkout"])
   ├── terms_text (TEXT)
   ├── starts_at (DATETIME)
   ├── expires_at (DATETIME NULLABLE)
   └── timestamps
```

---

## 4. HashtagCMS Admin Panel Modules (10 Modules)

| Module ID | Controller Route | Display Name | Icon | Management Scope |
|---|---|---|---|---|
| `70` | `salon/services` | Services & Catalog | `fa fa-scissors` | Haircut, color, facial, spa services, duration, and base pricing. |
| `71` | `salon/stylists` | Stylists & Staff | `fa fa-user-circle` | Stylist portraits, titles, bio quotes, experience, ratings, and specialties. |
| `72` | `salon/schedules` | Rosters & Shifts | `fa fa-calendar-check-o` | Working hours, breaks, holidays, and 30/60 min appointment slots. |
| `73` | `salon/bookings` | Bookings & Schedule | `fa fa-calendar` | Master appointment diary, client history, reschedule, and cancellation. |
| `74` | `salon/offers` | Offers & Promo Codes | `fa fa-tag` | Discount codes, validity, terms & conditions, and usage analytics. |
| `75` | `salon/memberships`| Membership Tiers | `fa fa-crown` | Subscription plans (*Essentiel / Atelier*), perk checklists, and pricing. |
| `76` | `salon/loyalty` | Loyalty & Points | `fa fa-star` | Points earning rates, tier thresholds, and customer balances. |
| `77` | `salon/locations` | Studios & Branches | `fa fa-map-marker` | Multi-studio branches, addresses, contact info, and tax rates. |
| `78` | `salon/preferences`| Onboarding Curation | `fa fa-sliders` | Category tiles (*Hair, Color, Skin, Nails, Spa*) for home feed curation. |
| `79` | `salon/theme` | Theme & Design Tokens | `fa fa-paint-brush` | Live color picker, typography selection, button radius, and logo upload. |

---

## 5. Mobile SDUI UI Kit Components (`hashtagcms-ui-salon` - 15 Modules)

| # | SDUI Module Key (`viewType`) | Corresponding Screen | Composable Features |
|---|---|---|---|
| **1** | `salon_next_ritual_banner` | **01 - Home** | Next appointment hero banner (*"Balayage with Juno - In 7 days"*) with Details & Message actions. |
| **2** | `salon_category_nav` | **01 - Home / 03 - Book** | Horizontal category filter pills (*Hair, Skin, Nails, Spa, Grooming*). |
| **3** | `salon_signature_rituals` | **01 - Home** | Horizontal service cards with duration, starting price, and booking route. |
| **4** | `salon_service_card` | **03 - Booking (Step 1)** | Service list item with category icon, title, duration badge, and select action. |
| **5** | `salon_stylist_card` | **03 - Booking (Step 2)** | Stylist card with portrait, title, 4.9★ rating, years experience, and availability badges. |
| **6** | `salon_slot_picker` | **03 - Booking (Step 3)** | Horizontal date strip calendar + Morning & Afternoon time chip grid with booked states. |
| **7** | `salon_booking_review_card` | **03 - Booking (Step 4)** | Booking summary card with service details, location, stylist, promo code input row, and confirm CTA. |
| **8** | `salon_confirmation_card` | **03 - Booking (Step 5)** | Full-screen celebratory confirmation view with checkmark icon, confirmation ID, and *"Add to Calendar"*. |
| **9** | `salon_stylist_profile` | **04 - Stylist Detail** | Stylist hero header, bio quote, rating stats, specialty tags, message action, and *"Book with Stylist"* CTA. |
| **10** | `salon_points_card` | **02 - Offers & Rewards** | Points balance card (*2,480 pts*), progress bar to next tier, and loyalty rewards summary. |
| **11** | `salon_offer_promo_card` | **02 - Offers & Rewards** | Curated promo cards with discount percentage badge (*-15%*), promo code (*XSALON15*), and *"Apply"* action. |
| **12** | `salon_membership_tier_card`| **06 - Subscription** | Dark luxury membership tier cards (*Essentiel / Atelier*), monthly/annual toggle, and *"Start membership"* CTA. |
| **13** | `salon_appointment_card` | **05 - My Bookings** | Appointment history cards (Upcoming & Past), date/time badges, service price, and *"Reschedule"* / *"Cancel"* actions. |
| **14** | `salon_profile_stats_card` | **07 - Profile** | Client avatar, membership status banner, quick stats grid (Visits: 24, Points: 2,480, Stylists: 3), and account menu. |
| **15** | `salon_onboarding_picker` | **Onboarding / Preferences** | Interactive multi-select category grid (*What are you here for?*) with selection states. |

---

## 6. Server-Driven Workflows (`hashtagcms/workflows`)

| Workflow Alias | Input Payload Contract | Actions & Emitted SDUI Directives |
|---|---|---|
| `WORKFLOW_VERIFY_OTP` | `{email, code}` | Verifies OTP $\rightarrow$ issues Sanctum token $\rightarrow$ `navigate("/preferences")`, `trigger_haptic("success")` |
| `WORKFLOW_SAVE_PREFERENCES` | `{categories: ["hair", "color"]}` | Saves client category interests $\rightarrow$ `navigate("/home")` |
| `WORKFLOW_APPLY_PROMO` | `{promo_code: "XSALON15", subtotal: 240}` | Validates promo $\rightarrow$ computes discount $\rightarrow$ `mutate_cart(discount: 11)`, `toast("Code applied!")` |
| `WORKFLOW_CONFIRM_BOOKING` | `{service_id, stylist_id, date, time, location_id}` | Verifies slot lock $\rightarrow$ creates booking $\rightarrow$ `navigate("/booking-confirmed")`, `trigger_haptic("heavy")` |
| `WORKFLOW_RESCHEDULE_BOOKING` | `{booking_id, new_date, new_time}` | Updates booking date/time slot $\rightarrow$ `toast("Appointment updated")`, `mutate_booking_state` |
| `WORKFLOW_CANCEL_BOOKING` | `{booking_id}` | Cancels booking & triggers refund $\rightarrow$ `toast("Appointment cancelled")`, `mutate_booking_state` |
| `WORKFLOW_START_MEMBERSHIP` | `{membership_id, interval: "monthly"}` | Creates recurring billing $\rightarrow$ upgrades user tier $\rightarrow$ `toast("Welcome to Atelier!")`, `navigate("/profile")` |

---

## 7. Distribution & Rollout Strategy

1. **Universal App (Recommended for Zero App Store Overhead)**:
   - Single app on App Store / Google Play.
   - Dynamic deep-link or QR code (`app.link?site=xsalon`) dynamically configures the app theme, services, and branding on first launch.
   - Onboard 500 salons instantly from the Admin Panel without waiting for Apple/Google app review!
2. **Automated Branded App Releases**:
   - Parameterized Gradle CI/CD pipeline (`./gradlew assembleRelease -PsiteContext=xsalon -PappName="xSalon"`) for enterprise salon chains that require their own dedicated App Store listing.
