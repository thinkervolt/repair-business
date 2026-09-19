# HANDOFF — repair-business (Laravel 8 → API + React upgrade)

Last updated: 2026-09-19. Written to let a future session/agent resume cleanly.

## Ground rules (from AGENTS.md — read it)

- Everything runs in Docker. Never install on the host. No bare `php`/`composer`/`npm`/`mysql`.
- App work: `docker exec -w /var/www repair-business-app php artisan <cmd>`
- Tests: `docker exec -w /var/www repair-business-app ./vendor/bin/phpunit`
- Match original business logic; verify every ported endpoint; don't commit unless asked.

## Environment

| Container | Purpose | URL / host |
|---|---|---|
| `repair-business-app` | Laravel 8 + Apache | http://localhost:5050 |
| `repair-business-client` | Vite dev server (proxies `/api`) | http://localhost:5173 |
| `mysql_development` | MySQL 8.4 | `mysql_development:3306` (host: `127.0.0.1:3306`) |
| `phpmyadmin_development` | phpMyAdmin | http://localhost:9010 |

- Code is bind-mounted at `/var/www` (repo root) in the app container.
- `vendor/`, `public/`, `storage/`, `bootstrap/cache` are image-baked volumes — after
  `composer require` you must `docker compose build && docker compose up -d --force-recreate app`.
- Credentials live in `.env` (git-ignored). Seed admin login is `admin@admin.com` / `password`.
- `NETWORK_NAME=builder.net` (external network).

## Node / client gotchas (IMPORTANT)

- The client **must** be built with **Node 20**. `AGENTS.md` says node:16 — that is stale;
  node:16's npm cannot install Tailwind v4 / `@tailwindcss/oxide` native bindings.
- Rebuild vendor if needed:
  ```
  docker run --rm -v "$PWD/client":/app -w /app node:20 sh -c 'rm -rf node_modules package-lock.json && npm i'
  ```
- Production build check:
  ```
  docker run --rm -v "$PWD/client":/app -w /app node:20 sh -c 'npx vite build'
  ```
- The running dev container serves from the bind mount, so source edits hot-reload; no rebuild needed for source changes.

## File-permission gotcha

Earlier agent-created files are **root-owned**; host user `rustedchip` (uid 1000) can't write,
and `sudo` fails (no TTY). Fix with the app container (runs as uid 0):
```
docker exec repair-business-app chown 1000:1000 /var/www/<path>
```

## Repo state (branch `react`)

Committed:
- `bf71623 feat: inventory module API + React SPA (M8)`
- `71369d8 react :: customers-and-repairs`
- `748c2a8 react :: invoice-and-inventory` (M4 invoices; ships the `.m4p/` scratch dir)
- `bebd137 react :: invoice-payments-and-reports` (M5 payments + M6 reports + M7 settings +
  print/email letter redesign — everything that was "uncommitted" below is now committed)

Working tree (M9 logs + M10 trash + M11 users/profile): `?? app/Http/Controllers/Api/V1/{Log,Trash,User}Controller.php`, `?? client/src/pages/{logs/LogsList,trash/TrashList,users/Profile,users/UsersList}.jsx`, `M routes/api.php`, `M client/src/App.jsx`, `M resources/lang/{en,es}/repair-business.php`, `M HANDOFF.md`, `del client/src/pages/Placeholder.jsx`.

> Note: commit `748c2a8` (M4) includes the `.m4p/` scratch dir despite the earlier note to
> exclude it — it's committed, so delete it as a separate small commit if desired.

## Modules status

- M1 auth/shell — done & committed
- M2 customers — done & committed
- M3 repairs — done & committed
- M8 inventory — done & committed
- M4 invoices — implemented + smoke-tested, committed (`748c2a8`)
- M5 payments — implemented + smoke-tested, committed (`bebd137`)
- M6 reports — implemented + smoke-tested, committed (`bebd137`)
- M7 settings — implemented + smoke-tested, committed (`bebd137`)
- **M9 logs — implemented + smoke-tested, NOT committed**
- **M10 trash — implemented + smoke-tested, NOT committed**
- **M11 users/profile — implemented + smoke-tested, NOT committed**

## M10 trash — what exists

- `app/Http/Controllers/Api/V1/TrashController.php` (new) — 1:1 port of legacy
  `TrashController@index_trash`: soft-deleted (`active='no'`) customers, repairs, invoices.
  Search queries replicate the legacy builder-subquery logic exactly (customers by
  name/id/phone/email; repairs by target/request OR linked-customer; invoices by
  customer_name/email/phone/id OR linked invoice-setting name). Repairs eager-load
  `customer_data` (matches the Blade view usage). Returns all three lists + the search term.
- `routes/api.php` — `GET /api/v1/trash` (auth:sanctum + api.locale only — the legacy
  web route has NO `admin` middleware, unlike logs; admin enforcement happens on the React
  side via `AdminOnlyRoute` + the admin-gated destroy endpoints, same split as payments).
- `client/src/pages/trash/TrashList.jsx` (new) — admin-only `TrashList` wired at `/trash`
  replacing the Placeholder. Debounced search; single table (ID / Item / Data / Deleted /
  Actions) with customers→repairs→invoices rows (Blade order). Restore calls the existing
  `PUT /{customers|repairs|invoices}/{id}/restore`; Destroy calls the existing
  admin-gated `DELETE /{customers|repairs|invoices}/{id}` after a `window.confirm`.
  Per-row busy spinner; parity formatting (name/phone/email, `[Total] [Balance]`).
- Legacy `trash/index-trash.blade.php` + web routes kept live (strangler deferred to M12).
- Verified: `php -l` clean, only `api/v1/trash` in the API (legacy web routes still listed),
  phpunit 2/2, `vite build` passes, curl cycle for all three entities — empty trash → 0,
  soft-delete → appears (customer w/ data, repair w/ `customer_data`, invoice w/ totals),
  search matches, restore → leaves trash; final trash counts back to all-zero.


## M9 logs — what exists

- `app/Http/Controllers/Api/V1/LogController.php` (new) — 1:1 port of legacy
  `LogController@index_log`: search across `data` (LIKE) OR user name subquery, `user_data`
  eager-loaded, `OrderBy created_at DESC`, paginate 50. Admin-only.
- `routes/api.php` — `GET /api/v1/logs` (auth:sanctum + api.admin + api.locale).
- `client/src/pages/logs/LogsList.jsx` (new) — debounced search + table (Activity / User /
  When via `formatDateTime`) + Pagination. Null-safe `user_data?.name` (legacy Blaqde view
  would crash if the user was deleted). Wired in `App.jsx` behind `AdminOnlyRoute` (`/logs`).
- Legacy `log/index-log.blade.php` + web routes kept live (strangler deferred to M12).
- Verified: `php -l` clean, only `api/v1/logs` listed, phpunit 2/2, `vite build` passes,
  curl smoke — index ok (latest first, `user_data` present), search `admin` → 49,
  no-match search → 0.


## M11 users/profile — what exists

- `app/Http/Controllers/Api/V1/UserController.php` (new) — ports legacy `UserController`:
  - `GET /users/profile` (auth) — current user's name/email/role (legacy `profile`).
  - `PUT /users/profile/password` (auth) — port of `update_password`: validates
    `current_password`/`password`/`password_confirmation` (min 8), `Hash::check` current
    (else `error_current-password-does-not-match`, 422), confirms match (else
    `error_new-password-does-not-match`, 422), updates + **revokes the current token**
    (faithful stand-in for the legacy `Auth::logout()` after a password change — the SPA's
    401 interceptor then clears the session and sends the user to `/login`).
  - `GET /users` (admin) — id/name/email/role, `active='yes'`, excludes the caller (legacy `users`).
  - `PUT /users/{id}` (admin) — update name/email/role; `password` is now **nullable** — it
    is only re-hashed when provided (previously it was required and always reset the password).
    Message `error_user-has-been-updated`.
  - `PUT /users/{id}/password` (admin, new) — dedicated **reset password** endpoint
    (required `password` + `password_confirmation`, min 8); message `error_user-password-reset`.
  - `DELETE /users/{id}` (admin) — hard delete; message `error_user-has-been-deleted`.
- `routes/api.php` — 5 routes under `v1` (`users/profile` + `users/profile/password`
  registered before `users/{id}`). Profile/password are auth-only; index/update/destroy add
  `api.admin` (matches the legacy web middleware split).
- `resources/lang/{en,es}/repair-business.php` — added `error_password-has-been-updated`
  (`'Password has been Updated.'` / `'La contraseña ha sido actualizada.'`).
- React pages: `client/src/pages/users/{Profile,UsersList}.jsx`, wired in `App.jsx` —
  - `/profile` — account card (name/email/role) + change-password form; on success shows
    the message then clears the token and navigates to `/login` (legacy logout behaviour).
  - `/users` — admin-only, mirrors `user/users.blade.php`: inline editable rows
    (**name/email/role only — no password field**), Update (PUT) + Delete (DELETE with confirm).
    Each row has a separate **Reset password** button that opens a modal posting
    `PUT /users/{id}/password`. A **Create user**
    card sits on top of the page (name/email/role/password/confirm) and posts
    `POST /auth/register`, promoting to admin via `PUT /users/{id}` when the Admin role is
    selected. This replaced the old sidebar `Create User` → `/register` page (removed the
    `/register` route, `Register.jsx`, and unused `AuthContext.register` — user complained it
    "jumps to the Laravel register route and looks bad").
- `client/src/pages/Placeholder.jsx` deleted — every route is now a real page.
- Legacy `user/*` Blade views + web routes kept live (strangler deferred to M12).
- Verified: `php -l` clean (controller, routes, both lang files), 5 routes listed under
  `api/v1/users`, phpunit 2/2, `vite build` passes. Curl: register temp user (id 5) → admin
  profile correct → users list shows temp only → wrong current password 422 → mismatched
  confirm 422 → correct change 200 + old token 401 → admin update (rename/role/password) then
  login with new creds works → admin delete → list empty. Orphaned test token cleaned up.

## Parity audit (post-M11) — three gaps fixed

Full legacy-controller → API → React mapping done for every action. Three real behavioural
gaps were found and fixed (all uncommitted):

1. **Notifications were not clickable** — legacy `NotificationController@notification($id)`
   deletes the notification and redirects to its `route`+`ref` (only `view-customer` is ever
   written). Fixed: added `DELETE /api/v1/notifications/{id}`
   (`Api\V1\NotificationController@destroy` returns `{route, ref}`; 404 `error_not-found`
   when missing). `Topbar.jsx` notification rows are now clickable → optimistically remove,
   DELETE, then navigate via `NOTIFICATION_ROUTES` (`view-customer` → `/customers/{ref}`,
   plus repair/invoice/product/payment mappings).
2. **"Repairs not invoiced" was dead in React** — legacy dashboard links to
   `index-repair/no-invoice/0` (paperless-leaving repairs); the API already supported
   `task=no-invoice` but the SPA never used it. Fixed: added a **No invoice** chip in
   `RepairsList.jsx` (special-cases `filter === 'no-invoice'` → `task=no-invoice`) and the
   Dashboard "Repairs not invoiced" card now links to `/repairs?filter=no-invoice`.
3. **No global barcode scanner** — legacy layouts accept a handheld-scanner sweep from any
   admin screen and pop a result modal. Fixed: new `client/src/components/inventory/GlobalScanner.jsx`
   (mounted once in `AppLayout`) listens for fast key-bursts ending in Enter (ignored while an
   input is focused, so the InvoiceView scan modal still works), POSTs `/barcode`, and shows a
   result overlay: product-found → Open product / Quick sell; invoice-found → `/invoices/:id`;
   repair-found → `/repairs/:id`; barcode-not-found → "Create product with this barcode" →
   `/inventory/products/create?barcode=…`. `ProductForm.jsx` now prefills the barcode field
   from the `?barcode=` query param.

Audit conclusions recorded (NOT bugs): Dashboard = 1:1 port of `AdminController@index`
(React adds only a recharts chart of the same monthly earnings); Inventory `restock` covers
legacy `inventory_create_transaction`; `quickSell` replicates legacy `company_address = email`
bug bug-for-bug; legacy web routes `update-company-setting` + `print-register-report` reference
non-existent methods (500 if hit, not real views); `AppController@set_sidebar_position` replaced
by Topbar collapse (deferred by design); public welcome/signup stay Blade (API `publicNewCustomer`
exists, no SPA page); RepairView "Add part" uses ProductPicker instead of legacy silent
`repair_barcode` auto-add (functional equivalent).

Verified: `php -l` clean, `DELETE /api/v1/notifications/{id}` in `route:list`, phpunit 2/2,
`vite build` passes, curl smoke — notif create→destroy→404 & index back to 0; `?task=no-invoice`
returns both un-invoiced repairs (6, 3); `/barcode` returns product-found (02154154), INV4→
invoice-found, REP3→repair-found, unknown→barcode-not-found. Note: `repair-business-client`
is **Alpine (musl)**; `npm install` must run inside that container (or a glibc rebuild leaves
`@rollup/rollup-linux-x64-gnu` which fails with `Cannot find module '@rollup/rollup-linux-x64-musl'`).

## M7 settings — what exists

- `app/Http/Controllers/Api/V1/SettingController.php` (new):
  - `GET /api/v1/settings` — all settings grouped by group (`business_profile`, `language`, `tax`),
    ordered by group then name (1:1 port of `SettingController@index_setting`).
  - `PUT /api/v1/settings/business-profile` — bulk update of the business profile
    (name/phone/email/address/terms), `firstOrCreate` per field, writes one `settings` Log,
    returns formatted profile.
  - `PUT /api/v1/settings/{id}` — single setting `data` update (port of legacy
    `update_setting`: same "Setting has been Updated" Log entry).
  - Admin-only (`api.admin`), auth + locale middleware. Route order matters:
    `business-profile` is registered before `{id}`.
  - Tax remains editable via the existing `PUT /invoice-settings/tax` (reused by the page).
- `client/src/pages/settings/SettingsPage.jsx` (new) — three cards: Business profile
  (bulk save), Language (`en`/`es` select → `PUT /settings/{id}`), Tax percent
  (→ `PUT /invoice-settings/tax`). Loads `GET /settings`.
- Wired in `client/src/App.jsx` route `settings` behind `AdminOnlyRoute`.
- Verified: routes listed, GET groups correct, PUT profile + single + revert worked,
  `settings` Log rows written (ids 101–103), `phpunit` OK (2/2), `vite build` passes.
- Legacy `setting/index-setting.blade.php` + web routes kept live (strangler deferred).

## M5 payments — what exists

- `app/Http/Controllers/Api/V1/PaymentController.php` — port of Blade `PaymentController`:
  `index` (search across id/invoice/amount/method/ref, `active=yes`, paginate 25),
  `store` (`POST /payments` standalone or `POST /payments/{invoice}` — recomputes the
  linked invoice), `show`, `update` (admin, recomputes linked invoice), `destroy`
  (admin, recomputes linked invoice). Logs match Blade: create/delete-on-invoice →
  `table='invoices'`, update/delete-standalone → `table='payments'`.
- `routes/api.php` — 5 live routes under the `v1` group
  (`GET/POST /payments`, `GET/PUT/DELETE /payments/{id}`; `POST /payments/{id?}` keeps
  the original `create-payment/{id?}` semantics). `update`/`destroy` are admin-gated.
- React pages: `client/src/pages/payments/{PaymentsList,PaymentView}.jsx`, wired in
  `client/src/App.jsx` behind `AdminOnlyRoute` (`/payments`, `/payments/:id`) — matches
  the original admin-only nav placement.
  - PaymentsList: inline "Create payment" form (amount/method/ref) + search + table
    (Date/Invoice/Amount/Method/Reference) + pagination, mirroring `index-payment.blade.php`.
  - PaymentView: read-first card with Edit toggle (Save/Cancel); delete icon only for
    standalone payments (invoice-linked ones are deleted via the invoice), "View invoice"
    button when linked — same pattern as the redesigned InvoiceView.
- `InvoiceView.jsx` — added "Record payment" inline form in the totals card
  (`POST /payments/{invoice.id}`); payment rows already rendered there.

### Language is now system-wide (this session, M7 follow-up)

The Settings **Language** card used to only write the `language` Setting row, which the SPA
(author) never saw — its default `rb_locale='en'` was sent as `Accept-Language` on every request,
overriding the setting. Fixed so the setting actually drives the whole system:

- `SettingsPage` `handleSaveLanguage` now calls `setLocale(language)` before saving, so the
  current session flips immediately (all subsequent API messages + dompdf invoices/reports
  render in the new language).
- New sessions pick up the system default: `AuthController` exposes `locale`
  (= the `language` Setting, `'en'` fallback) in both `/auth/login` and `/auth/me` responses;
  `AuthContext` seeds `rb_locale` from it when the browser has no explicit preference.
- The axios interceptor now only sends `Accept-Language` when the browser HAS a stored
  preference (`hasLocale()`); otherwise the backend falls back to the system setting
  (`ApiLocale` already did this). Result: no preference → system language applies; explicit
  preference → per-browser override. Verified via curl: no header → `Has iniciado sesión.`
  (setting is `es`), `Accept-Language: en` → `You are now logged in.`
- Added `api.locale` middleware to `/auth/login` and `/auth/register` (was missing, so their
  messages were always English regardless of the setting).

### Design decisions during M5
1. **Delete icon hidden for invoice-linked payments** in PaymentView to match the Blade
   view (delete button only shown when `invoice === null`). The DELETE API still works for
   all payments (recomputes the invoice when linked).
2. **`store` with invoice sets `active='yes'`** — Blade's `create_payment` omitted it in
   the invoice branch (schema is `active NOT NULL`); set in both branches to avoid DB error.
3. **`update` recomputes the linked invoice** — Blade's `update_payment` left the balance
   stale on amount changes (latent bug). Recompute keeps balance consistent with create/delete;
   same spirit as the earlier intentional `createRepairItem` ref fix. Note it in any review.
4. **Fixed duplicate lang key** — `resources/lang/en/repair-business.php` had two
   `error_payment-has-been-deleted` entries (the 2nd, "Payment has been Updated", won).
   Renamed the 2nd to `error_payment-has-been-updated`; delete messages now read correctly.

## M6 reports — what exists

- `app/Http/Controllers/Api/V1/ReportController.php` — port of Blade `ReportController`
  (report builder + register-report), plus:
  - `preview` (`POST /reports/preview`): date range + module toggles → report data.
    Response `data` = `{ report:{from,to,invoices,repairs,payments}, invoices|null,
    repairs|null, payments|null }`. Each module payload: `{ count, ...totals, items }`.
    Invoices eager-load `status_data` + `items`; repairs eager-load
    `customer_data,status_data,priority_data`. Earnings = `total - balance` (Blade math).
  - `print` (`GET /reports/print`): same params via query → dompdf PDF of
    `report.print-report` (Blade view reused, gated by `on`/`off` toggles).
  - `register` (`POST /reports/register`): `{date, cash, card}` → day's payment list +
    totals incl. `cash_diff` / `card_diff` (register − system).
  - `registerInsert` (`POST /reports/register/insert`): `{date, cash, card}` → creates
    non-invoiced Payment rows (`ref = NON-INVOICED-{CASH|CARD}-TRANSACTIONS-{date}`,
    `created_at = date 00:00:00`) + logs, preserving the Blade behaviour (only inserts
    when amount != 0; card log string has the original `[cash]` typo).
- `routes/api.php` — 4 routes under the `v1` group (`reports/preview`,
  `reports/print`, `reports/register`, `reports/register/insert`). Auth + locale only,
  matching the web routes (the Management sidebar section is admin-only, and the React
  routes are `AdminOnlyRoute` — same visibility split as payments).
- React pages: `client/src/pages/reports/{ReportsPage,RegisterReport}.jsx`, wired in
  `client/src/App.jsx` behind `AdminOnlyRoute` (`/reports`, `/reports/register`).
  - ReportsPage: from/to dates + invoice/repair/payment checkboxes → Generate →
    on-screen sections (invoices table w/ items + status badges + view links, repairs
    table, payments table + method breakdown) + Print button (blob → new tab PDF).
  - RegisterReport: date + cash register + credit/debit totals → Generate → day's
    payments + per-method totals, no-invoice cash/card difference cards, and
    "Insert transactions" button (posts `cash_diff`/`card_diff`/`date`, then clears).

### Design decisions during M6
1. **Print stays server-side dompdf** using the existing `report.print-report` Blade
   view (its `window.print()` is ignored by dompdf) instead of a browser-print SPA
   layout — matches the invoice/repair receipt PDF pattern already in place.
2. **The Blade `print-register-report` web route referenced a non-existent method**
   (`print_register_report`); no register print was implemented (nothing broken to port).
3. **No `active`-column filter added where Blade had none** — same as M5 (schema has no
   such column on Invoice/Repair; Payment uses `active='yes'`).

## Print / email redesign (this session)

User feedback: the **letter-paper** outputs (`invoice.print-invoice` and `report.print-report`)
used SB Admin CSS via `asset()` URLs, which dompdf cannot fetch (remote disabled) — so the
generated PDFs looked bare/ugly. Receipts are fine (thermal). Changes:

- `invoice.print-invoice.blade.php` — fully self-contained inline CSS (no external styles),
  professional letter layout: dark header band (company block + INVOICE #id / status badge /
  issue date), BILLED FROM / BILLED TO columns, dark-header items table with zebra rows
  (invoice items + inventory transactions), right-aligned totals table (subtotal / tax /
  total / payment rows / boxed balance colored by sign), terms footer, QR code. Uses
  `font-family: 'DejaVu Sans', ...` (bundled in dompdf; browsers fall back cleanly) instead
  of the unloadable Nunito `@font-face`. Works in both the API (dompdf) and the legacy
  Blade `window.print()` popup.
- `report.print-report.blade.php` — same palette, letter layout: header band with period,
  section headings, stat strips, structured data tables w/ status/priority badges, footer.
- **Email now matches the print option.** `API\InvoiceController@email`,
  `InvoiceController@email_invoice`, and `app/Mail/MailInvoice.php` now generate/attach the
  letter `invoice.print-invoice` PDF (saved as `/public/invoice.pdf`) instead of the thermal
  receipt. Receipt stays only for the **receipt** tasks (`print_invoice($id,'receipt')` +
  `GET /invoices/{id}/print/receipt`) — thermal printer output.
- `print-invoice-receipt.blade.php` untouched.

### Third styling pass (user: "match the React UI, simple light, and PDF has no margins")
Done:
- **Margins were a real bug**: barryvdh's dompdf renders A4 by default; the `@page { margin: ... }`
  rule in the view was not honored by dompdf, so pages had no margin and wrong (A4) size.
  Fix: call `->setPaper('letter', 'portrait')` on the dompdf in `Api/V1/InvoiceController::print`
  (print task only; receipt keeps default) and `::email`, `Api/V1/ReportController::print`,
  and legacy `InvoiceController::email_invoice`. `@page` in views now uses `margin: 0` and the
  whitespace comes from `body { margin: 18mm 20mm }` (body margins are honored by dompdf).
  Verified via MediaBox in the generated files: both PDFs are 612×792 (letter).
- **Style**: both letter views are now a simple light "plain card" look matching the React SPA —
  white page, one card with a thin `#e2e8f0` border + 12px radius, soft `#f8fafc` section/table
  heads, slate grays for text, subtle `#2563eb` accents, pastel status pills, no dark bands.
  Invoice header: `INVOICE` label + large blue `#id` + status pill + date on the right.
  Report header mirrors it; payments summary moved into a tidy stat strip.
- Added missing lang keys: `terms`, `ref`, `breakdown` in `repair-business.php`.

## M4 invoices — what exists

- `app/Http/Controllers/Api/V1/InvoiceController.php` — full port (index/show/create/update/
  softDelete/restore/destroy/updateCustomer/print/email/items CRUD/createRepairItem/
  settings CRUD/updateTaxSetting, plus helpers `recomputeInvoice`, `applyCompany`,
  `applyCustomer`, `formatPhone`, `repairJobsText`).
- `routes/api.php` — all invoice + invoice-settings routes LIVE INSIDE the `v1` prefix group
  only (`api/v1/invoices...`). Duplicate out-of-prefix blocks were removed this session.
- React pages: `client/src/pages/invoices/{InvoicesList,InvoiceForm,InvoiceView,InvoiceSettings}.jsx`;
  wired in `client/src/App.jsx` (settings behind `AdminOnlyRoute`, route `invoices/settings`).

### InvoiceView redesign (this session)

User feedback: the view "looks too full". Reworked to a read-first invoice document:
- From/To/tax/status are plain text until an **Edit** toggle is used (Save/Cancel inline).
- Items are read-only rows; edit/delete icons appear on row hover; "Add item" form hidden
  behind a button; barcode scan behind a "Scan" toggle.
- Removed separate customer card and linked-repairs card (repair rows link to the repair +
  a one-line linked-repairs strip). Totals in card footer. Activity log stays collapsed
  and is paginated (passes `?page=` to `show`).
- User reviewed it: "looks super clean".

## Fixes made this session

1. `InvoiceController@show` now eager-loads `user_data` on logs (activity log author was blank;
   `RepairController@show` already did this).
2. `routes/api.php` — deleted duplicate non-`v1` invoice/invoice-settings route groups.
3. `client/src/components/inventory/ProductPicker.jsx:35` — was reading `data.data.data`
   (undefined → blank page when opening the picker). The products endpoint returns
   `data.data.products.data`. This one fix restored **Add product (invoices)** and
   **Add part (repairs)**, which share the picker.

## API envelope shapes (memory helpers)

- Success: `{ success, message, data }` (App\Traits\ApiResponses).
- `/inventory/products` → `data = { products: <paginator>, categories: [...] }` (products nested one extra level).
- `/customers` and many list endpoints → `data = <paginator>` (so `data.data.data` is correct there).
- Client axios baseURL `/api/v1`; token key `rb_token`; locale `rb_locale`.

## Route / behaviour quirks

- Cancel inventory transaction expects `task=invoice` **singular**:
  `DELETE /api/v1/inventory/invoice/{id}/transactions/{txn}`. Sell uses plural
  `/inventory/invoices/{invoice}/products/{product}/sell`.
- `restock` requires both `purchase_price` and `quantity`.
- `POST /v1/barcode/invoice` body `{ invoice, barcode }`.
- `createRepairItem` sets `ref = repair id` (original Blade used the invoice id — a bug that
  broke repair→invoice linkage; intentionally fixed).
- `recomputeInvoice` must NOT filter payments by `active`.
- Invoice columns confirmed: `customer_id, customer_name, company_*, tax_porcentage, subtotal, tax, total, balance`.
- InvoiceItem fields: `name, description, sub_description, unit_cost, quantity, total, group, ref`.
- Stock is computed (purchases − sells); there is no product `quantity`/`active` column.

## Verification done this session

- M4 (prior session): `vite build` passes; `php -l` clean; `phpunit` OK; curl smoke of
  login/invoice routes, `logs.user_data`, transactions have `product`.
- M5 (this session): `php -l` clean (controller, routes, lang); `route:list` shows the 5
  payment routes only under `api/v1`; `phpunit` OK (2 tests, 2 assertions); `vite build`
  passes (chunk-size warning pre-existing).
- M5 curl smoke: login → payments index (2 seeded rows); standalone create (id 6) →
  show (invoice null); invoice-linked create on invoice 3 (balance 65→−10); update to 25
  (balance →−25); delete standalone (id 6) + delete linked (id 7, balance →0). Test rows
  cleaned up — DB back to payments 3,4 only.
- M6 (this session): `php -l` clean; `route:list` shows the 4 report routes only under
  `api/v1`; `phpunit` OK; `vite build` passes. Curl smoke: `reports/preview` (2020→2030
  → invoices 2 / $135, repairs 2, payments 3 / cash $135); `reports/print` → 200
  `application/pdf` for all-1s and mixed-toggle combos; `reports/register` day with no
  payments → diff math correct; `reports/register/insert` created payments 9 (cash
  125.50) + 10 (card 40) with `NON-INVOICED-*-2026-09-16` refs, then both + logs cleaned
  up. (Payment id 8, cash $5.00, is a pre-existing leftover from the M5 'Record payment'
  UI test — left in place.)
- Print redesign (this session): rebuilt `invoice.print-invoice` + `report.print-report`
  with inline CSS; re-verified `GET /invoices/3/print/print` → 200 PDF (23 KB),
  `GET /invoices/3/print/receipt` → 200 PDF (3 KB, untouched), `GET /reports/print?…` →
  200 PDF (35 KB). `php -l` clean on both InvoiceControllers + `MailInvoice`.
- Print letter-size + light-card pass (this session): controllers now call
  `->setPaper('letter', 'portrait')` (invoice print+email, report print, legacy email);
  views use `@page { margin: 0 }` + `body { margin: 18mm 20mm }`. Verified MediaBox =
  612×792 (letter) in both generated PDFs.
- Invoice payment/totals fix (this session): payments + subtotal/tax/total/balance are now
  **rows inside the main `<table>`** (was a floated side panel the user called "floating /
  doesn't belong"). Balance is a full-width light-blue table-footer row.
- **Pitfalls hit while editing the invoice PDF (avoid next time):**
  - dompdf crashes with `Call to a member function get_cellmap() on null`
    (`FrameReflower/TableCell.php:40`) if any `<tr>/<td>` ends up OUTSIDE a `<table>` —
    an orphan `</tbody></table>` closed the items table early and all payment/total rows
    fell outside. Keep every row inside `<tbody>`. Empty spacer rows
    (`<td colspan=6 border:0>`), `vertical-align: baseline` on cells were also suspects.
  - After `php artisan view:cache` (run as root) the app can fail to recompile changed
    Blade with `Permission denied` → clear it and let the app recompile as its own user:
    `docker exec repair-business-app rm -rf storage/framework/views/*`.
- M7 settings (this session): `GET/PUT` verified via curl, settings Log rows written
  (ids 101–103), `phpunit` OK, `vite build` passes. Dev DB business profile text now set
  to "FixHub Repair / 555-1234 / hello@fixhub.example / 12 Main St / terms…" (test data).

## Known outstanding / next steps

0. **Client i18n (translation) COMPLETED 2026-09-19 — uncommitted.** The previous session
   built the i18n system (`client/src/i18n/{I18nContext.jsx,locales.js}` — en + es dicts,
   stored on the auth user + fallback to `Accept-Language` on the server side) and wired
   ~24 files but left 9 pages and two half-translated files. This session finished it:
   - Wired `useI18n`/`t()` into `client/src/pages/{logs/LogsList,trash/TrashList,users/UsersList,
     users/Profile,payments/PaymentView,reports/ReportsPage,reports/RegisterReport,settings/
     SettingsPage}.jsx` + `client/src/layouts/AppLayout.jsx` (VerifyBanner).
   - Finished `client/src/pages/payments/PaymentsList.jsx` (main list + NewPaymentForm: title,
     subtitle, search placeholder, table headers, View link, empty/loading states, form labels)
     and `client/src/pages/VerifyEmail.jsx` (title, verifying, Go/Back to sign in).
   - Fixed pre-existing bug: `RegisterReport.jsx` used `formatDate` without importing it.
   - Removed 4 truly-dead dict keys from both locales: `auth.already_account`, `common.user`,
     `dashboard.title`, `invoices.product`.
   - Verified: `npx vite build` passes; key checker (/tmp/opencode/checkkeys.mjs) reports
     427 used keys, `MISSING IN EN/ES: []`, es/en diff empty, and the only "unused" keys
     are the `nav.*` items that Sidebar resolves dynamically via `t(item.label)`.
   - Convention: `getApiError(err, '…')` fallback strings stay hardcoded English (matches
     how all other translated files handle them); aria-label a11y strings stay hardcoded.

1. **Confirm M9 (logs) + M10 (trash) + M11 (users/profile) + parity-gap fixes with the user,
   then commit** — files: new `Api/V1/{Log,Trash,User}Controller.php`,
   `Api/V1/NotificationController.php@destroy`, `client/src/pages/{logs/LogsList,trash/TrashList,
   users/Profile,users/UsersList}.jsx`, `client/src/components/inventory/GlobalScanner.jsx`,
   edits to `routes/api.php`, `client/src/{App,layouts/AppLayout,pages/Dashboard}.jsx`,
   `client/src/components/layout/Topbar.jsx`, `client/src/pages/{repairs/RepairsList,
   inventory/ProductForm}.jsx`, lang files, HANDOFF.md. Note: push `react` (currently 4 commits
   ahead of `origin/react`) once confirmed.
2. Optional: delete the `.m4p/` scratch directory (it slipped into commit `748c2a8`).
3. Next milestone is **M12 cleanup** (final): remove legacy `web.php` routes + Blade views
   (auth/customer/repair/invoice/payment/report/setting/log/trash/user/emails leftovers),
   drop `laravel/ui` + laravel-mix + vue, tighten CORS, document the production build,
   then run the full smoke pass of every React screen against the API.
   (M1–M11 modules are all ported.)
4. Legacy Blade routes still registered (web) — strangler removal pending once React is
   fully verified. PDF views still needed: `report.print-report` (M6 print),
   `invoice.print-invoice`, `invoice.print-invoice-receipt` (expect `$invoice,$invoic
   items,$transactions,$invoice_statuses,$logs,$payments,$terms`; receipt uses `DNS2D`
   barcode of `view-invoice`).
5. Email endpoints return 500 in this env (mailtrap null creds) — expected, not a code bug.
6. `client/node_modules` was regenerated with Node 20; keep using Node 20.
7. Test customers are ids 5,6. DB back to payments 3,4,8; invoices 3,4;
   transactions 6–9. Settings rows: business_profile(3-7) + language(2) + tax(1);
   `settings` Log entries 101–103.
8. Business profile in the dev DB currently has "FixHub Repair" test data (set by M7
   smoke test).

## Relevant files

- `app/Http/Controllers/Api/V1/InvoiceController.php` — M4 API controller.
- `app/Http/Controllers/Api/V1/PaymentController.php` — new M5 API controller.
- `app/Http/Controllers/Api/V1/ReportController.php` — new M6 API controller.
- `app/Http/Controllers/Api/V1/SettingController.php` — new M7 API controller (index/update/updateProfile).
- `routes/api.php` — invoice/invoice-settings/payment/report routes under `v1`; legacy web routes below.
- `client/src/pages/invoices/InvoiceView.jsx` — redesigned (read-first + Edit toggle) + Record payment.
- `client/src/pages/invoices/{InvoicesList,InvoiceForm,InvoiceSettings}.jsx`.
- `client/src/pages/payments/{PaymentsList,PaymentView}.jsx` — new M5 pages.
- `client/src/pages/reports/{ReportsPage,RegisterReport}.jsx` — new M6 pages.
- `client/src/pages/settings/SettingsPage.jsx` — new M7 page (profile / language / tax cards).
- `client/src/App.jsx` — routes wired (payments + reports behind `AdminOnlyRoute`).
- `client/src/components/inventory/ProductPicker.jsx` — fixed (invoice "Add product" + repair "Add part").
- `client/src/components/customers/CustomerPicker.jsx` — reference for picker pattern.
- `client/src/i18n/{I18nContext.jsx,locales.js}` — new client translation system (en/es).
- `client/src/pages/VerifyEmail.jsx`, `client/src/components/layout/nav.js` — wired to dict.
- `app/Http/Controllers/InvoiceController.php` (Blade) — source of truth for port parity.
- `UPGRADE_PLAN.md`, `REQUIREMENTS.md` — module plan + requirements.
- `.m4p/` — controller assembly scratch (deletable).
