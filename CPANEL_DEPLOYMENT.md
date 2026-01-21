# VeriCrowd - cPanel Deployment Guide

VeriCrowd is now configured for cPanel/shared hosting with MariaDB and a browser-based installer.

## Pre-Deployment Checklist

- [ ] PHP 8.3+ installed (cPanel supports this)
- [ ] MariaDB/MySQL database access
- [ ] cPanel account with SSH access
- [ ] Composer installed on server
- [ ] Domain/subdomain configured

## Installation Steps (cPanel Hosting)

### 1. Upload Files to cPanel

```bash
# Via SSH or File Manager:
# Upload all project files to your public_html or a subdirectory
# Example: /home/username/public_html/vericrown/
```

### 2. SSH into Your Server

```bash
ssh username@yourdomain.com
cd public_html/vericrown  # or wherever you uploaded
```

### 3. Create .env File

```bash
# Copy the example file
cp .env.example .env
```

### 4. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Set Permissions (IMPORTANT for cPanel)

```bash
# Set proper permissions for Laravel
chmod -R 755 bootstrap/cache
chmod -R 755 storage
chmod -R 755 storage/framework
chmod -R 755 storage/logs

# If cPanel uses a specific user/group (like nobody or www-data):
chown -R nobody:nobody storage bootstrap/cache
# OR
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Access the Installation Wizard

Open your browser and navigate to:
```
https://yourdomain.com/install
```

Or if in a subdirectory:
```
https://yourdomain.com/vericrown/install
```

### 8. Complete the Installation Wizard

Open your browser and navigate to:
```
https://yourdomain.com/install
```

The installer will ask you to provide:
- **Application Settings**: Application name and URL
- **Database Configuration**: The database host, name, username, and password you created in cPanel
- **Admin Account**: Create your initial admin user account

Simply fill in the form with your cPanel database credentials and click "Complete Installation".

### 9. Login

After installation completes, you'll be redirected to the login page. Use the admin credentials you created.

---

## cPanel-Specific Configuration

### Setting up the Database in cPanel

Before running the installer, you must create a MariaDB database and user in cPanel:

1. **In cPanel Dashboard:**
   - Click "MySQL Databases" or "MariaDB"
   - **Create a new database** (e.g., `username_vericrown`)
   - Scroll down to "MySQL Users" section
   - **Create a new user** with a strong password (e.g., `username_dbuser`)
   - Under "Add User to Database", select the database and user
   - Check **ALL PRIVILEGES** for full permissions

2. **Database Credentials to Use in Installer:**
   - **Host**: Usually `localhost` (sometimes `127.0.0.1`)
   - **Database**: `username_vericrown` (the database you created)
   - **Username**: `username_dbuser` (the user you created)
   - **Password**: The password you set for the user

### Setting up Public Directory (Optional)

For production, you can point your domain to the `public` folder:

1. In cPanel, go to **Addon Domains** or **Parked Domains**
2. Point your domain to the `public` folder of your vericrown installation
3. Example: `/home/username/public_html/vericrown/public`

### Configuring PHP Settings

1. **In cPanel:**
   - Go to **EasyApache 4** or **PHP Configuration**
   - Make sure PHP 8.3+ is selected
   - Set necessary PHP extensions:
     - php-pdo
     - php-mysql
     - php-json
     - php-bcmath
     - php-curl
     - php-fileinfo

2. **Check `.htaccess` in `public/` folder:**
   - cPanel should handle this, but make sure rewrite rules are enabled

### Important: Disable DirectAdmin/cPanel Installer Cache

If you've run the installer and want to reinstall:

1. Delete the `.env` file or set `INSTALLATION_COMPLETE=false`
2. Clear Laravel cache: `php artisan config:cache`
3. Revisit `/install`

---

## After Installation

### Setting Up Queue Processing (Optional)

If you want to process background jobs (URL ingestion), you'll need to set up a cron job:

**In cPanel Cron Jobs:**
```
* * * * * cd /home/username/public_html/vericrown && php artisan schedule:run >> /dev/null 2>&1
```

Or use the built-in Laravel queue (runs synchronously by default on shared hosting).

### Setting Up SSL/TLS

1. **In cPanel:**
   - Go to **SSL/TLS**
   - Use AutoSSL (if available) or install a certificate

2. **Update .env:**
   ```
   APP_URL=https://yourdomain.com
   ```

### Backup & Maintenance

**Regular Backups:**
```bash
# Backup database
mysqldump -u username_dbuser -p username_vericrown > backup.sql

# Backup files
tar -czf vericrown_backup.tar.gz /home/username/public_html/vericrown
```

---

## Troubleshooting

### Installation Wizard Not Showing

If you see a different page, the installation might already be complete. To reset:

```bash
# Via SSH:
nano .env
# Find line: INSTALLATION_COMPLETE=true
# Change to: INSTALLATION_COMPLETE=false
# Save (Ctrl+X, Y, Enter)
```

### Database Connection Errors

1. **Check credentials in cPanel**
2. **Verify database user privileges** (should have ALL)
3. **Test connection:**
   ```bash
   mysql -h localhost -u username_dbuser -p username_vericrown
   # Enter password when prompted
   # If successful, you'll see: mysql>
   ```

### Permission Errors

Run these commands:
```bash
chmod -R 755 bootstrap/cache storage
chown -R nobody:nobody bootstrap/cache storage
# If that doesn't work, try:
chown -R $(whoami):$(whoami) bootstrap/cache storage
```

### White Screen / 500 Error

Check error logs:
```bash
tail -100 storage/logs/laravel.log
# Or in cPanel error logs:
tail -100 /home/username/public_html/error_log
```

### Installer Button Not Working

1. Clear Laravel cache:
   ```bash
   php artisan cache:clear
   ```
2. Check file permissions on `.env`
3. Verify database connection

---

## Environment Variables (Created by Installer)

The installer automatically sets these in `.env`:

```
APP_NAME=VeriCrowd
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_vericrown
DB_USERNAME=username_dbuser
DB_PASSWORD=your_password

QUEUE_CONNECTION=sync
CACHE_DRIVER=file
SESSION_DRIVER=cookie

INSTALLATION_COMPLETE=true
```

**DO NOT share your .env file** - it contains sensitive credentials.

---

## Production Recommendations

### Security

1. ✅ Set `APP_DEBUG=false` (installer does this automatically)
2. ✅ Use HTTPS/SSL (configure in cPanel)
3. ✅ Keep `.env` out of version control (already in .gitignore)
4. ✅ Change default admin password after installation
5. ✅ Keep Laravel updated: `composer update`

### Performance

1. Cache configuration:
   ```bash
   php artisan config:cache
   ```

2. Optimize autoloader:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

### Monitoring

1. Check error logs regularly:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Monitor cPanel error logs via cPanel Dashboard

---

## Reinstalling on Another cPanel Account

1. Upload files to new server
2. Copy `.env.example` to `.env` (remove old .env if exists)
3. Set `INSTALLATION_COMPLETE=false` in .env
4. Visit `/install` and complete the wizard

---

## Support

For Laravel-specific issues, refer to:
- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Laravel Forum](https://laracasts.com/discuss)

For cPanel-specific issues:
- Contact your hosting provider
- Check cPanel documentation

---

**VeriCrowd is now ready for cPanel deployment!**

Visit `/install` to complete setup via the browser-based installer.
