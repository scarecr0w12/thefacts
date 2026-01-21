# Admin Panel Quick Reference

## Quick Start

```bash
# 1. Run migrations
php artisan migrate

# 2. Create admin user
php artisan db:seed --class=AdminUserSeeder

# 3. Login
# Email: admin@vericrowd.local
# Password: password

# 4. Visit admin panel
# Navigate to: /admin
```

## Admin Panel Routes

| Route | Purpose |
|-------|---------|
| `/admin` | Dashboard with metrics |
| `/admin/claims` | View and manage all claims |
| `/admin/claims/{id}` | Edit specific claim |
| `/admin/evidence` | View and manage all evidence |
| `/admin/evidence/{id}` | Edit specific evidence |
| `/admin/users` | Manage user accounts |
| `/admin/users/{id}` | Edit user and assign admin role |
| `/admin/llm/config` | Configure LLM provider |
| `/admin/llm/usage` | View API usage and costs |
| `/admin/audit-logs` | View activity log |
| `/admin/audit-logs/{id}` | View audit log details |

## Key Files

### Controllers
- `app/Http/Controllers/Admin/DashboardController.php` - Dashboard metrics
- `app/Http/Controllers/Admin/ClaimController.php` - Claims CRUD
- `app/Http/Controllers/Admin/EvidenceController.php` - Evidence CRUD
- `app/Http/Controllers/Admin/UserController.php` - Users and admin assignment
- `app/Http/Controllers/Admin/LLMController.php` - LLM config and usage
- `app/Http/Controllers/Admin/AuditLogController.php` - Activity logs

### Models
- `app/Models/LLMConfig.php` - LLM provider settings
- `app/Models/LLMUsage.php` - API call tracking
- `app/Models/AuditLog.php` - Activity logging

### Middleware
- `app/Http/Middleware/IsAdmin.php` - Checks admin access

### Views
- `resources/views/admin/layout.blade.php` - Admin layout template
- `resources/views/admin/dashboard.blade.php` - Dashboard
- `resources/views/admin/claims/` - Claims management views
- `resources/views/admin/evidence/` - Evidence management views
- `resources/views/admin/users/` - User management views
- `resources/views/admin/llm/` - LLM configuration views
- `resources/views/admin/audit-logs/` - Audit log views

### Routes
- `routes/admin.php` - All admin routes

### Migrations
- `database/migrations/2024_01_21_000004_add_admin_to_users.php`
- `database/migrations/2024_01_21_000005_create_llm_configs_table.php`
- `database/migrations/2024_01_21_000006_create_llm_usages_table.php`
- `database/migrations/2024_01_21_000007_create_audit_logs_table.php`

## Making a User Admin

### In Admin Panel
1. Go to `/admin/users`
2. Click user to edit
3. Check "Admin Access" checkbox
4. Click "Update User"

### Via Tinker
```php
php artisan tinker
>>> $user = User::find(1);
>>> $user->is_admin = true;
>>> $user->save();
```

### Via SQL
```sql
UPDATE users SET is_admin = 1 WHERE id = 1;
```

## Recording LLM Usage

```php
use App\Models\LLMUsage;

LLMUsage::create([
    'user_id' => auth()->id(),
    'provider' => 'openai',
    'model' => 'gpt-4-turbo',
    'action' => 'claim_analysis',
    'input_tokens' => 150,
    'output_tokens' => 250,
    'cost' => 0.005,
    'response_time_ms' => 1200,
    'success' => true,
    'error_message' => null, // only if success = false
]);
```

## Recording Audit Logs

```php
use App\Models\AuditLog;

// Update
AuditLog::log('update', 'Claim', $claim->id, 
    $before, $after);

// Delete
AuditLog::log('delete', 'User', $user->id, 
    $user->toArray());

// Create
AuditLog::log('create', 'Evidence', $evidence->id);
```

## Dashboard Metrics

- **Total Users** - Count of all registered users
- **Total Claims** - Count of all claims submitted
- **Total Evidence** - Count of all evidence pieces
- **Total Votes** - Count of all votes cast
- **Claims This Month** - Claims created in current month
- **Evidence Status** - Breakdown of PENDING/READY/FAILED
- **Verdict Breakdown** - Count by TRUE/FALSE/MIXED/UNVERIFIED

## LLM Configuration

**Provider Options:**
- OpenAI (gpt-4-turbo, gpt-3.5-turbo, etc.)
- Anthropic (claude-3, claude-2, etc.)
- DeepSeek
- Custom

**Key Settings:**
- `api_key` - Encrypted, never displayed
- `model` - Model name/version
- `max_tokens` - Max response length (1-10000)
- `temperature` - Randomness (0=deterministic, 2=creative)
- `cost_per_1k_tokens` - For cost tracking
- `system_prompt` - Custom instructions for LLM
- `enabled` - Toggle LLM integration on/off

## Audit Log Details

Each log entry includes:
- Action (create, update, delete)
- Model (Claim, Evidence, User, etc.)
- Model ID
- User who performed action
- IP address
- User agent
- Timestamp
- Before/after JSON data (for updates)

## Security Notes

- ✓ All admin routes require authentication + `is_admin = true`
- ✓ API keys encrypted and never displayed
- ✓ All sensitive actions logged
- ✓ Before/after data preserved for compliance
- ✓ IP addresses and user agents tracked
- ✓ Can revoke admin access instantly

## Troubleshooting

**Can't access /admin?**
- Check user has `is_admin = true` in database
- Clear cache: `php artisan config:clear`

**LLM config not saving?**
- Verify `llm_configs` table exists
- Check API key is not empty
- Verify file permissions

**No audit logs appearing?**
- Ensure `AuditLog::log()` called in controllers
- Check `audit_logs` table exists
- Verify database connection

## See Also

- [Full Admin Panel Setup Guide](ADMIN_PANEL_SETUP.md)
- [VeriCrowd Documentation](HANDOFF.md)
