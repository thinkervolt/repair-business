# HANDOFF — repair-business (Laravel 8 → API + React upgrade)

Last updated: 2026-09-16. Written to let a future session/agent resume cleanly.

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

Uncommitted working tree:
```
 M client/src/App.jsx
 M client/src/components/inventory/ProductPicker.jsx
 M routes/api.php
?? app/Http/Controllers/Api/V1/InvoiceController.php
?? client/src/pages/invoices/            (InvoicesList, InvoiceForm, InvoiceView, InvoiceSettings)
?? .m4p/                                 (scratch used to assemble the controller; safe to delete)
```

## Modules status

- M1 auth/shell — done & committed
- M2 customers — done & committed
- M3 repairs — done & committed
- M8 inventory — done & committed
- **M4 invoices — implemented + smoke-tested, NOT committed**

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

- `vite build` passes (chunk-size warning pre-existing; CSS grew to ~38.8 kB).
- `php -l routes/api.php` clean; `route:list` shows invoice routes only under `api/v1`.
- `phpunit` OK (2 tests, 2 assertions).
- curl smoke tests: login, invoice index/show, `/inventory/products` shape, `logs.user_data`
  present, transactions have `product`.

## Known outstanding / next steps

1. **User has seen invoices + product/part picker working; paused before committing.**
   Confirm with the user, then commit M4 (only `App.jsx`, `routes/api.php`, `ProductPicker.jsx`,
   `InvoiceController.php`, `client/src/pages/invoices/` — exclude `.m4p/`).
2. Optional: delete the `.m4p/` scratch directory.
3. Legacy Blade invoice/inventory routes still registered (web) — strangler removal pending
   once React is fully verified. PDF views still needed: `invoice.print-invoice`,
   `invoice.print-invoice-receipt` (expect `$invoice,$invoice_items,$transactions,
   $invoice_statuses,$logs,$payments,$terms`; receipt uses `DNS2D` barcode of `view-invoice`).
4. Email endpoint returns 500 in this env (mailtrap null creds) — expected, not a code bug.
5. `client/node_modules` was regenerated with Node 20; keep using Node 20.
6. Test customers are ids 5,6. DB is back to pre-smoke state (invoices 3,4; transactions 6–9).

## Relevant files

- `app/Http/Controllers/Api/V1/InvoiceController.php` — new M4 API controller.
- `routes/api.php` — invoice routes under `v1`; legacy web routes below.
- `client/src/pages/invoices/InvoiceView.jsx` — redesigned (read-first + Edit toggle).
- `client/src/pages/invoices/{InvoicesList,InvoiceForm,InvoiceSettings}.jsx`.
- `client/src/App.jsx` — routes wired.
- `client/src/components/inventory/ProductPicker.jsx` — fixed (invoice "Add product" + repair "Add part").
- `client/src/components/customers/CustomerPicker.jsx` — reference for picker pattern.
- `app/Http/Controllers/InvoiceController.php` (Blade) — source of truth for port parity.
- `UPGRADE_PLAN.md`, `REQUIREMENTS.md` — module plan + requirements.
- `.m4p/` — controller assembly scratch (deletable).
