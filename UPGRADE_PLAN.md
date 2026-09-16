# UPGRADE PLAN — API-Only Backend + React Frontend

Turns the current Laravel 8 Blade app into a JSON API backend with a separate React SPA.
Work happens one milestone at a time, each milestone mapped to an existing `resources/views/*`
folder. Old Blade screens stay live until their React replacement is verified, then the
Blade views are removed (strangler approach).

## 1. Target Architecture

```
Browser ── React SPA (client/) ── HTTP/JSON (fetch + axios) ── Laravel API (/api/v1) ── MySQL
                                   │                                   │
                                   │ dev: Vite dev server              └─ dompdf (PDF), milon (barcodes),
                                   │      (node container,             │   Mail (SMTP), Lang (i18n)
                                   │      proxies /api to app container)│
                                   └ prod: npm run build ──> static assets served by Apache
```

- Backend keeps all business logic, validation, PDF, barcodes, mail, and translations.
- Frontend renders UI only; it never queries the DB directly and holds no business rules.
- Customers (`Customer` model) stay as data only — they do not log into the app.
  Users (`users` table) remain the only logins (roles `admin` / `staff`).

## 2. Technology Decisions (for review)

| Concern          | Choice                      | Why |
|------------------|-----------------------------|-----|
| API auth         | Laravel Sanctum (token SPAs) | Laravel 8 native; no oauth server needed; simpler than Passport |
| Frontend scaffold| React 18 + Vite + React Router v6 + axios | Fast build, current standard, small footprint |
| State            | Context + hooks (no Redux)  | App screens are form/table heavy, not state-heavy |
| Styling          | Bootstrap 4 (port existing look, not bootstrap-paginator hacks) | Keep visual continuity with current UI |
| PDF/barcode/mail | Stay on backend as API endpoints returning binary/data URIs | dompdf + milon are PHP-only |
| i18n             | Backend Laravel `Lang`; API returns translated strings (or `en` keys) | Existing lang files reused |
| Dev serving      | New `client` service added to docker-compose (node image) running Vite, proxying `/api` → app | Everything stays in Docker |
| Deprecations     | Remove `laravel/ui`, laravel-mix, `resources/views`, most `web.php` routes after each milestone | Final cleanup milestone |

## 3. API Conventions

- Base path `/api/v1`. JSON everywhere; auth via `Authorization: Bearer <token>` (Sanctum).
- Response envelope: success → `{ "data": …, "message": … }`; validation error → `422`
  with `{ "message", "errors": { field: [] } }`; auth errors → `401`; forbidden → `403`.
- Routes keep the same middleware intent:
  - `auth:sanctum` replaces the `verified` gate for every private route.
  - `admin` check becomes a small middleware/authorization guard (same `role === 'admin'` rule).
  - `locale` middleware reads `Accept-Language` header (falls back to DB `language` setting).
  - `notification` middleware moves from `View::share` to data on `/api/v1/notifications`.
- Soft-delete behaviour preserved: `PUT /…/{id}/delete` (soft), `PUT /…/{id}/restore`,
  `DELETE /…/{id}` (hard, admin) — keep current route semantics so frontend logic is 1:1.
- PDF endpoints return `application/pdf` (print invoice, receipt, repair receipt, reports).
- Barcode scan endpoints (`POST /barcode`, `POST /barcode/invoice`, `POST /barcode/repair`)
  are ported unchanged as POST endpoints.

## 4. Milestones (1 per views folder)

Order follows dependencies (customers before repairs/invoices; settings feeds invoice/repair pages).

### M1 — Foundation: Auth + Shell (`auth/`, `layouts/`, `welcome`, `home.blade.php`)
- API: install Sanctum; create `AuthController` (login/register/logout/me), email-verify +
  resend, password forgot/reset; dashboard stats endpoint (ports `AdminController::index`
  aggregates exactly). New `api.php` route groups + `ApiResponse`/error handling.
- Frontend: scaffold `client/` (Vite + Router + axios instance with token interceptor);
  `AuthContext` + protected routes; Login, Register, Verify, Forgot/Reset password pages;
  app shell (topbar/sidebar/branding from company settings). Replace `auth/` + `layouts/admin`
  + `home`.
- Remove: `auth/*`, `layouts/*`, `home.blade.php`, the parts of `web.php` for login/register/
  verify/reset + `/home`.

### M2 — Customers (`customer/`)
- API: `CustomerController` port — index/search, create, show, update, soft-delete,
  restore, destroy (admin); public signup endpoint (from `PublicController::public_new_customer`,
  creates Customer + Notification).
- Frontend: Customers list (table + search), Customer form, Customer detail view, trash actions.
- Remove: `customer/*`, `customer-sign-up.blade.php`, `welcome.blade.php`.
  > Deferred until M3/M4: `index-customer` routes (task=repair/invoice picker) and the public
  > sign-up page are still depended on by the live Blade repair/invoice create flows and by the
  > public sign-up form (no React sign-up page yet). Customer Blade views/routes stay reachable
  > meanwhile; full `web.php` + `views` cleanup happens once those dependents are ported.

### M3 — Repairs (`repair/`)
- API: repairs index/search (grouped by status), create form options, create/update, show,
  soft-delete/restore/destroy, print receipt (PDF), mail receipt, repair items
  (create/delete), repair settings CRUD, update-customer-repair.
- Frontend: Repairs list grouped by status, Create/Edit Repair (with items + customer picker),
  Repair detail, Print/Email, Setting-Manager page.
- Remove: `repair/*`.

### M4 — Invoices (`invoice/`)
- API: invoices index/search (grouped by status), create (from repair / wizard), show,
  update, print (invoice + thermal receipt as PDF), email, soft-delete/restore/destroy,
  invoice items CRUD (+ create-from-repair), invoice settings CRUD, update-customer-invoice.
- Frontend: Invoices list, Create/Edit Invoice (line items + repair items), Invoice detail,
  Print/Email, Settings page. Barcode scan-to-add (`POST /barcode/invoice`) wired here.
- Remove: `invoice/*`.

### M5 — Payments (`payment/`)
- API: payments index/search, create (for invoice — recomputes invoice balance), show,
  update (admin), delete (admin).
- Frontend: Payments list, Payment form, Payment detail. Payment actions surface from
  Invoice detail too.
- Remove: `payment/*`.

### M6 — Reports (`report/`)
- API: create report options, get report (date-range over invoices/repairs/payments),
  print report (PDF); register-report create/get-print/insert-data.
- Frontend: Report builder + printable preview (embedded PDF/link), Register report screen.
- Remove: `report/*`.

### M7 — Settings (`setting/`)
- API: get settings (grouped), update general + company profile settings (admin).
- Frontend: Settings pages (general + business profile).
- Remove: `setting/*`.

### M8 — Inventory (`inventory/`)
- API: categories CRUD; products CRUD/search/view; transactions index/search/create/update/
  cancel/delete; restock, sell, quick-sell; product barcode generation/scan.
- Frontend: Category manager, Product list/detail/form, Transaction index/view, Restock/Sell/
  Quick-sell actions, barcode scanning added here and to prior milestones as needed.
- Remove: `inventory/*`, and the standalone `components/*` barcode views.

### M9 — Logs (`log/`)
- API: logs index/search (admin).
- Frontend: Logs table with filters.
- Remove: `log/*`.

### M10 — Trash (`trash/`)
- API: trash index/search (all soft-deleted records, with action targets).
- Frontend: Trash screen with restore/destroy per entity type.
- Remove: `trash/*`.

### M11 — Users & Profile (`user/`)
- API: profile + change password; users list/update/delete (admin).
- Frontend: My Profile, Manage Users screen.
- Remove: `user/*`.

### M12 — Email templates + final cleanup (`emails/`, cross-cutting)
- `emails/` stays server-side (Mailables render Blade) — only re-verified/tested.
- Remove remaining `web.php` app routes, `laravel/ui`, laravel-mix + vue, `resources/views`
  leftovers, JS/CSS mix assets; tighten CORS to the built frontend origin; add Sanctum
  config; document production build/serve; run full test suite + manual smoke of every
  screen against the API.

## 5. Cross-Cutting Rules

- **Per-milestone verification (required before moving on):**
  1. Port endpoints → smoke-test each with curl/integration test inside the container.
  2. Build React page → verify every action (create/update/delete/search/print/email).
  3. Delete that feature's Blade views + web routes (keep old commit reachable in git).
  4. Run `docker exec repair-business-app ./vendor/bin/phpunit`.
- Every milestone = a mergeable, runnable state (both old Blade and new React can coexist
  during a milestone).
- Business logic is ported, not redesigned: preserve validation rules, decimal/tax math
  (invoice subtotal/tax/total/balance), barcode stock math, and soft-delete semantics exactly.
- No secrets in the repo; `/api` never returns passwords/tokens beyond the login token.

## 6. Backend Work To Reuse / New

- New `app/Http/Controllers/Api/V1/` controllers per module, reusing existing Models and
  business rules (or adapt existing controllers to return JSON — prefer new Api controllers
  to avoid churn on eventual deletion).
- `RouteServiceProvider` keeps `api.php` loaded; register `/api/v1` prefix + Sanctum middleware.
- API validation: keep `$request->validate(...)` rules; on failure return 422 envelope.

## 7. Risks / To Decide (needs your call)

1. **Auth for API:** Sanctum token auth (recommended). Confirm you don't want Passport.
2. **Customer login:** currently customers never log in. Keep it that way? (Recommended: yes.)
3. **Sidebar position** (`set-sidebar-position`): currently a session route. In React this
   becomes `localStorage` — no backend endpoint needed. OK?
4. **PDF/print UX:** print screens currently open a printable page. With React we will
   request the PDF (blob) and open in a new tab / downloadable. Acceptable?
5. **Frontend serving:** dev via Vite-proxy; production static files served by the existing
   Apache container (as a separate `client` build output). Acceptable?
6. **Verify each milestone in order M1→M12** with me, or do you want to batch any?

## 8. Deliverables Per Milestone

- API controller(s) + routes + tests
- React page(s) + components
- Deleted Blade views/routes for that feature  (except M1/M12)
- Update to `REQUIREMENTS.md` and `AGENTS.md` as instructions change