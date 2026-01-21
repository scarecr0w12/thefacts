# VeriCrowd - Crowdsourced Fact-Checking Platform

An MVP Laravel 11 application for community-driven fact-checking with async evidence ingestion, voting systems, and automated verdict calculation.

## Tech Stack

- **Backend**: Laravel 11 + PHP 8.3
- **Database**: MariaDB (MySQL 5.7+ compatible)
- **Frontend**: Laravel Blade + TailwindCSS
- **Installation**: Browser-based installer (zero configuration)

## Features

- User registration and authentication (Laravel Sanctum)
- Submit and view claims (max 280 characters)
- Add evidence with URL ingestion (async job-based)
- Vote on evidence quality (upvote/downvote)
- Automated verdict calculation based on evidence votes
- Rate limiting on claim creation and evidence submission
- SSRF protection on evidence URLs
- REST JSON API endpoints
- Responsive Blade templates with TailwindCSS

## Project Structure

```
vericrown/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── ClaimController.php
│   │   │   ├── EvidenceController.php
│   │   │   └── VoteController.php
│   │   └── Requests/
│   │       ├── StoreClaimRequest.php
│   │       ├── StoreEvidenceRequest.php
│   │       └── StoreVoteRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Claim.php
│   │   ├── Evidence.php
│   │   └── Vote.php
│   ├── Services/
│   │   └── VerdictService.php
│   └── Jobs/
│       └── IngestEvidenceUrlJob.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2024_01_21_000001_create_claims_table.php
│   │   ├── 2024_01_21_000002_create_evidence_table.php
│   │   └── 2024_01_21_000003_create_votes_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── layout.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       └── claims/
│           ├── index.blade.php
│           ├── create.blade.php
│           └── show.blade.php
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── queue.php
│   ├── cache.php
│   ├── redis.php
│   └── session.php
├── bootstrap/
│   └── app.php
├── docker-compose.yml
├── Dockerfile
├── .env.example
├── composer.json
└── README.md
```

## Quick Start (Local Development)

### Prerequisites

- Docker and Docker Compose
- OR: PHP 8.3, Composer, PostgreSQL, Redis

### Option 1: Using Docker Compose (Recommended)

```bash
# 1. Clone/navigate to the project
cd /path/to/vericrown

# 2. Copy environment file
cp .env.example .env

# 3. Generate app key
docker-compose run --rm app php artisan key:generate

# 4. Start services
docker-compose up -d

# 5. Run migrations
docker-compose exec app php artisan migrate

# 6. Seed database with sample data
docker-compose exec app php artisan db:seed

# 7. Start queue worker in a new terminal
docker-compose exec app php artisan queue:work

# Access the app
# Web: http://localhost:8000
# Mailpit (email testing): http://localhost:8025
```

### Option 2: Local PHP Development

```bash
# 1. Install dependencies
composer install

# 2. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 3. Create database and run migrations
# First ensure PostgreSQL is running
php artisan migrate

# 4. Seed sample data
php artisan db:seed

# 5. In one terminal, start the development server
php artisan serve

# 6. In another terminal, start the queue worker
php artisan queue:work

# 7. Start Redis server (if not already running)
redis-server
```

## Database Schema

### users
- id (bigint, PK)
- name (string)
- email (string, unique)
- password (string, hashed)
- timestamps

### claims
- id (bigint, PK)
- text (string, 280 chars)
- normalized_text (string, indexed)
- context_url (nullable)
- created_by (FK → users.id)
- verdict (enum: UNVERIFIED, TRUE, FALSE, MIXED, MISLEADING, UNVERIFIABLE)
- confidence (unsigned tinyint 0-100)
- timestamps

### evidence
- id (bigint, PK)
- claim_id (FK → claims.id)
- url (text)
- title (nullable)
- publisher_domain (string, indexed)
- published_at (nullable timestamp)
- stance (enum: SUPPORTS, REFUTES, CONTEXT)
- excerpt (text)
- status (enum: PENDING, READY, FAILED)
- error (nullable text)
- created_by (FK → users.id)
- timestamps

### votes
- id (bigint, PK)
- user_id (FK → users.id)
- evidence_id (FK → evidence.id)
- value (smallint, -1 or +1)
- unique(user_id, evidence_id)
- timestamps

## API Endpoints

### Public Endpoints

```
GET /api/claims
GET /api/claims/{id}
```

### Authenticated Endpoints (Rate-Limited: 30 requests/min)

```
POST /api/claims
POST /api/claims/{id}/evidence
POST /api/evidence/{id}/vote
```

## Web Routes

```
GET  /                          # Claim list with search
GET  /register                  # Registration form
POST /register                  # Submit registration
GET  /login                     # Login form
POST /login                     # Submit login
POST /logout                    # Logout (auth required)
GET  /claims/create             # Create claim form (auth required)
POST /claims                    # Submit claim (auth required)
GET  /claims/{id}               # View claim detail
POST /claims/{id}/evidence      # Submit evidence (auth required)
POST /evidence/{id}/vote        # Vote on evidence (auth required)
```

## Verdict Calculation Logic

When evidence or votes change:

1. Count READY evidence items
2. If < 2 evidence items: **UNVERIFIED** (confidence = 0)
3. Otherwise:
   - score = (sum votes on SUPPORTS) - (sum votes on REFUTES)
   - score >= 3 → **TRUE** (confidence = min(90, 50 + 10×score))
   - score <= -3 → **FALSE** (confidence = min(90, 50 + 10×|score|))
   - otherwise → **MIXED** (confidence = 50)

## Queue Jobs

### IngestEvidenceUrlJob

Async job triggered when evidence is submitted:

- Fetches the URL with timeouts and size limits
- Extracts HTML title
- Parses publisher domain from URL
- Attempts to detect published date from meta tags:
  - `article:published_time`
  - `og:updated_time`
  - `pubdate`
  - `date`
- Updates evidence with extracted metadata or error
- Recomputes claim verdict

**Queue Configuration**: Uses Redis (default) or sync for testing.

### Running Queue Worker

```bash
# Docker
docker-compose exec app php artisan queue:work

# Local
php artisan queue:work

# With daemon/supervisor (production)
php artisan queue:work --daemon
```

## Validation & Security

### Form Validation

- **Claims**: Required, 3-280 characters
- **Evidence URLs**: Valid URL, SSRF protection (no private IPs)
- **Evidence Stance**: One of SUPPORTS, REFUTES, CONTEXT
- **Evidence Excerpt**: Required, 5-2000 characters
- **Votes**: Value -1 or +1 only

### Rate Limiting

Uses Laravel RateLimiter + Redis:

- Claim creation: 10 claims per minute per user
- Evidence submission: 15 evidence per minute per user
- API (unauthenticated): 60 requests per minute
- API (authenticated): 30 requests per minute

### Authentication

- Session-based via cookies (web)
- Sanctum tokens supported (API)
- Auth middleware required for mutations

## Sample Credentials

After seeding:

```
Email: john@example.com
Password: password

Email: jane@example.com
Password: password
```

## Environment Variables

See `.env.example` for full list. Key variables:

```
APP_NAME=VeriCrowd
APP_ENV=local
APP_DEBUG=true
APP_KEY=<auto-generated>

DB_CONNECTION=pgsql
DB_HOST=postgres (Docker) / 127.0.0.1 (Local)
DB_PORT=5432
DB_DATABASE=vericrown
DB_USERNAME=postgres
DB_PASSWORD=password

REDIS_HOST=redis (Docker) / 127.0.0.1 (Local)
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis

MAIL_MAILER=mailpit
MAIL_HOST=localhost
MAIL_PORT=1025
```

## Troubleshooting

### Database Connection Issues

```bash
# Check if PostgreSQL is running
docker-compose ps postgres

# View logs
docker-compose logs postgres
```

### Queue Not Processing

```bash
# Check if Redis is running
docker-compose ps redis

# Monitor queue
docker-compose exec app php artisan queue:failed

# Retry failed jobs
docker-compose exec app php artisan queue:retry all
```

### Permission Issues

```bash
# Reset storage permissions
docker-compose exec app chmod -R 755 storage bootstrap/cache
```

## Development Notes

- Migrations use UUIDs alternatively via `uuid()` instead of `bigIncrements()`
- Rate limiting configured per user via authenticated sessions
- Evidence ingestion handles timeouts (15s) and max file size (10MB)
- SSRF protection blocks private IP ranges
- Verdict recomputed on every vote or evidence status change
- Queue uses Redis but falls back to sync for testing

## Production Considerations

- Switch `APP_DEBUG=false`
- Use environment-specific database credentials
- Configure proper Redis persistence
- Use queue supervisor (Laravel Horizon recommended)
- Implement rate limiting at reverse proxy level
- Add HTTPS enforcement
- Set up proper logging and monitoring
- Use environment-based mail configuration

## License

MIT
