# VeriCrowd Quick Reference

## Start the App (30 seconds)

```bash
cd vericrown
cp .env.example .env
docker-compose run --rm app php artisan key:generate
docker-compose up -d
docker-compose exec app php artisan migrate db:seed
docker-compose exec app php artisan queue:work  # New terminal
```

**Open**: http://localhost:8000

**Login**: john@example.com / password

---

## Key Routes

| Path | Purpose |
|------|---------|
| `/` | Claim list with search |
| `/register` | Create account |
| `/login` | Login |
| `/claims/{id}` | View claim + evidence + vote |
| `/claims/create` | Submit new claim |

---

## API Endpoints

```
GET  /api/claims              # List all claims
GET  /api/claims/{id}         # Get claim detail
POST /api/claims              # Create claim (auth)
POST /api/claims/{id}/evidence  # Add evidence (auth)
POST /api/evidence/{id}/vote    # Vote (auth)
```

---

## Database Commands

```bash
docker-compose exec app php artisan migrate        # Run migrations
docker-compose exec app php artisan migrate:rollback  # Undo
docker-compose exec app php artisan db:seed        # Load sample data
docker-compose exec app php artisan tinker         # Interactive shell
```

---

## Queue Commands

```bash
docker-compose exec app php artisan queue:work    # Start worker
docker-compose exec app php artisan queue:failed   # View failed jobs
docker-compose exec app php artisan queue:retry all  # Retry failures
```

---

## Useful URLs (Local)

| Service | URL |
|---------|-----|
| App | http://localhost:8000 |
| Mailpit | http://localhost:8025 |
| PostgreSQL | localhost:5432 |
| Redis | localhost:6379 |

---

## Models & Relationships

```
User
  ├─ hasMany Claims (as created_by)
  ├─ hasMany Evidence (as created_by)
  └─ hasMany Votes

Claim
  ├─ belongsTo User (creator)
  └─ hasMany Evidence

Evidence
  ├─ belongsTo Claim
  ├─ belongsTo User (creator)
  └─ hasMany Votes

Vote
  ├─ belongsTo User
  └─ belongsTo Evidence
```

---

## Verdict Logic

```
< 2 evidence     → UNVERIFIED (0% confidence)
score >= 3      → TRUE (confidence: 50-90%)
score <= -3     → FALSE (confidence: 50-90%)
-2 to +2        → MIXED (50% confidence)

score = SUPPORTS_votes - REFUTES_votes
```

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| Port 8000 in use | `lsof -i :8000` then kill, or use different port |
| Can't login | Run `docker-compose exec app php artisan db:seed` |
| Queue not working | Check: `docker-compose logs redis` |
| Migration fails | `docker-compose exec app php artisan cache:clear` |

---

## File Structure (Key Files)

```
app/
  ├─ Models/ (User, Claim, Evidence, Vote)
  ├─ Http/Controllers/ (Claim, Evidence, Vote, Auth)
  ├─ Http/Requests/ (Validation logic)
  ├─ Services/VerdictService.php
  └─ Jobs/IngestEvidenceUrlJob.php

database/
  ├─ migrations/
  └─ seeders/DatabaseSeeder.php

resources/views/
  ├─ layout.blade.php
  ├─ auth/ (login, register)
  └─ claims/ (index, create, show)

routes/
  ├─ web.php (Session-based routes)
  └─ api.php (JSON endpoints)

config/ (database, queue, cache, redis, session, app)
```

---

## Important Classes

| Class | File | Purpose |
|-------|------|---------|
| VerdictService | app/Services/ | Compute claim verdict |
| IngestEvidenceUrlJob | app/Jobs/ | Async URL scraping |
| ClaimController | app/Http/Controllers/ | Claim CRUD + API |
| EvidenceController | app/Http/Controllers/ | Evidence CRUD + API |
| VoteController | app/Http/Controllers/ | Vote creation + API |

---

## Test Credentials

```
Email: john@example.com
Password: password

Email: jane@example.com
Password: password
```

---

## Environment Variables

See `.env.example`:

```
APP_NAME=VeriCrowd
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=pgsql
DB_HOST=postgres
REDIS_HOST=redis
QUEUE_CONNECTION=redis
```

---

## Features Summary

✅ User auth (register/login)
✅ Claim submission (280 chars)
✅ Evidence submission with URL ingestion
✅ Voting on evidence (upvote/downvote)
✅ Auto verdict calculation
✅ Search claims
✅ Async queue processing
✅ Rate limiting
✅ SSRF protection
✅ REST JSON API
✅ Blade templates with TailwindCSS
✅ PostgreSQL + Redis
✅ Docker Compose setup

---

## Documentation

- `README.md` - Full guide
- `SETUP.md` - Getting started
- `IMPLEMENTATION.md` - What's included
- `QUICK_REF.md` - This file

---

**Built with Laravel 11 + PHP 8.3 + PostgreSQL + Redis**

Questions? Check README.md or SETUP.md for detailed docs.
