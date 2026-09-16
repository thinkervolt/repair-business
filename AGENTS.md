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

| Container               | Purpose                | Reachable at            |
|-------------------------|------------------------|-------------------------|
| `repair-business-app`   | Laravel 8 + Apache     | http://localhost:5050   |
| `mysql_development`     | MySQL 8.4              | `mysql_development:3306`|
| `phpmyadmin_development`| phpMyAdmin             | http://localhost:9010   |

All three are on the external Docker network `builder.net` (see `.env` → `NETWORK_NAME`).
The app container's code directory is bind-mounted from this repo root (`.:/var/www`).

> Note: `vendor/`, `public/`, `storage/`, and `bootstrap/cache` are anonymous volumes baked
> at image build time — they are NOT synced with the host. Commands like
> `composer require` or changes to `public/` only take effect after
> `docker compose build` + `docker compose up -d --force-recreate app`.

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

### npm / asset build (app image has no Node; uses throwaway image)
```
docker run --rm -v "$PWD":/app -w /app node:16 npm install
docker run --rm -v "$PWD":/app -w /app node:16 npm run production
```
Note: files created this way are owned by root. Minor permission churn is acceptable;
do not `sudo` anything unless strictly needed and safe.

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

1. Make code changes using the repo's existing conventions (Laravel 8, Blade + Bootstrap 4).
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