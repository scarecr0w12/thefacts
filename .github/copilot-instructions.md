# VeriCrowd - AI Coding Agent Instructions

## Project Overview

**VeriCrowd** is a crowdsourced fact-checking platform built with Laravel 11. It enables users to submit claims, provide evidence via URLs, and vote on evidence quality. Verdicts are auto-calculated based on weighted evidence votes.

**Key Goals**: Zero-config setup, async processing, security-first (SSRF protection), API + web interfaces.

---

## Architecture & Data Flow

### Core Domain Model

```
User → creates → Claim (text, verdict, confidence)
              ↓
            Evidence (url, stance: SUPPORTS/REFUTES/CONTEXT, status: PENDING/READY/FAILED)
              ↓
            Vote (value: +1/-1 per user per evidence)
```

### Verdict Calculation Pipeline

1. **Evidence Submission** → [EvidenceController::store()](app/Http/Controllers/EvidenceController.php) creates Evidence with `status: PENDING`
2. **Async Ingestion** → [IngestEvidenceUrlJob](app/Jobs/IngestEvidenceUrlJob.php) fetches URL, extracts metadata (title, domain, publish_date), sets `status: READY` or `FAILED`
3. **Voting** → [VoteController](app/Http/Controllers/VoteController.php) allows users to upvote/downvote evidence
4. **Verdict Recompute** → [VerdictService::computeVerdict()](app/Services/VerdictService.php) runs after URL ingestion or vote changes
   - Requires ≥2 ready evidence pieces, otherwise `verdict: UNVERIFIED`
   - Sums votes by stance (`SUPPORTS` votes - `REFUTES` votes)
   - Score ≥3 → TRUE, ≤-3 → FALSE, else MIXED
   - Confidence = 50 + (10 * score), capped at 90%

**Critical Pattern**: Verdict is NOT persisted until async job completes. Plan for eventual consistency.

---

## Key Patterns & Conventions

### 1. **Dual Routes (Web + API)**

Web routes handle HTML returns; API routes return JSON via `request()->expectsJson()`:
- [routes/web.php](routes/web.php) — Traditional Blade templates
- [routes/api.php](routes/api.php) — Sanctum-authenticated JSON endpoints

Both controllers check `request()->expectsJson()` and return accordingly. See [EvidenceController](app/Http/Controllers/EvidenceController.php#L16-L44).

### 2. **SSRF Protection on URLs**

[StoreEvidenceRequest](app/Http/Requests/StoreEvidenceRequest.php) validates URLs in `prepareForValidation()`:
- Blocks private IP ranges (127.0.0.1, 192.168.x.x, 10.x.x.x, 172.16-31.x.x)
- Uses `filter_var(FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)`
- **Always validate in FormRequest**, not controller

### 3. **Service Injection for Business Logic**

[VerdictService](app/Services/VerdictService.php) is injected into jobs/controllers:
```php
app(VerdictService::class)->computeVerdict($claim);
```
This keeps logic testable and reusable. Don't embed verdict logic in controllers.

### 4. **Status Tracking for Async Jobs**

Evidence has three states:
- `PENDING` — URL fetch in progress
- `READY` — Metadata extracted, ready for voting
- `FAILED` — HTTP error or timeout (logged, user notified)

Filter queries with `where('status', 'READY')` when calculating verdicts.

### 5. **Vote Upsert Pattern**

Users can only vote once per evidence. [VoteController](app/Http/Controllers/VoteController.php#L11-L18) checks existing vote and updates OR creates:
```php
$vote = $evidence->votes()->where('user_id', $user->id)->first();
if ($vote) {
    $vote->update(['value' => $request->value]);
} else {
    $evidence->votes()->create(['user_id' => $user->id, 'value' => $request->value]);
}
```

---

## File Organization

| Layer | Files | Purpose |
|-------|-------|---------|
| **Routes** | [web.php](routes/web.php), [api.php](routes/api.php) | HTTP endpoints + middleware |
| **Models** | [Claim.php](app/Models/Claim.php), [Evidence.php](app/Models/Evidence.php), [Vote.php](app/Models/Vote.php) | Domain entities + relationships |
| **Controllers** | [ClaimController.php](app/Http/Controllers/ClaimController.php), [EvidenceController.php](app/Http/Controllers/EvidenceController.php), [VoteController.php](app/Http/Controllers/VoteController.php) | HTTP handlers |
| **Requests** | [StoreClaimRequest.php](app/Http/Requests/StoreClaimRequest.php), [StoreEvidenceRequest.php](app/Http/Requests/StoreEvidenceRequest.php) | Validation + authorization |
| **Services** | [VerdictService.php](app/Services/VerdictService.php) | Business logic (reusable) |
| **Jobs** | [IngestEvidenceUrlJob.php](app/Jobs/IngestEvidenceUrlJob.php) | Async tasks (URL fetching) |
| **Views** | [resources/views/](resources/views/) | Blade templates (auth, claims, installer) |
| **Migrations** | [database/migrations/](database/migrations/) | Schema definitions |

---

## Common Workflows

### Adding a New Feature

1. **Add migration** if modifying schema → `php artisan make:migration`
2. **Update models** → Add properties to `$fillable`, relationships in Model
3. **Create FormRequest** → `StoreXyzRequest` with rules + authorization
4. **Create controller** → Handle web + API both (check `expectsJson()`)
5. **Add routes** → Both [web.php](routes/web.php) and [api.php](routes/api.php)
6. **Extract logic** → Move business logic to Service if reusable
7. **Create Blade template** — Responsive TailwindCSS in [resources/views/](resources/views/)

### Debugging Async Jobs

1. Check `evidence.status` in DB — is it PENDING, READY, or FAILED?
2. Review logs in `storage/logs/laravel.log` — [IngestEvidenceUrlJob](app/Jobs/IngestEvidenceUrlJob.php) logs all errors
3. Verify queue is running — `php artisan queue:work` (local) or supervisor (production)
4. Test verdict recalc manually: `VerdictService::computeVerdict($claim)`

### Testing Verdict Logic

[VerdictService](app/Services/VerdictService.php) is pure logic — no DB calls within algorithm:
- Minimal evidence (<2 ready) → UNVERIFIED
- Test score thresholds: -3 to +3 tests all three verdicts
- Confidence scaling: Check 50-90 range

---

## Configuration & Deployment

- **Database**: MariaDB (MySQL 5.7+) via [config/database.php](config/database.php)
- **Queue**: Async jobs in [config/queue.php](config/queue.php) — ensure queue worker is running
- **Cache**: [config/cache.php](config/cache.php) — for session/cache layer
- **Authentication**: Laravel Sanctum for API tokens, traditional sessions for web
- **Installation**: Browser-based [installer](resources/views/installer/index.blade.php) sets `.env` values

---

## Security Considerations

- **SSRF**: Always use [StoreEvidenceRequest::isPrivateIp()](app/Http/Requests/StoreEvidenceRequest.php#L42)
- **Rate Limiting**: API routes throttled (60/min read, 30/min write); web routes unthrottled
- **Authorization**: Form requests check `authorize()`, controllers use `middleware('auth')`
- **Secrets**: Never commit `.env`; use `.env.example` template

---

## Testing Notes

- Models use `HasFactory` trait — seed data via [DatabaseSeeder](database/seeders/DatabaseSeeder.php)
- Controllers are testable via HTTP tests (use Laravel TestCase)
- Services are pure logic — test without DB mocks
- Jobs dispatch async — monitor queue for completion in tests

---

## Quick Commands

```bash
# Setup
php artisan migrate
php artisan db:seed

# Development
php artisan serve                    # Web server
php artisan queue:work               # Async job processor

# Debugging
php artisan tinker                   # Interactive shell
php artisan cache:clear && php artisan config:clear

# Check status
php artisan migrate:status
php artisan queue:failed              # View failed jobs
```

---

## Common Pitfalls

1. **Forget to dispatch queue worker** — Async jobs won't run locally without `php artisan queue:work`
2. **Verdict doesn't update** — Ensure [VerdictService::computeVerdict()](app/Services/VerdictService.php) is called after vote/evidence changes
3. **SSRF validation bypassed** — Validate URLs in FormRequest, not controller
4. **API returns HTML** — Check `request()->expectsJson()` in controller response
5. **Missing relationships** — Verify model `HasMany`/`BelongsTo` declarations match migrations
