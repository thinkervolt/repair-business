# AGENTS.md — Working Rules

Rules for any agent (or developer) working on this project. Read before making changes.

## Golden Rule: Everything Runs in Docker

1. **Never install anything on the host machine.** This includes OS packages, PHP, Composer,
   Node.js, npm/yarn, MySQL clients, etc. All tooling must run inside a Docker container.
2. **All commands run through Docker.** Use `docker exec`, `docker run --rm`, or
   `docker compose` — never bare host commands like `php`, `composer`, `npm`, `mysql`, or `phpunit`.
3. The only host-level prerequisites are the ones already present: Docker + Docker Compose.
4. If a tool doesn't exist in a running container, use a throwaway container for it
   (see commands below). Do not install it on the host.

## Running Containers (current dev setup)

| Container               | Purpose                                | Reachable at            |
|-------------------------|----------------------------------------|-------------------------|
| `repair-business-app`   | Laravel 8 + Apache (API + React SPA)   | http://localhost:5050   |
| `repair-business-client`| Vite dev server (React, dev-only)      | http://localhost:5173   |
| `mysql_development`     | MySQL 8.4                              | `mysql_development:3306`|
| `phpmyadmin_development`| phpMyAdmin                             | http://localhost:9010   |

All are on the external Docker network `builder.net` (see `.env` → `NETWORK_NAME`).
The app container's code directory is bind-mounted from this repo root (`.:/var/www`).

## Dev vs Production (one image, one compose file)

The `Dockerfile` is multi-stage and produces a **single self-contained image**:
composer deps → Node builds the React SPA → `php:8.1-apache` serves **everything on port 80**
(React SPA for UI routes, Laravel for `/api/*`). `public/.htaccess` has the SPA fallback:
non-`/api`, non-file requests go to `index.html`; `/api/*` goes to Laravel.

- **Production / GCE**: build and run the image; it listens on TCP 80. `docker compose`
  maps ports from `.env` (`SERVICE_PORT` → 80 for the app, `CLIENT_PORT` → 5173 for the
  dev client). Set the deployment URL/env in the Laravel `.env` at deploy time.
- **Dev**: no image rebuild needed for code. React devs use the Vite server on `:5173`
  (HMR) which proxies `/api` → app container; PHP changes flow in via the bind mount.
- **Ports served by the Dockerfile**: `80`. Host ports (5050 / 5173 / 9010) are just
  compose mappings from `.env` — change `SERVICE_PORT`/`CLIENT_PORT` per environment.

> The baked SPA on the app (`:5050`) is a **build-time snapshot**, not the live dev UI.
> Editing React source only shows up on `:5173`. Rebuild the image to refresh `:5050`.

> `vendor/`, `public/`, `storage/`, and `bootstrap/cache` are anonymous volumes baked
> at image build time — they are NOT synced with the host. Changes to Composer deps or
> `public/` only take effect after `docker compose build` + `docker compose up -d --force-recreate app`.
> To reseed those volumes from a fresh image (e.g. after SPA/htaccess changes) run
> `docker compose down -v && docker compose up -d`. This does NOT touch the DB or
> phpMyAdmin (they are not part of this compose file).

> Container state: `APP_ENV`, `APP_DEBUG`, `APP_URL`, etc. are injected by compose
> (`env_file: .env`) **at container start** and override the file. After changing them in
> `.env`, recreate with `docker compose up -d --force-recreate app`.

> Note: the container's default working directory is `/var/www/html`. Always pass
> `-w /var/www` when running artisan/phpunit in the app container.

## Commands

Run from the repo root unless noted.

### Laravel / artisan
```
docker exec -w /var/www repair-business-app php artisan <cmd>
docker exec -w /var/www repair-business-app php artisan migrate --seed
docker exec -w /var/www repair-business-app php artisan route:list
```

### Composer (no composer on host; uses throwaway image)
```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker run --rm -v "$PWD":/app -w /app composer:2 composer dump-autoload
docker run --rm -v "$PWD":/app -w /app composer:2 composer require <pkg>
```
Then rebuild so the new vendor lands in the app container:
```
docker compose build && docker compose up -d --force-recreate app
```

### npm / asset build (dev-only; the Dockerfile builds the SPA itself for prod)
```
docker run --rm -v "$PWD/client":/app -w /app -v rb_npm_cache:/root/.npm node:20 npm install
docker run --rm -v "$PWD/client":/app -w /app -v rb_npm_cache:/root/.npm node:20 npm run build
```
Output lands in `client/dist` (Vite). Files created this way are owned by root; minor
permission churn is acceptable — do not `sudo` anything unless strictly needed and safe.
`.dockerignore` keeps `.env`, `node_modules`, `dist`, `vendor`, and Laravel caches out of
the image build context so secrets never get baked in.

### MySQL (inside the DB container)
```
docker exec -it mysql_development mysql -u repair_business_database -p repair_business_database
docker exec mysql_development mysqldump -u repair_business_database -p repair_business_database > backup.sql
```
Credentials come from `.env` (never hardcode or commit them).

### Tests
```
docker exec -w /var/www repair-business-app ./vendor/bin/phpunit
```

### Logs
```
docker logs -f repair-business-app
docker exec -w /var/www repair-business-app tail -f storage/logs/laravel.log
```

## Required Workflow

1. Make code changes using the repo's existing conventions (Laravel 8 API in `app/`,
   React SPA in `client/`).
2. Verify with in-container commands: run relevant tests and `php artisan` commands.
3. Do not commit unless the user explicitly asks.
4. Never modify `.env` for committed examples — keep `.env.example` in sync instead.
5. Never commit secrets, `.env`, real credentials, or generated assets.

## Env & Secrets

- `.env` is local-only and already git-ignored. `.env.example` is the committed template;
  keep them in sync when adding variables.
- Database credentials and mail credentials live only in `.env` / the Docker environment.

## Code Style

- Follow existing Laravel conventions already in the codebase.
- Do not add code comments unless the user asks.
- Keep changes minimal and focused on the task at hand.