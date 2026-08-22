# Master Plan & Architecture Specification: White-Label Multi-Tenant Salon Platform (`xSalon`)

> **Master Engineering & Product Specification**  
> **Workspace Root:** `/Users/marghoobsuleman/www/suleman/projects-n-products/focused/business/xsalon`  
> **Backend App:** `/Users/marghoobsuleman/www/suleman/projects-n-products/focused/business/xsalon/backend/admin-panel`  
> **Frontend KMP:** `/Users/marghoobsuleman/www/suleman/projects-n-products/focused/business/xsalon/frontend/xsalon-mobile`  
> **Schema Standard:** HashtagCMS Multi-Tenant (`site_id`) & Multilingual (`_langs` tables with `timestamps` & `softDeletes`)

---

## 1. Executive Summary & Vision

The goal of the **xSalon Platform** is to provide a commercial-grade, multi-tenant appointment booking, membership, and rewards mobile platform powered by **HashtagCMS**. The architecture allows selling the platform to **hundreds of independent salon brands** (e.g. *xSalon Atelier, Glow & Co., Barber Republic, Pastel Nails*) with:

1. **Zero-Code Custom Theming**: Each salon can customize their primary colors, secondary accents, backgrounds, typography, corner radii, and logos dynamically from the HashtagCMS Admin Panel without mobile app rebuilds.
2. **Multi-Tenant & Multilingual Data Isolation**: Complete separation of services, stylists, rosters, appointments, promo codes, and payment gateways per salon using HashtagCMS `site_id` and translatable `_langs` tables.
3. **Server-Driven Booking Funnel**: Dynamic 2-to-5 step booking flows (Service $\rightarrow$ Stylist $\rightarrow$ Date/Time $\rightarrow$ Review $\rightarrow$ Confirmation) adaptable per salon business model.
4. **Zero-Binary Dynamic Primitives**: Marketing teams can publish promotional banners and seasonal offer cards directly from CMS JSON.

---

## 2. HashtagCMS Database Schema Standards

Every entity strictly follows the official **HashtagCMS schema pattern**:
- **Main Table**: Contains structural & numerical fields, `site_id` FK (on delete cascade), `insert_by`, `update_by`, `publish_status`, `timestamps()`, and `softDeletes()`.
- **`_langs` Table**: Contains translatable text fields with composite primary key `[entity_id, lang_id]`, `timestamps()`, and `softDeletes()`.

```
+----------------------------------------------------------------------------------------------------+
|                                  HASHTAGCMS SALON DATABASE SCHEMA                                  |
+----------------------------------------------------------------------------------------------------+

1. salon_services (Structural / Pricing)
   ├── id (BIGINT UNSIGNED PK)
   ├── site_id (BIGINT UNSIGNED FK -> sites.id ON DELETE CASCADE)
   ├── category_id (BIGINT UNSIGNED NULLABLE FK -> categories.id)
   ├── duration_minutes (INT: 120)
   ├── base_price (DECIMAL 10,2: 180.00)
   ├── image_url (VARCHAR 255 NULLABLE)
   ├── is_signature (TINYINT: 1)
   ├── insert_by (BIGINT UNSIGNED)
   ├── update_by (BIGINT UNSIGNED NULLABLE)
   ├── publish_status (TINYINT: 1)
   ├── timestamps()
   └── softDeletes()

   salon_service_langs (Translatable Content)
   ├── service_id (BIGINT UNSIGNED FK -> salon_services.id ON DELETE CASCADE)
   ├── lang_id (BIGINT UNSIGNED FK -> langs.id ON DELETE CASCADE)
   ├── name (VARCHAR 128: "Balayage + Gloss")
   ├── title (VARCHAR 128 NULLABLE)
   ├── description (TEXT NULLABLE)
   ├── timestamps()
   ├── softDeletes()
   └── PRIMARY KEY (service_id, lang_id)

──────────────────────────────────────────────────────────────────────────────────────────────────────

2. salon_stylists (Structural / Ratings)
   ├── id (BIGINT UNSIGNED PK)
   ├── site_id (BIGINT UNSIGNED FK -> sites.id ON DELETE CASCADE)
   ├── user_id (BIGINT UNSIGNED NULLABLE FK -> users.id)
   ├── experience_years (INT: 9)
   ├── rating (DECIMAL 3,2: 4.90)
   ├── review_count (INT: 284)
   ├── starting_price (DECIMAL 10,2: 180.00)
   ├── photo_url (VARCHAR 255 NULLABLE)
   ├── specialties (JSON NULLABLE: ["Balayage", "Color correction", "Gloss"])
   ├── insert_by (BIGINT UNSIGNED)
   ├── update_by (BIGINT UNSIGNED NULLABLE)
   ├── publish_status (TINYINT: 1)
   ├── timestamps()
   └── softDeletes()

   salon_stylist_langs (Translatable Profile)
   ├── stylist_id (BIGINT UNSIGNED FK -> salon_stylists.id ON DELETE CASCADE)
   ├── lang_id (BIGINT UNSIGNED FK -> langs.id ON DELETE CASCADE)
   ├── name (VARCHAR 128: "Juno Okafor")
   ├── title (VARCHAR 128: "Senior Colourist")
   ├── bio_quote (TEXT NULLABLE: "Trained in Paris and Tokyo...")
   ├── timestamps()
   ├── softDeletes()
   └── PRIMARY KEY (stylist_id, lang_id)

──────────────────────────────────────────────────────────────────────────────────────────────────────

3. salon_schedules (Weekly Working Shifts & Slots)
   ├── id (BIGINT UNSIGNED PK)
   ├── site_id (BIGINT UNSIGNED FK -> sites.id ON DELETE CASCADE)
   ├── stylist_id (BIGINT UNSIGNED FK -> salon_stylists.id ON DELETE CASCADE)
   ├── day_of_week (TINYINT: 1=Mon, 7=Sun)
   ├── shift_start (TIME: 09:00:00)
   ├── shift_end (TIME: 19:00:00)
   ├── slot_interval_minutes (INT: 30)
   ├── is_active (TINYINT: 1)
   ├── insert_by (BIGINT UNSIGNED)
   ├── update_by (BIGINT UNSIGNED NULLABLE)
   ├── timestamps()
   └── softDeletes()

──────────────────────────────────────────────────────────────────────────────────────────────────────

4. salon_bookings (Transactional Appointments)
   ├── id (BIGINT UNSIGNED PK)
   ├── site_id (BIGINT UNSIGNED FK -> sites.id ON DELETE CASCADE)
   ├── confirmation_code (VARCHAR 64: "#A48521")
   ├── user_id (BIGINT UNSIGNED FK -> users.id)
   ├── stylist_id (BIGINT UNSIGNED NULLABLE FK -> salon_stylists.id)
   ├── service_id (BIGINT UNSIGNED FK -> salon_services.id)
   ├── location_id (BIGINT UNSIGNED FK -> salon_locations.id)
   ├── booking_date (DATE: 2026-04-30)
   ├── start_time (TIME: 14:00:00)
   ├── end_time (TIME: 15:40:00)
   ├── total_amount (DECIMAL 10,2: 240.00)
   ├── discount_amount (DECIMAL 10,2: 11.00)
   ├── final_amount (DECIMAL 10,2: 229.00)
   ├── status (ENUM: pending, confirmed, completed, cancelled, rescheduled)
   ├── promo_code (VARCHAR 64 NULLABLE)
   ├── payment_status (ENUM: unpaid, deposit_paid, paid_full)
   ├── insert_by (BIGINT UNSIGNED)
   ├── update_by (BIGINT UNSIGNED NULLABLE)
   ├── timestamps()
   └── softDeletes()

──────────────────────────────────────────────────────────────────────────────────────────────────────

5. salon_memberships (Subscription Tiers)
   ├── id (BIGINT UNSIGNED PK)
   ├── site_id (BIGINT UNSIGNED FK -> sites.id ON DELETE CASCADE)
   ├── slug (VARCHAR 64: "atelier")
   ├── monthly_price (DECIMAL 10,2: 129.00)
   ├── annual_price (DECIMAL 10,2: 1315.00)
   ├── perks (JSON NULLABLE: ["2 signature services/mo", "15% off everything"])
   ├── is_most_chosen (TINYINT: 1)
   ├── insert_by (BIGINT UNSIGNED)
   ├── update_by (BIGINT UNSIGNED NULLABLE)
   ├── publish_status (TINYINT: 1)
   ├── timestamps()
   └── softDeletes()

   salon_membership_langs (Translatable Plan Details)
   ├── membership_id (BIGINT UNSIGNED FK -> salon_memberships.id ON DELETE CASCADE)
   ├── lang_id (BIGINT UNSIGNED FK -> langs.id ON DELETE CASCADE)
   ├── name (VARCHAR 128: "Atelier")
   ├── title (VARCHAR 128 NULLABLE)
   ├── description (TEXT NULLABLE)
   ├── timestamps()
   ├── softDeletes()
   └── PRIMARY KEY (membership_id, lang_id)

──────────────────────────────────────────────────────────────────────────────────────────────────────

6. salon_offers (Discounts & Promos)
   ├── id (BIGINT UNSIGNED PK)
   ├── site_id (BIGINT UNSIGNED FK -> sites.id ON DELETE CASCADE)
   ├── promo_code (VARCHAR 64: "XSALON15")
   ├── discount_type (ENUM: percentage, fixed)
   ├── discount_value (DECIMAL 10,2: 15.00)
   ├── how_it_works (JSON NULLABLE)
   ├── starts_at (TIMESTAMP NULLABLE)
   ├── expires_at (TIMESTAMP NULLABLE)
   ├── insert_by (BIGINT UNSIGNED)
   ├── update_by (BIGINT UNSIGNED NULLABLE)
   ├── publish_status (TINYINT: 1)
   ├── timestamps()
   └── softDeletes()

   salon_offer_langs (Translatable Offer Text)
   ├── offer_id (BIGINT UNSIGNED FK -> salon_offers.id ON DELETE CASCADE)
   ├── lang_id (BIGINT UNSIGNED FK -> langs.id ON DELETE CASCADE)
   ├── title (VARCHAR 128: "First Visit")
   ├── description (TEXT NULLABLE)
   ├── terms_text (TEXT NULLABLE)
   ├── timestamps()
   ├── softDeletes()
   └── PRIMARY KEY (offer_id, lang_id)

──────────────────────────────────────────────────────────────────────────────────────────────────────

7. salon_locations (Studios & Branches)
   ├── id (BIGINT UNSIGNED PK)
   ├── site_id (BIGINT UNSIGNED FK -> sites.id ON DELETE CASCADE)
   ├── phone (VARCHAR 32 NULLABLE)
   ├── email (VARCHAR 128 NULLABLE)
   ├── latitude (DECIMAL 10,8 NULLABLE)
   ├── longitude (DECIMAL 11,8 NULLABLE)
   ├── insert_by (BIGINT UNSIGNED)
   ├── update_by (BIGINT UNSIGNED NULLABLE)
   ├── publish_status (TINYINT: 1)
   ├── timestamps()
   └── softDeletes()

   salon_location_langs (Translatable Location Info)
   ├── location_id (BIGINT UNSIGNED FK -> salon_locations.id ON DELETE CASCADE)
   ├── lang_id (BIGINT UNSIGNED FK -> langs.id ON DELETE CASCADE)
   ├── name (VARCHAR 128: "xSalon Atelier Mulberry")
   ├── address (VARCHAR 255: "124 Mulberry St")
   ├── city (VARCHAR 64: "New York")
   ├── timestamps()
   ├── softDeletes()
   └── PRIMARY KEY (location_id, lang_id)
```

---

## 3. Ten HashtagCMS Admin Panel Modules (`backend/admin-panel`)

| Module ID | Route | Display Name | Management Scope |
|---|---|---|---|
| `70` | `salon/services` | Services & Catalog | Service catalog, duration, base pricing, and category tagging (with language tabs). |
| `71` | `salon/stylists` | Stylists & Staff | Stylist portraits, titles, bio quotes, experience, ratings, and specialties. |
| `72` | `salon/schedules` | Rosters & Shifts | Working hours, weekly shifts, and 30/60 min appointment slots. |
| `73` | `salon/bookings` | Bookings & Schedule | Master appointment diary, client history, reschedule, and cancellation. |
| `74` | `salon/offers` | Offers & Promo Codes | Discount codes (`XSALON15`), terms, validity, and usage limits. |
| `75` | `salon/memberships`| Membership Tiers | Subscription plans (*Essentiel / Atelier*), perks, and pricing. |
| `76` | `salon/loyalty` | Loyalty & Points | Points earning rates, tier thresholds, and customer balances. |
| `77` | `salon/locations` | Studios & Branches | Multi-studio branches, addresses, contact info, and tax rates. |
| `78` | `salon/preferences`| Onboarding Curation | Category tiles (*Hair, Color, Skin, Nails, Spa*) for home feed curation. |
| `79` | `salon/theme` | Theme & Design Tokens | Live color picker, typography selection, button radius, and logo upload. |

---

## 4. Frontend SDUI View Modules (`frontend/xsalon-mobile` - 15 Modules)

| # | SDUI Module Key (`viewType`) | Corresponding Screen | Composable Features |
|---|---|---|---|
| **1** | `salon_next_ritual_banner` | **01 - Home** | Next appointment hero banner (*"Balayage with Juno - In 7 days"*) with Details & Message actions. |
| **2** | `salon_category_nav` | **01 - Home / 03 - Book** | Horizontal category filter pills (*Hair, Skin, Nails, Spa, Grooming*). |
| **3** | `salon_signature_rituals` | **01 - Home** | Horizontal service catalog cards with price and duration. |
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

## 5. Server-Driven Workflows (`backend/admin-panel`)

| Workflow Alias | Input Payload Contract | Actions & Emitted SDUI Directives |
|---|---|---|
| `WORKFLOW_VERIFY_OTP` | `{email, code}` | Verifies OTP $\rightarrow$ issues Sanctum token $\rightarrow$ `navigate("/preferences")`, `trigger_haptic("success")` |
| `WORKFLOW_SAVE_PREFERENCES` | `{categories: ["hair", "color"]}` | Saves client category interests $\rightarrow$ `navigate("/home")` |
| `WORKFLOW_APPLY_PROMO` | `{promo_code: "XSALON15", subtotal: 240}` | Validates promo $\rightarrow$ computes discount $\rightarrow$ `mutate_cart(discount: 11)`, `toast("Code applied!")` |
| `WORKFLOW_CONFIRM_BOOKING` | `{service_id, stylist_id, date, time, location_id}` | Verifies slot $\rightarrow$ creates booking $\rightarrow$ `navigate("/booking-confirmed")`, `trigger_haptic("heavy")` |
| `WORKFLOW_RESCHEDULE_BOOKING` | `{booking_id, new_date, new_time}` | Updates booking date/time slot $\rightarrow$ `toast("Appointment updated")`, `mutate_booking_state` |
| `WORKFLOW_CANCEL_BOOKING` | `{booking_id}` | Cancels booking & triggers refund $\rightarrow$ `toast("Appointment cancelled")`, `mutate_booking_state` |
| `WORKFLOW_START_MEMBERSHIP` | `{membership_id, interval: "monthly"}` | Creates recurring subscription $\rightarrow$ `toast("Welcome to Atelier!")`, `navigate("/profile")` |
