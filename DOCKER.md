# Docker setup (Laravel 5.6 / PHP 7.4 / MySQL 5.7)

## Prerequisites

- Docker and Docker Compose installed
- Frontend assets built on the host (optional but recommended):

  ```bash
  npm install
  npm run production
  ```

## Quick start

1. Ensure `.env` exists and uses Docker database settings (or copy the example):

   ```bash
   cp .env.docker.example .env
   php artisan key:generate   # only if APP_KEY is empty
   ```

   Minimum DB settings for Docker:

   ```
   DB_HOST=db
   DB_DATABASE=ichat
   DB_USERNAME=ichat
   DB_PASSWORD=ichat_secret
   APP_URL=http://localhost:8080
   APP_DEBUG=true
   ```

2. Build and start:

   ```bash
   docker compose up -d --build
   ```

3. Open the app: **http://localhost:8080**

4. MySQL from the host (optional): `127.0.0.1:3307`, user `ichat`, password `ichat_secret`, database `ichat`.

5. phpMyAdmin: **http://localhost:8081** — Server: `db`, user `ichat` / password `ichat_secret`, database `ichat` (or root / `root_secret`).

### Import SQL (recommended: terminal, not phpMyAdmin UI)

phpMyAdmin often shows *"Could not load the progress of the import"* in Docker even for small files. Use the command line instead:

```bash
# Project dump + missing tables
docker compose exec -T db mysql -u ichat -pichat_secret ichat < ichat.sql
docker compose exec -T db mysql -u ichat -pichat_secret ichat < docker/mysql/03-missing-tables.sql

# Your own .sql file
docker compose exec -T db mysql -u ichat -pichat_secret ichat < /path/to/your-file.sql
```

Then restart phpMyAdmin if you changed its config: `docker compose up -d phpmyadmin`

## Services

| Service | Image / build | Port |
|---------|----------------|------|
| `web`   | nginx          | 8080 → 80 |
| `app`   | PHP 7.4-FPM    | (internal) |
| `db`    | MySQL 5.7      | 3307 → 3306 |
| `phpmyadmin` | phpMyAdmin 5 | 8081 → 80 |

On first start, `ichat.sql` and `docker/mysql/03-missing-tables.sql` are imported into the `ichat` database.

If the DB volume already exists and you see **Table 'ichat.settings' doesn't exist**:

```bash
docker compose exec -T db mysql -u ichat -pichat_secret ichat < docker/mysql/03-missing-tables.sql
```

## Useful commands

```bash
# View logs
docker compose logs -f

# Run Artisan inside the app container
docker compose exec app php artisan migrate
docker compose exec app php artisan cache:clear

# Stop and remove containers (keeps DB volume)
docker compose down

# Stop and remove DB data volume (re-imports ichat.sql on next up)
docker compose down -v
```

## Troubleshooting

**Containers `Restarting` in a loop** — rebuild after Dockerfile/nginx fixes:

```bash
docker compose down
docker compose up -d --build
docker compose ps
```

All services should show **Up** (not `Restarting`). `lbs-web` must show `0.0.0.0:8080->80/tcp`.

**502 Bad Gateway** — nginx cannot reach PHP-FPM. Rebuild and restart:

```bash
docker compose up -d --build
docker compose logs app --tail 50
```

If Composer is still installing `vendor/`, wait 1–2 minutes and refresh, or run:

```bash
docker compose exec app composer install
```

## Notes

- Uploads are stored in `public/uploads/` on the mounted project folder (not S3).
- `composer install` runs automatically on first container start if `vendor/` is missing.
- Rebuild after Dockerfile changes: `docker compose up -d --build`
