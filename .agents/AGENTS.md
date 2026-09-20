# Casjoe SaaS Project Rules

## LOCKED FILES - DO NOT MODIFY
The following files and features are strictly **LOCKED** to preserve core platform functionality, landing page routing, and Google Authentication (One Tap + OAuth). Agents must **NEVER** modify, overwrite, break, or delete these files, routes, or configurations without EXPLICIT and REPEATED confirmation from the user overriding this lock:

### 1. Landing Page & Root Routing
- `app/Views/landing.php` (The landing page view)
- `app/Core/Controllers/LandingController.php` (The landing page logic)
- The routing mapping for `/` in `app/Core/bootstrap.php`. It MUST remain pointing to `[\App\Core\Controllers\LandingController::class, 'index']`. If a user asks to modify the dashboard or any other module, **DO NOT** accidentally map the root `/` to it. The root `/` belongs exclusively to the landing page.

### 2. Google Login & Google One Tap (`g_id_onload` / GIS)
- `app/Core/Controllers/AuthController.php` (The Google OAuth and One Tap authentication logic (`onetap` handling, JWT verification, session creation))
- `app/Core/Views/auth/login.php` (The login page view containing `#g_id_onload`, `.g_id_signin`, `.btn-google`, and `.page-wrapper` CSS architecture)
- `app/Core/Views/auth/register.php` (The registration page view containing `#g_id_onload`, `.g_id_signin`, `.btn-google`, and `.page-wrapper` CSS architecture)
- The Google routing mappings (`/auth/google` and `/auth/google/onetap`) in `app/Core/bootstrap.php`.
- The Google OAuth constants (`GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET`) defined in `app/Core/bootstrap.php`.

**CRITICAL INSTRUCTION FOR ALL AGENTS:**
1. **Landing Page Protection:** Any future task must NEVER alter, overwrite, or delete `app/Views/landing.php` or `app/Core/Controllers/LandingController.php`, and must NEVER remap the root `/` route in `bootstrap.php` away from `LandingController::class`.
2. **Google Authentication Protection:** Any future task involving UI adjustments, new modules, or platform modifications must NEVER alter or break the Google One Tap popup prompt (`g_id_onload`), the visible Google login buttons, or the isolated `.page-wrapper` flex architecture on `login.php` and `register.php`. Both systems are strictly locked across all work.
3. **Super Admin & CMS Architecture Protection (Zero 404 Guarantee):**
   - All super admin routes (`/casper-joe/*`) and sub-routes registered in `app/Core/bootstrap.php` are strictly locked. Agents must NEVER remove, comment out, or unmap any admin routes, and must ensure all sidebar navigation items (`app/Core/Views/admin/sidebar.php`) point to valid, implemented controller methods.
   - Core static pages (`about-us`, `privacy-policy`, `terms-of-service`) in `cms_pages` are programmatically locked against deletion inside `AdminCmsController::deletePage` and auto-synchronized via `PageController`. Agents must NEVER allow these system pages to be deleted or return 404 errors.

### 4. AI Office System Protection
- `app/Core/Controllers/AIEmployeeController.php` (The AI Office logic and routing).
- `app/Core/Views/ai/office.php` and `app/Core/Views/ai/builder.php` (The AI Office UI and automation builder views).
- The routing mappings (`/ai-office`, `/ai-office/chat`, `/ai-office/queue/approve`, `/ai-office/queue/reject`, `/ai-office/automations/create`, `/ai-office/automations/delete`) in `app/Core/bootstrap.php`.
- The AI Agent classes in `app/Core/Services/AI/Employees/`.
- Agents must NEVER modify, overwrite, or delete these files, and must NEVER unmap the routes from `bootstrap.php`.

### 5. Onboarding & Dashboard UI Integrity
- `app/Core/Controllers/OnboardingController.php` (Specifically Step 5 filtering logic).
- `app/Core/Views/global_dashboard.php` (The floating theme widget and the ON/OFF pill switch JavaScript).
- `app/Core/Controllers/ModuleController.php` (The module toggle logic).
- Agents must NEVER overwrite or delete the module toggle scripts (`handleDashboardPillClick`, `toggleModule`, `toggleModuleCard`) or the theme floating widget script, as they are crucial for user experience.

### 6. CasjoeERP Routing Integrity
- `app/Modules/CasjoeERP/routes.php` (The module routing map).
- Agents must NEVER overwrite, delete, or comment out existing routes in this file. When adding new modules or fixing 404 errors (like goals, ai-manager, whatsapp), agents must ONLY append new routes without breaking existing mappings.

### 7. CasjoePay Module Integrity
- `app/Modules/CasjoePay/routes.php`
- `app/Modules/CasjoePay/Controllers/PayController.php`
- `app/Modules/CasjoePay/Controllers/CardController.php`
- `app/Modules/CasjoePay/Controllers/NairaCardController.php`
- `app/Modules/CasjoePay/Services/StroWalletService.php`
- `app/Modules/CasjoePay/Views/cards.php` (The virtual cards UI and the `revealCardDetails` JavaScript function)
- Agents must NEVER overwrite, break, or delete these files, or unmap the CasjoePay routes in `bootstrap.php`. The wallet funding URL logic and StroWallet API endpoints (virtual card creation, funding, termination) are strictly locked to preserve live production functionality.

**Card Details Display Architecture (LOCKED):**
- StroWallet's API returns `card_number: null` and `cvv: null` for security. It provides `card_number_url` and `cvv_url` which are HTML pages using a **browser-only SecureProxy** (`js.securepro.xyz`) that renders card digits client-side. Server-side extraction is **impossible by design** (the SecureProxy host does not resolve via DNS from server).
- `CardController::getCardDetails()` builds iframe HTML server-side (`card_number_html`, `cvv_html` fields) with `filter:invert(1)` for dark theme visibility. It NEVER returns `null` for `card_number` or `cvv` — always empty string `''` to prevent JavaScript displaying "undefined".
- `cards.php` `revealCardDetails()` uses `card_number_html` / `cvv_html` from the JSON response to inject iframes via `innerHTML`.
- Agents must NEVER: (1) attempt server-side cURL extraction of card numbers from SecureProxy URLs, (2) return `null` for `card_number`/`cvv` in `getCardDetails()`, (3) remove the `card_number_html`/`cvv_html` iframe approach, or (4) add debug alerts to `revealCardDetails()`.

### 8. CasjoeLinks Routing Integrity
- `app/Modules/CasjoeLinks/routes.php`
- `app/Modules/CasjoeLinks/Controllers/LinkResolverController.php`
- Agents must NEVER overwrite, delete, or comment out the public routing endpoints (like `/@{slug}` and `/bio/{slug}`) as these serve live public user profiles. Modifying these routes can cause widespread 404 errors for user bio pages.

### 9. Zero 404 Route Lock (All 9 Modules & Global Routes)
- All module route files across all 9 modules (`CasjoeAcademy`, `CasjoeCloud`, `CasjoeERP`, `CasjoeLinks`, `CasjoeMail`, `CasjoePay`, `CasjoeShop`, `CasjoeSmartForms`, `CasjoeSupport`) and their inclusion in `app/Core/bootstrap.php` are strictly **LOCKED**.
- **Permanent Registration**: All 9 module route files MUST remain unconditionally included in `app/Core/bootstrap.php`. Tenant module toggles control UI access within controllers/middleware, but routes themselves must NEVER be unmapped, commented out, or conditionalized in bootstrap, guaranteeing zero 404 errors.
- **Route Files Protected**:
  - `app/Modules/CasjoeAcademy/routes.php`
  - `app/Modules/CasjoeCloud/routes.php`
  - `app/Modules/CasjoeERP/routes.php`
  - `app/Modules/CasjoeLinks/routes.php`
  - `app/Modules/CasjoeMail/routes.php`
  - `app/Modules/CasjoePay/routes.php`
  - `app/Modules/CasjoeShop/routes.php`
  - `app/Modules/CasjoeSmartForms/routes.php`
  - `app/Modules/CasjoeSupport/routes.php`
- **Method & View Integrity**:
  - Every route defined across these route files must point to an implemented method on its target controller.
  - Every link, form action, and AJAX endpoint in all view templates must have a corresponding route.
  - Agents must NEVER delete or rename existing route endpoints or controller methods that service active views.
