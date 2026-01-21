# VeriCrowd Admin Panel - Setup Guide

## Overview

The VeriCrowd Admin Panel provides comprehensive management tools for:
- 📊 Dashboard with key metrics
- 📝 Claims Management
- 📑 Evidence Management
- 👥 User Management & Admin Assignment
- 🤖 LLM Configuration & Usage Analytics
- 📋 Complete Audit Logs

## Installation

### 1. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `llm_configs` - LLM provider configuration
- `llm_usages` - API call tracking and cost analysis
- `audit_logs` - Complete activity log
- `users.is_admin` - Admin role flag

### 2. Create an Admin User

Option A: Using Seeder (Recommended)
```bash
php artisan db:seed --class=AdminUserSeeder
```

This creates:
- Email: `admin@vericrowd.local`
- Password: `password`

⚠️ **Change this password immediately in production!**

Option B: Manually in Tinker
```bash
php artisan tinker
>>> $user = User::find(1);
>>> $user->is_admin = true;
>>> $user->save();
```

### 3. Access Admin Panel

Navigate to: `/admin`

You'll be redirected to login if not authenticated.

## Features

### Dashboard (`/admin`)

**Key Metrics:**
- Total users, claims, evidence pieces, votes
- Evidence status breakdown (Pending, Ready, Failed)
- Claims this month
- Verdict distribution (True, False, Mixed, Unverified)
- Recent claims and evidence activity

### Claims Management (`/admin/claims`)

**Capabilities:**
- View all claims with creator and verdict
- Edit claim text, verdict, and confidence
- Delete claims (with cascade to evidence and votes)
- View detailed claim information
- Track evidence count per claim

### Evidence Management (`/admin/evidence`)

**Capabilities:**
- View all evidence pieces
- Filter by status (PENDING, READY, FAILED)
- View stance (SUPPORTS, REFUTES, CONTEXT)
- Edit evidence status and stance
- View error messages for failed ingestions
- See vote counts per evidence
- Access detailed evidence view with vote breakdown

### User Management (`/admin/users`)

**Capabilities:**
- View all users with activity counts
- Assign/revoke admin access
- Edit user details (name, email)
- Delete user accounts
- View user activity summary
- See recent claims created by user

### LLM Configuration (`/admin/llm/config`)

**Settings:**
- Provider selection (OpenAI, Anthropic, DeepSeek, Custom)
- Model name configuration
- API Key management (encrypted, never displayed)
- Max tokens limit
- Temperature setting (0-2, higher = more creative)
- Cost tracking per 1K tokens
- System prompt customization
- Enable/disable LLM integration

### LLM Usage Analytics (`/admin/llm/usage`)

**Metrics:**
- Total cost breakdown
- Token usage (input/output)
- Daily cost trends (7/30/90 day views)
- Cost by action type
- Average response times
- API call success/failure rates
- Recent API calls with details

**Filtering:**
- Time period selection (7, 30, 90 days)
- View by action type
- Cost calculations and projections

### Audit Logs (`/admin/audit-logs`)

**Tracking:**
- All create/update/delete actions
- User who performed action
- IP address and user agent
- Before/after data comparison
- Searchable by model type
- Detailed view with JSON comparison

## Database Schema

### llm_configs
```
id, provider, api_key, model, enabled, max_tokens, 
temperature, system_prompt, cost_per_1k_tokens, 
timestamps
```

### llm_usages
```
id, user_id, provider, model, action, input_tokens, 
output_tokens, cost, response_time_ms, success, 
error_message, timestamps
```

### audit_logs
```
id, user_id, action, model, model_id, before (JSON), 
after (JSON), ip_address, user_agent, timestamps
```

### users
```
... existing fields ... is_admin (boolean, indexed)
```

## API Integration

### Recording LLM Usage

In your LLM service:

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
]);
```

### Recording Audit Events

In your controllers:

```php
use App\Models\AuditLog;

// On update
AuditLog::log('update', 'Claim', $claim->id, 
    $before, $claim->fresh()->toArray());

// On delete
AuditLog::log('delete', 'User', $user->id, 
    $user->toArray());

// On create
AuditLog::log('create', 'Evidence', $evidence->id);
```

## Security

### Admin Middleware
- All admin routes protected by `is_admin` middleware
- Automatically checks `users.is_admin` field
- Denies access with 403 Forbidden if not admin

### Data Protection
- LLM API keys stored encrypted
- Never displayed in UI (shows "✓ Configured" only)
- Audit logs track all sensitive changes
- IP addresses and user agents logged
- Before/after data preserved for compliance

### Permissions
- Only admins can access `/admin`
- Only admins can modify users/configuration
- Actions logged with user attribution

## Usage Tracking

The system automatically tracks all LLM API calls for:
- Cost analysis and budgeting
- Performance monitoring
- Usage trends and patterns
- Failure tracking and debugging

Cost calculations are based on:
- Input tokens × (cost_per_1k / 1000)
- Output tokens × (cost_per_1k / 1000)
- Total = input_cost + output_cost

## Troubleshooting

### Cannot access /admin
- Verify user has `is_admin = true` in database
- Check middleware registration in `bootstrap/app.php`
- Clear config cache: `php artisan config:clear`

### LLM config not saving
- Verify `llm_configs` table exists
- Check `api_key` field is not empty for updates
- Verify file permissions for encrypted storage

### Audit logs not appearing
- Ensure `AuditLog::log()` is called in controllers
- Check `audit_logs` table exists
- Verify database connection is active

### Usage stats showing $0.00
- Set `cost_per_1k_tokens` in LLM config
- Ensure `LLMUsage::create()` includes cost field
- Check database values are not NULL

## File Structure

```
Admin Interface Files:
├── app/
│   ├── Models/
│   │   ├── LLMConfig.php
│   │   ├── LLMUsage.php
│   │   └── AuditLog.php
│   ├── Http/
│   │   ├── Controllers/Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── ClaimController.php
│   │   │   ├── EvidenceController.php
│   │   │   ├── UserController.php
│   │   │   ├── LLMController.php
│   │   │   └── AuditLogController.php
│   │   └── Middleware/
│   │       └── IsAdmin.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_21_000004_add_admin_to_users.php
│   │   ├── 2024_01_21_000005_create_llm_configs_table.php
│   │   ├── 2024_01_21_000006_create_llm_usages_table.php
│   │   └── 2024_01_21_000007_create_audit_logs_table.php
│   └── seeders/
│       └── AdminUserSeeder.php
├── routes/
│   └── admin.php
└── resources/
    └── views/
        └── admin/
            ├── layout.blade.php
            ├── dashboard.blade.php
            ├── claims/
            ├── evidence/
            ├── users/
            ├── llm/
            └── audit-logs/
```

## Next Steps

1. Run migrations: `php artisan migrate`
2. Create admin user: `php artisan db:seed --class=AdminUserSeeder`
3. Login with admin credentials
4. Configure LLM settings at `/admin/llm/config`
5. Monitor usage at `/admin/llm/usage`
6. Review audit logs at `/admin/audit-logs`

## Support

For issues or questions, refer to the [VeriCrowd Documentation](HANDOFF.md)
