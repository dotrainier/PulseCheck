# PulseCheck — What Changed & How to Test

A full breakdown of every file added, modified, or removed when converting the app from static Vue pages to a fully functional Laravel + PostgreSQL backend with live data.

---

## Table of Contents

- [Overview](#overview)
- [Stack](#stack)
- [Added Files](#added-files)
    - [Backend](#backend-added)
    - [Frontend](#frontend-added)
- [Modified Files](#modified-files)
    - [Backend](#backend-modified)
    - [Frontend](#frontend-modified)
- [Database Schema](#database-schema)
- [API Reference](#api-reference)
- [How to Run Locally](#how-to-run-locally)
- [How to Run with Docker](#how-to-run-with-docker)
- [How to Test the API (Manual)](#how-to-test-the-api-manual)
- [Seeded Demo Data](#seeded-demo-data)
- [Key Concepts for New Developers](#key-concepts-for-new-developers)

---

## Overview

The app was originally five static Vue pages with hardcoded/fake data. This update wires them to a real database with:

- **Authentication** via Laravel Sanctum (token-based)
- **CRUD** for monitors with real HTTP health checking
- **Automatic incident creation/resolution** based on check results
- **SSL certificate tracking** per monitor
- **Scheduled checks** every minute via Laravel's task scheduler
- **Dashboard and incident stats** computed from real check history

---

## Stack

| Layer     | Technology                   |
| --------- | ---------------------------- |
| Backend   | Laravel 13, PHP 8.3+         |
| Auth      | Laravel Sanctum (API tokens) |
| Database  | PostgreSQL 16                |
| Frontend  | Vue 3 (Composition API)      |
| State     | Pinia                        |
| Router    | Vue Router 4                 |
| HTTP      | Axios                        |
| Styling   | Tailwind CSS v4              |
| Container | Docker + Docker Compose      |

---

## Added Files

### Backend (Added)

#### Migrations — `database/migrations/`

| File                                                  | What it creates                                                             |
| ----------------------------------------------------- | --------------------------------------------------------------------------- |
| `2026_05_12_000001_create_monitors_table.php`         | Stores each user's monitors (URL, interval, SSL settings, status, uptime %) |
| `2026_05_12_000002_create_monitor_checks_table.php`   | One row per health check result (success, status code, response time)       |
| `2026_05_12_000003_create_incidents_table.php`        | Incidents triggered by failed checks (severity, status, impact)             |
| `2026_05_12_000004_create_incident_updates_table.php` | Timeline updates attached to an incident                                    |

#### Models — `app/Models/`

| File                 | Responsibility                                                                                              |
| -------------------- | ----------------------------------------------------------------------------------------------------------- |
| `Monitor.php`        | Belongs to User; has many Checks and Incidents. Has `getIntervalInSeconds()` and `isDueForCheck()` helpers. |
| `MonitorCheck.php`   | Single check result; belongs to Monitor.                                                                    |
| `Incident.php`       | Belongs to Monitor; has many IncidentUpdates. Has `getDurationAttribute()` computed property.               |
| `IncidentUpdate.php` | A timestamped message on an incident timeline.                                                              |

#### Policies — `app/Policies/`

| File                 | What it guards                                                   |
| -------------------- | ---------------------------------------------------------------- |
| `MonitorPolicy.php`  | Only the monitor's owner can view, update, or delete it.         |
| `IncidentPolicy.php` | Only the owner of the monitor linked to an incident can view it. |

#### Service — `app/Services/MonitorService.php`

The core health-checking logic. Called by the controller and the scheduled command.

| Method                                   | What it does                                                                                                      |
| ---------------------------------------- | ----------------------------------------------------------------------------------------------------------------- |
| `runCheck(Monitor)`                      | Makes an HTTP GET request, records the result, updates stats, handles incidents.                                  |
| `updateMonitorStats()`                   | Recalculates 30-day uptime %, 24h avg response time, and sets status (`operational` / `degraded` / `down`).       |
| `handleIncidents()`                      | Creates a new incident on first failure; increments `failed_checks` if one is active; marks resolved on recovery. |
| `updateSslInfo()`                        | Connects via SSL socket, reads the certificate, saves expiry date, issuer, and days remaining.                    |
| `getUptimeHistory(Monitor, days)`        | Returns per-day uptime % for the last N days (used by MonitorDetail chart).                                       |
| `getResponseTimeHistory(Monitor, hours)` | Returns `{time, value}` points for the last N hours (used by MonitorDetail chart).                                |

**Status logic:**

- `down` — 3 or more consecutive failures
- `degraded` — 1+ failure OR avg response time > 1000 ms
- `operational` — everything else

#### Controllers — `app/Http/Controllers/`

| File                      | Routes it handles                                                           |
| ------------------------- | --------------------------------------------------------------------------- |
| `DashboardController.php` | `GET /api/dashboard/stats` — aggregate counts, avg uptime, recent incidents |
| `MonitorController.php`   | Full CRUD + `GET /{id}/checks` + `POST /{id}/check` (manual trigger)        |
| `IncidentController.php`  | `GET /api/incidents` (with filters) + `GET /api/incidents/{id}`             |

#### Artisan Command — `app/Console/Commands/RunMonitorChecks.php`

```
php artisan monitors:check
php artisan monitors:check --monitor=5   # run for one specific monitor
```

Iterates all monitors, skips any that are not yet due (based on their `check_interval`), and calls `MonitorService::runCheck()` for each.

#### Seeder — `database/seeders/MonitorSeeder.php`

Seeds 6 realistic monitors for `test@example.com`, plus 1,728 historical check records (288 per monitor over 24 hours) and 4 incidents with updates. See [Seeded Demo Data](#seeded-demo-data).

---

### Frontend (Added)

#### Pinia Stores — `resources/js/stores/`

| File          | What it holds                                                                                                                                                                                                                                                             |
| ------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `auth.js`     | `user`, `token`, `isAuthenticated`. Methods: `fetchUser()`, `setSession()`, `clear()`, `logout()`. Token and user are persisted in `localStorage` under `auth_token` and `user`.                                                                                          |
| `monitors.js` | `monitors[]`, `loading`, `error`. Computed: `totalMonitors`, `operationalCount`, `downCount`, `degradedCount`, `systemStatus`, `averageUptime`, `averageResponseTime`. Methods: `fetchMonitors()`, `createMonitor()`, `updateMonitor()`, `deleteMonitor()`, `runCheck()`. |

---

## Modified Files

### Backend (Modified)

#### `app/Models/User.php`

- Added `monitors()` HasMany relationship so `$user->monitors` works everywhere.

#### `app/Http/Controllers/AuthController.php`

- Added `signout()` method — deletes the current Sanctum token, returns `{ message: "Signed out" }`.
- Removed two unused imports (`Auth`, `ValidationException`) that were left over from a previous version.

#### `routes/api.php`

Completely rewritten. Before: only had a sign-in route. After:

```
POST   /api/signin
GET    /api/user                         (auth)
POST   /api/signout                      (auth)
GET    /api/dashboard/stats              (auth)
GET    /api/monitors                     (auth)
POST   /api/monitors                     (auth)
GET    /api/monitors/{id}                (auth)
PUT    /api/monitors/{id}                (auth)
DELETE /api/monitors/{id}                (auth)
GET    /api/monitors/{id}/checks         (auth)
POST   /api/monitors/{id}/check          (auth)
GET    /api/incidents                    (auth)
GET    /api/incidents/{id}               (auth)
```

#### `routes/console.php`

Added the scheduler entry so monitors are checked automatically:

```php
Schedule::command('monitors:check')->everyMinute();
```

This requires a cron job (or `php artisan schedule:run` in a loop) to actually fire.

#### `database/seeders/DatabaseSeeder.php`

- Uses `User::firstOrCreate()` instead of `User::factory()->create()` to avoid duplicate key errors if seeded more than once.
- Calls `MonitorSeeder`.

---

### Frontend (Modified)

#### `resources/js/composables/useMonitors.js`

Rewritten from a self-contained composable with its own state to a **thin wrapper around the Pinia `monitors` store**. Uses `storeToRefs()` so all returned values stay reactive. This preserved backward compatibility — existing pages that call `useMonitors()` continue to work without changes.

#### `resources/js/utils/axios.js`

Fixed a pre-existing bug: the 401 interceptor was calling `localStorage.removeItem("token")` (wrong key). Changed to remove `"auth_token"` and `"user"`, and redirect to `/` instead of `/login`.

#### `resources/js/components/modals/AuthModal.vue`

- Now calls `authStore.setSession(token, user)` after a successful sign-in instead of manually writing to `localStorage`.
- Uses the `auth` Pinia store so the rest of the app reactively sees the logged-in user immediately.

#### `resources/js/layouts/MainLayout.vue`

- Reads the logged-in user's name and email from `authStore.user` and displays them in the sidebar footer.
- The logout button calls `authStore.logout()` (which calls `POST /api/signout` then clears local state).
- On mount: if a token exists but user data is missing, it calls `authStore.fetchUser()` to restore the session.

#### `resources/js/pages/Dashboard.vue`

Replaced all hardcoded stats with a `GET /api/dashboard/stats` call. Auto-refreshes every 30 seconds. The animated sparkline chart is still present as a visual element (it does not represent real data — it's intentionally decorative).

#### `resources/js/pages/Monitors.vue`

- Uses `useMonitors()` composable which is backed by the Pinia store.
- Create/Edit/Delete all call real API endpoints.
- Added a "Run Check Now" button (arrow icon) per monitor row that calls `POST /api/monitors/{id}/check`.
- Displays `last_checked_at` as a relative time ("2 min ago").
- Shows SSL expiry warning badge if `ssl_expiring` is true.

#### `resources/js/pages/MonitorDetail.vue`

- Fetches monitor detail (including `uptime_history` and `response_history`) from `GET /api/monitors/{id}`.
- Fetches recent checks from `GET /api/monitors/{id}/checks`.
- Fetches incidents for this monitor from `GET /api/incidents` (filtered client-side).
- The response time chart uses real data when ≥ 2 data points are available; falls back to an animated sparkline otherwise.
- Auto-refreshes every 30 seconds.

#### `resources/js/pages/Incidents.vue`

- Fetches all incidents from `GET /api/incidents` which also returns computed stats (`total`, `critical`, `average_downtime`, `mttr`).
- Filtering by status/severity is done client-side from the full list.

---

## Database Schema

```
users
  id, name, email, password, ...

monitors
  id, user_id (FK)
  name, url, check_interval, expected_status_code, timeout
  track_ssl, ssl_expiry_date, ssl_issuer, ssl_days_remaining, ssl_expiring
  status, uptime, avg_response_time, total_checks, last_checked_at
  created_at, updated_at

monitor_checks
  id, monitor_id (FK cascade)
  success, status_code, response_time, message
  created_at, updated_at

incidents
  id, monitor_id (FK cascade)
  severity (critical | warning)
  status (investigating | identified | resolved)
  message, failed_checks, error_details, impact
  resolved_at, created_at, updated_at

incident_updates
  id, incident_id (FK cascade)
  message, created_at, updated_at
```

**`check_interval` values:** `30s`, `1m`, `5m`, `10m`, `30m`, `1h`

**Monitor status values:** `pending`, `operational`, `degraded`, `down`

---

## API Reference

All routes except `/api/signin` require the header:

```
Authorization: Bearer <token>
```

### Auth

| Method | Endpoint       | Body                  | Returns                    |
| ------ | -------------- | --------------------- | -------------------------- |
| POST   | `/api/signin`  | `{ email, password }` | `{ token, user, success }` |
| POST   | `/api/signout` | —                     | `{ message }`              |
| GET    | `/api/user`    | —                     | User object                |

### Dashboard

| Method | Endpoint               | Returns                                                                                                                                       |
| ------ | ---------------------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| GET    | `/api/dashboard/stats` | `{ total_monitors, operational_count, degraded_count, down_count, average_uptime, average_response_time, system_status, recent_incidents[] }` |

### Monitors

| Method | Endpoint                    | Notes                                                      |
| ------ | --------------------------- | ---------------------------------------------------------- |
| GET    | `/api/monitors`             | List all monitors for the authenticated user               |
| POST   | `/api/monitors`             | Create a monitor; runs an initial check immediately        |
| GET    | `/api/monitors/{id}`        | Single monitor + `uptime_history[]` + `response_history[]` |
| PUT    | `/api/monitors/{id}`        | Partial update (any field)                                 |
| DELETE | `/api/monitors/{id}`        | Cascades to checks and incidents                           |
| GET    | `/api/monitors/{id}/checks` | Last 50 check records                                      |
| POST   | `/api/monitors/{id}/check`  | Manually trigger a check right now                         |

**Create/Update fields:**

```json
{
    "name": "My API",
    "url": "https://example.com/health",
    "check_interval": "5m",
    "expected_status_code": 200,
    "timeout": 30,
    "track_ssl": true
}
```

### Incidents

| Method | Endpoint              | Query params                             | Returns              |
| ------ | --------------------- | ---------------------------------------- | -------------------- |
| GET    | `/api/incidents`      | `?severity=critical`, `?status=resolved` | `{ data[], stats }`  |
| GET    | `/api/incidents/{id}` | —                                        | Incident + updates[] |

---

## How to Run Locally

### Prerequisites

- PHP 8.3+
- Composer
- Node.js 20+
- PostgreSQL 16 (or Docker Desktop)

### Steps

```bash
# 1. Install dependencies
composer install
npm install

# 2. Copy environment file
cp .env.example .env   # or edit .env directly

# 3. Set DB credentials in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pulsecheck
DB_USERNAME=postgres
DB_PASSWORD=123456

# 4. Generate app key (if not already set)
php artisan key:generate

# 5. Run migrations
php artisan migrate

# 6. Seed demo data
php artisan db:seed

# 7. Start Laravel (use a port other than 8000 if it's blocked)
php artisan serve --port=8888

# 8. In a second terminal, start Vite
npm run dev
```

Open `http://localhost:5173` in your browser.

> **Sign in with:** `test@example.com` / `password`

### Running the monitor scheduler

```bash
# One-time run (checks all due monitors right now)
php artisan monitors:check

# Continuous loop (mimics the cron scheduler)
while true; do php artisan schedule:run; sleep 60; done
```

---

## How to Run with Docker

```bash
# Start only the database (recommended for local dev)
docker compose up postgres -d

# Then run the app locally with php artisan serve (see above)

# --- OR --- start everything in containers
docker compose up -d

# The app will be at http://localhost:8080
# Run migrations inside the container:
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

> **Note:** `DB_HOST` must be `postgres` (the service name) when running inside Docker, and `127.0.0.1` when running locally. The current `.env` uses `127.0.0.1` for local dev.

---

## How to Test the API (Manual)

### 1. Sign in and grab your token

```bash
curl -s -X POST http://127.0.0.1:8888/api/signin \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

Copy the `token` value from the response.

### 2. List monitors

```bash
curl http://127.0.0.1:8888/api/monitors \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Create a monitor

```bash
curl -X POST http://127.0.0.1:8888/api/monitors \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "My Test Monitor",
    "url": "https://httpbin.org/status/200",
    "check_interval": "5m",
    "expected_status_code": 200,
    "timeout": 10,
    "track_ssl": true
  }'
```

### 4. Manually trigger a check

```bash
curl -X POST http://127.0.0.1:8888/api/monitors/1/check \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 5. View incidents with a filter

```bash
curl "http://127.0.0.1:8888/api/incidents?status=resolved" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 6. Dashboard stats

```bash
curl http://127.0.0.1:8888/api/dashboard/stats \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Seeded Demo Data

Running `php artisan db:seed` creates the following for `test@example.com`:

| Monitor           | URL                    | Status      | Notes                                       |
| ----------------- | ---------------------- | ----------- | ------------------------------------------- |
| Production API    | httpbin.org/get        | operational | Had a resolved critical incident 2 days ago |
| Marketing Website | httpbin.org/get        | operational | Clean history                               |
| CDN Service       | httpbin.org/status/200 | operational | SSL expiry warning (14 days)                |
| Auth Service      | httpbin.org/delay/1    | operational | Had a resolved warning 5 hours ago          |
| Database Health   | httpbin.org/status/200 | operational | Clean history                               |
| Legacy API        | httpbin.org/status/503 | down        | Ongoing critical incident                   |

Each monitor has **288 check records** (every 5 minutes for 24 hours), giving enough history for the uptime and response time charts to show real data immediately.

---

## Key Concepts for New Developers

### Why `useMonitors()` is a wrapper, not the store itself

Components that used `useMonitors()` existed before Pinia was introduced. Rather than updating every component to call `useMonitorsStore()` directly, the composable was rewritten to wrap the store with `storeToRefs()`. This means:

```js
// This still works in any component — no changes needed:
const { monitors, loading, fetchMonitors } = useMonitors();
```

### Why auth state is in localStorage

The Sanctum token must survive page refreshes. The `auth` Pinia store reads from `localStorage` on initialization, so the user stays logged in. On logout, both the store state and localStorage are cleared.

### How incident creation works

`MonitorService::handleIncidents()` is called after every check:

- **Failure + no active incident** → creates a new incident (`critical` if no response at all, `warning` if wrong status code)
- **Failure + active incident exists** → just increments `failed_checks` on the existing one
- **Success + active incident exists** → marks the incident `resolved` and adds a recovery update

### How monitor status is determined

After every check, the last 3 checks are examined for consecutive failures:

- 0 failures → `operational`
- 1–2 failures (or avg response time > 1000 ms) → `degraded`
- 3+ failures → `down`

### SSL checking

SSL info is read using PHP's `stream_socket_client` with SSL capture enabled, then `openssl_x509_parse()` extracts the expiry date and issuer. It runs automatically after each successful check if `track_ssl` is enabled and the URL starts with `https://`. If the cert expires within 30 days, `ssl_expiring` is set to `true` and a warning badge appears in the UI.
