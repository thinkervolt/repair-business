# REQUIREMENTS — Repair Business App

Requirements for the repair business management application. Everything referenced here
runs inside Docker (see AGENTS.md).

## Project Overview

A system to handle repairs, invoices, payments, inventory, and reports in a fast-paced
repair business environment. Built with Laravel 8 (Blade + Bootstrap 4) and MySQL 8.4,
served by Apache inside a Docker container.

## Stack

- Backend: PHP 8 / Laravel 8
- Frontend: Blade templates, Bootstrap 4, jQuery, Vue 2 (via laravel-mix)
- Database: MySQL 8.4
- Web server: Apache inside the `repair-business-app` container
- PDF: barryvdh/laravel-dompdf
- Barcodes: milon/barcode
- Development: fully containerized (Docker Compose), no host-level tooling

## Functional Requirements

### 1. Authentication & Roles
- Email-verified authentication (Laravel `Auth::routes(['verify' => true])`).
- Roles: Customer (public signup), Staff, Admin.
- Admin-only actions are gated by the `admin` middleware; all private routes require
  verified login and run the `notification` + `locale` middleware.

### 2. Dashboard
- Logged-in landing page (`/home`, `/dashboard`) with business overview.

### 3. Customers
- Public signup and staff-created customers.
- CRUD with search, view, and soft delete.
- Trash flow: restore or permanently destroy (admin-only destroy).
- A repair/invoice can be re-assigned to a different customer.

### 4. Repairs
- Create, view, update, and soft-delete repairs, with per-repair customer.
- Searchable list grouped by repair status.
- Repair settings (admin-managed statuses/options: create, update, delete).
- Repairs have multiple repair items (add/remove).
- Print thermal drop-off receipt, barcode, and email receipt to customer.

### 5. Invoices
- Create, view, update, soft-delete; searchable list grouped by status.
- Invoice items, including items pulled from repairs.
- Invoice settings (admin-managed).
- Print invoice (thermal), barcode, email invoice, PDF generation.

### 6. Payments
- Record, view, search, update, and delete payments.
- Delete/update restricted to admin; deletion logs.

### 7. Inventory
- Categories: CRUD (delete admin-only).
- Products: CRUD, search, view; direct barcode/pricing support.
- Transactions: restock, sell, quick-sell, view, cancel; admin-only listing/search.
- Transaction history per product.

### 8. Barcodes
- Generate barcodes for products, invoices, and repairs.

### 9. Reports
- Report by date range covering invoices, repairs, and payments; printable.
- Register report with data insertion; printable.

### 10. Settings
- General/company settings (edit by admin).
- Invoice and repair settings (admin-managed).

### 11. Logs & Notifications
- Activity logs index (admin) with search.
- In-app notifications.

### 12. Users & Profile
- User list, update, delete (admin).
- Profile view + password change.

### 13. Localization
- `locale` middleware present; UI should support locale-aware output.

## Non-Functional Requirements

1. **Fully containerized.** The app, DB, and phpMyAdmin run in Docker; nothing is
   installed on the host machine. All tooling commands run through `docker exec` /
   `docker run --rm` (see AGENTS.md).
2. **Reproducible setup.** `docker-compose.yml` + `.env` must be enough to bring up the
   app against MySQL on the shared `builder.net` network.
3. **Security.** No secrets in the repo; credentials live in `.env` only. All private
   routes require verified auth; destructive admin actions are role-gated.
4. **Data safety.** MySQL data is backed up via `mysqldump`; soft-delete + trash used
   before permanent deletion.
5. **Performance.** Fast-paced shop usage — lists are searchable/filterable, printing and
   email paths are one-click without slow complex flows.
6. **Output formats.** Drop-off receipts, invoices, barcodes, and reports must print
   cleanly (thermal printer and PDF).
7. **Code quality.** Follow existing Laravel 8 / Blade + Bootstrap 4 conventions.
   No code comments unless asked. Keep changes minimal and focused.