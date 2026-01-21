# VeriCrowd - Installation & Setup (MariaDB + Browser Installer)

This version of VeriCrowd includes a **browser-based installer** for easy setup on cPanel or any hosting environment. It uses **MariaDB** for better compatibility.

## What Changed

✅ **MariaDB** instead of PostgreSQL (better for shared hosting)
✅ **Browser-based installer** - no manual configuration needed  
✅ **cPanel ready** - optimized for shared hosting
✅ **Zero dependencies** - no Docker, no .env files

---

## Quick Start (cPanel / Shared Hosting)

**See [CPANEL_DEPLOYMENT.md](CPANEL_DEPLOYMENT.md) for complete instructions.**

1. Upload files to public_html via FTP/SFTP
2. SSH: `composer install --no-dev --optimize-autoloader`
3. Set permissions: `chmod -R 755 storage bootstrap/cache`
4. Visit `https://yourdomain.com/install` in your browser
5. Fill out the installer form with your database credentials
6. Done! You're installed.

---

## Quick Start (Local PHP Development)

```bash
composer install
# Create database and user in MariaDB locally

# Start dev server
php artisan serve

# Visit http://localhost:8000/install
# Open: http://localhost:8000/install
# Complete the installer form
```

---

## The Installation Wizard

When you first visit your VeriCrowd installation, you'll be redirected to `/install` where you'll enter:

### Application Settings
- **Application Name** (default: VeriCrowd)
- **Application URL** (where your site will be hosted)

### Database Configuration
- **Database Host** (usually `localhost`)
- **Database Port** (default: `3306`)
- **Database Name** (database name, no spaces)
- **Database Username** (MariaDB user)
- **Database Password** (MariaDB password)

### Administrator Account
- **Admin Name** (your name)
- **Admin Email** (login email)
- **Admin Password** (at least 8 characters)

Once submitted, the installer will:
1. ✅ Test database connection
2. ✅ Update config files with your database settings
3. ✅ Run database migrations
4. ✅ Create your admin user
5. ✅ Mark installation as complete

Then you'll be logged in and ready to use VeriCrowd!

---

## Database Requirements

### MariaDB / MySQL

**Minimum version**: MariaDB 10.3 or MySQL 5.7

**Required access**:
- Can connect to database server
- Can create/modify databases
- Can create/modify tables
- Can insert data

**For cPanel**:
1. Log into cPanel
2. Go to "MySQL Databases"
3. Create a database (e.g., `mysite_vericrown`)
4. Create a database user with a strong password
5. Add the user to the database with **ALL privileges**

### Getting Database Credentials in cPanel

**Database Host**: Usually `localhost` (occasionally `127.0.0.1` or a remote hostname)

**Database Name**: The database you created (e.g., `mysite_vericrown`)

**Database Username**: The database user you created

**Database Password**: The password you set for that user

---

## Installation Troubleshooting

### Installer Not Loading

**Problem**: Seeing a blank page or different content at `/install`

**Solution**: 
1. Check that `.env` file exists
2. If already installed, you can reset by:
   ```bash
   nano .env
   # Change: INSTALLATION_COMPLETE=false
   ```

### Database Connection Failed

**Problem**: "Failed to connect to database" error

**Steps**:
1. Verify credentials in cPanel
2. Test connection manually:
   ```bash
   mysql -h localhost -u dbuser -p dbname
   # Enter password when prompted
   ```
3. If using cPanel and not localhost:
   - Try `127.0.0.1` instead
   - Try the actual hostname provided by cPanel
4. Verify database user has ALL privileges

### Permission Errors After Installation

**Problem**: Directories not writable

**Solution**:
```bash
chmod -R 755 bootstrap/cache storage
# If that doesn't work, try:
chown -R nobody:nobody bootstrap/cache storage
```

### Stuck on Installer / Won't Complete

**Problem**: Installer keeps showing after submission

**Solution**:
1. Check error logs:
   ```bash
   tail -50 storage/logs/laravel.log
   ```
2. Check `.env` was actually created/updated:
   ```bash
   cat .env | grep DB_
   ```
3. Try manually:
   ```bash
   php artisan migrate --force
   php artisan db:seed
   ```

---

## After Installation

### First Login

- **Email**: The admin email you set during installation
- **Password**: The admin password you set

### Change Your Password

After logging in:
1. You can change your password in your account settings
2. Recommended: Do this immediately after installation

### Create More Users

Users can register themselves via `/register` page, or you can create them manually.

### Seed Sample Data

To add test claims and evidence:
```bash
php artisan db:seed
```

---

## File Structure (Key Locations)

```
vericrown/
├── resources/views/installer/index.blade.php  ← Installation form
├── app/Http/Controllers/InstallerController.php ← Installer logic
├── .env                 ← Created/updated by installer (contains secrets)
├── config/              ← Configuration files (updated by installer)
├── app/                 ← Application code
├── storage/             ← Logs, cache, temp files (writable)
└── bootstrap/cache/     ← Cache directory (writable)
```

---

## Configuration Files (Updated by Installer)

After installation, the installer updates these PHP config files:

**config/database.php**:
- Database host, port, database name
- Database username and password

**config/app.php**:
- Application name

No environment variables or .env files needed!

---

## Upgrading to a Different Server

If you're moving to a new cPanel server:

1. Upload files to new server
2. Remove `config/installer.php` from your new server
3. Visit `/install` again
4. Complete the installer with new database credentials
5. Done!

---

## Manual Setup (Without Installer)

If the installer doesn't work for some reason, edit the config files directly:

**config/database.php** - Find the `mysql` connection and update:
```php
'host' => 'localhost',          // Your database host
'port' => 3306,                 // Your database port
'database' => 'your_database',  // Your database name
'username' => 'your_user',      // Your database user
'password' => 'your_password',  // Your database password
```

**config/app.php** - Update:
```php
'name' => 'Your App Name',
```

Then run migrations:
```bash
php artisan migrate --force
```

Create admin user:
```bash
php artisan tinker
# \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')])
```

Create `config/installer.php` to mark installation complete:
```php
<?php
return ['installed' => true];
```

---

## Support & Documentation

**For general usage**: See [README.md](README.md)

**For architecture details**: See [IMPLEMENTATION.md](IMPLEMENTATION.md)

**For cPanel-specific help**: See [CPANEL_DEPLOYMENT.md](CPANEL_DEPLOYMENT.md)

**For all commands**: See [QUICK_REF.md](QUICK_REF.md)

---

## Next Steps After Installation

1. ✅ **Login** with your admin credentials
2. 📝 **Create a claim** to test the system
3. 📎 **Add evidence** with a URL
4. 👍 **Vote on evidence** to see verdict change
5. 🔐 **Secure your instance**:
   - Keep config files secure (read-only on production)
   - Use HTTPS/SSL
   - Keep Laravel updated
6. 📊 **Monitor** logs and usage

---

**VeriCrowd is ready to install! Visit `/install` to get started.**
