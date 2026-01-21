# VeriCrowd MVP - Complete File Structure & Setup Guide

## 📁 Project File Tree

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
│   │   │   ├── VoteController.php
│   │   │   └── Controller.php
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
├── bootstrap/
│   └── app.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── queue.php
│   ├── cache.php
│   ├── redis.php
│   └── session.php
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
├── storage/
│   ├── logs/
│   ├── framework/
│   │   ├── cache/
│   │   └── sessions/
│   ├── postgres/        # (Docker volume)
│   └── redis/           # (Docker volume)
├── vendor/              # (Created by composer install)
├── .env.example
├── .gitignore
├── artisan              # Laravel CLI
├── composer.json
├── docker-compose.yml
├── Dockerfile
└── README.md
```

## 🚀 Quick Start Commands

### Using Docker Compose (RECOMMENDED)

```bash
# 1. Navigate to project
cd /path/to/vericrown

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
docker-compose run --rm app php artisan key:generate

# 4. Start all services (app, postgres, redis, mailpit)
docker-compose up -d

# 5. Run database migrations
docker-compose exec app php artisan migrate

# 6. Seed sample data
docker-compose exec app php artisan db:seed

# 7. Start queue worker (in a new terminal)
docker-compose exec app php artisan queue:work

# Access the application
# Web App: http://localhost:8000
# Mailpit: http://localhost:8025 (for testing emails)
```

### Using Local PHP (8.3+, PostgreSQL, Redis)

```bash
# 1. Navigate to project
cd /path/to/vericrown

# 2. Copy environment file
cp .env.example .env

# 3. Update .env database credentials
nano .env
# Edit DB_HOST, DB_USERNAME, DB_PASSWORD for your PostgreSQL instance

# 4. Install dependencies
composer install

# 5. Generate application key
php artisan key:generate

# 6. Create database
createdb vericrown  # or via PostgreSQL CLI

# 7. Run migrations
php artisan migrate

# 8. Seed sample data
php artisan db:seed

# 9. In terminal 1: Start development server
php artisan serve
# App available at http://localhost:8000

# 10. In terminal 2: Start Redis
redis-server

# 11. In terminal 3: Start queue worker
php artisan queue:work
```

## 🔧 Key Commands Reference

```bash
# Database
php artisan migrate              # Run migrations
php artisan migrate:rollback     # Undo migrations
php artisan db:seed              # Run seeders
php artisan db:seed --class=DatabaseSeeder

# Queue
php artisan queue:work           # Start queue worker
php artisan queue:failed         # View failed jobs
php artisan queue:retry all      # Retry failed jobs
php artisan queue:flush          # Clear all jobs

# Development
php artisan serve                # Start dev server
php artisan tinker               # Interactive shell
php artisan cache:clear          # Clear cache
php artisan config:cache         # Cache config

# Docker
docker-compose up -d             # Start services
docker-compose down              # Stop services
docker-compose logs app          # View app logs
docker-compose exec app bash     # Open app shell
```

## 📊 Data Models Summary

### Claim
- Represents a factual claim (max 280 chars)
- Stored in normalized form for search
- Computed verdict based on evidence votes
- Belongs to a User, has many Evidence items

### Evidence
- Links to a Claim with a URL + excerpt
- Stance: SUPPORTS | REFUTES | CONTEXT
- Status: PENDING → READY / FAILED (async ingestion)
- Extracts title, domain, published date from URL
- Has many Votes from Users

### Vote
- User rates evidence quality: -1 (not helpful) or +1 (helpful)
- Unique per user per evidence
- Triggers verdict recalculation

### User
- Standard Laravel User model
- Has many Claims, Evidence, Votes

## 🔐 Authentication & Authorization

- **Registration & Login**: Laravel session-based auth
- **API**: Laravel Sanctum (token support)
- **Protected Routes**: Middleware `auth` for web, `auth:sanctum` for API
- **Rate Limiting**: Via Redis with key limits per user/IP

## 🔄 Verdict Calculation Flow

1. User votes on evidence → Vote created
2. VoteController calls VerdictService::computeVerdict()
3. Service counts READY evidence:
   - < 2 items → UNVERIFIED (confidence 0%)
   - score = supports_votes - refutes_votes
   - score >= 3 → TRUE (confidence: 50% + 10% per point, max 90%)
   - score <= -3 → FALSE (confidence: 50% + 10% per point, max 90%)
   - otherwise → MIXED (confidence 50%)
4. Claim updated with new verdict + confidence
5. Frontend displays updated badge on claim page

## 📮 Queue Job: IngestEvidenceUrlJob

Triggered when evidence is submitted:

1. **Fetch**: HTTP GET with timeouts (15s), user-agent, size limits
2. **Extract Title**: Parse `<title>` tag from HTML
3. **Get Domain**: Extract from URL via `parse_url()`
4. **Detect Published Date**: Try meta tags in order:
   - `article:published_time`
   - `og:updated_time`
   - `pubdate`
   - `date`
5. **Update**: Set status to READY or FAILED with error message
6. **Recompute Verdict**: Call VerdictService on the claim

Queue driver: **Redis** (also supports sync for testing)

## 🛡️ Security Features

- **SSRF Protection**: Block private IP ranges in evidence URLs
- **Rate Limiting**: 10 claims/min, 15 evidence/min per user
- **Validation**: Form requests with custom rules
- **Auth**: All mutations require authentication
- **Unique Votes**: One vote per user per evidence
- **Timestamps**: All records timestamped for audit trails

## 🌐 API Endpoints (with throttling)

### Public (60 req/min)
```
GET  /api/claims              # List claims (paginated)
GET  /api/claims/{id}         # Get claim detail
```

### Authenticated (30 req/min)
```
POST /api/claims              # Create claim
POST /api/claims/{id}/evidence  # Add evidence
POST /api/evidence/{id}/vote    # Vote on evidence
```

## 📝 Test Credentials (from seed)

```
john@example.com : password
jane@example.com : password
```

## 📧 Email Testing

Mailpit is included in docker-compose:
- **SMTP**: localhost:1025
- **Web UI**: http://localhost:8025

No real emails sent in local development.

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Port 8000 in use | `docker-compose exec app php artisan serve --port=8001` or kill the process |
| PostgreSQL won't start | Check storage/postgres permissions: `chmod 755 storage/postgres` |
| Queue not working | Ensure Redis is running: `docker-compose ps redis` |
| Migrations fail | Clear app cache: `php artisan cache:clear && php artisan config:cache` |
| SSRF validation error | Check if URL host resolves to private IP range |

## 📚 Next Steps / Production

1. **Environment**: Set `APP_DEBUG=false`, use strong secrets
2. **Queue Supervisor**: Use Laravel Horizon or systemd supervisor
3. **Database Backups**: Set up PostgreSQL backup schedule
4. **SSL/HTTPS**: Use Let's Encrypt behind reverse proxy (Nginx)
5. **Logging**: Configure remote logging service
6. **Monitoring**: Set up APM (e.g., New Relic, DataDog)
7. **Cache**: Configure Redis persistence
8. **CDN**: Serve static assets from CDN
9. **Limits**: Implement stricter rate limits in production
10. **Cleanup Jobs**: Add jobs to delete old failed evidence records

## 📖 Useful Links

- [Laravel 11 Docs](https://laravel.com/docs/11.x)
- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)
- [Laravel Queue](https://laravel.com/docs/11.x/queues)
- [Laravel Rate Limiting](https://laravel.com/docs/11.x/rate-limiting)
- [Laravel Blade](https://laravel.com/docs/11.x/blade)

---

**VeriCrowd MVP** is ready to deploy! Start with Docker Compose for quickest setup.
