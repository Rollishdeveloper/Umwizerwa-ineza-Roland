# 🗄️ Switching from SQLite to MySQL — Migration Guide

> **E-LMS** uses SQLite by default for development. This guide explains how to switch to MySQL for production — **without losing any data**.

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Step 1: Create a MySQL Database](#step-1-create-a-mysql-database)
3. [Step 2: Update Environment Variables](#step-2-update-environment-variables)
4. [Step 3: Files That May Need Review](#step-3-files-that-may-need-review)
5. [Step 4: Export SQLite Data](#step-4-export-sqlite-data)
6. [Step 5: Run Migrations on MySQL](#step-5-run-migrations-on-mysql)
7. [Step 6: Import Data into MySQL](#step-6-import-data-into-mysql)
8. [Step 7: Clear Cache & Test](#step-7-clear-cache--test)
9. [Troubleshooting Guide](#troubleshooting-guide)
10. [Production Deployment Checklist](#production-deployment-checklist)

---

## Prerequisites

Before starting, ensure you have:

- ✅ MySQL 8.0+ installed and running
- ✅ PHP `pdo_mysql` extension enabled (`extension=pdo_mysql` in `php.ini`)
- ✅ Access to create databases (usually via `root` or a privileged user)
- ✅ Your application running on SQLite with all data intact

### Verify PHP MySQL Extension

```bash
php -m | grep -i mysql
```

Expected output: `pdo_mysql` and `mysqli` (if installed)

---

## Step 1: Create a MySQL Database

Log into MySQL and create a database:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE elearning_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER 'elearning_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON elearning_db.* TO 'elearning_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

> ⚠️ **Security:** Use a strong, unique password. Never commit credentials to version control.

---

## Step 2: Update Environment Variables

Open `.env` in the project root and replace the SQLite configuration:

### Before (SQLite — Development)

```env
DB_CONNECTION=sqlite
DB_DATABASE=
```

### After (MySQL — Production)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elearning_db
DB_USERNAME=elearning_user
DB_PASSWORD=your_strong_password
```

### Optional MySQL Optimizations

Add these for better performance:

```env
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

---

## Step 3: Files That May Need Review

These configuration files reference database connections and may need attention:

| File | What to Check |
|---|---|
| `config/database.php` | `mysql` connection settings. The default config already supports MySQL out-of-the-box. |
| `config/session.php` | `connection` option — set `SESSION_CONNECTION=mysql` in `.env` to use MySQL for sessions. |
| `config/cache.php` | `connection` option — set `DB_CACHE_CONNECTION=mysql` in `.env` to use MySQL for cache. |
| `config/queue.php` | `database` and `batching.database` — set `DB_QUEUE_CONNECTION=mysql` in `.env` to use MySQL for queues. |
| `.env` | All `DB_*` variables must point to MySQL. |

### Additional `.env` variables for session/cache/queue

If you want MySQL to handle sessions, cache, and queues as well:

```env
SESSION_DRIVER=database
SESSION_CONNECTION=mysql

CACHE_STORE=database
DB_CACHE_CONNECTION=mysql

QUEUE_CONNECTION=database
DB_QUEUE_CONNECTION=mysql
```

> ⚠️ These must match the `table` names defined in your respective config files. By default: `sessions`, `cache`, `jobs`, `job_batches`, `failed_jobs`.

---

## Step 4: Export SQLite Data

### Option A: Using the Admin Web Interface

1. Log in as **Admin**
2. Navigate to **Database Tools** in the sidebar → **Export Database**
3. Choose your export format:
   - **SQL Export** — Complete INSERT statements (best for migration)
   - **CSV Export** — One file per table (good for selective imports)
   - **JSON Export** — All data in one JSON file (good for programmatic use)
   - **Full Backup** — Everything bundled in a ZIP

### Option B: Using the Admin Web Interface Only

> The export feature is available exclusively through the admin web interface.
> Log in as an admin, navigate to **Database Tools** → **Export Database**, and choose your format.

---

## Step 5: Run Migrations on MySQL

### Via the Web Interface

1. Go to **Database Tools** → **Import to MySQL**
2. Click **Preflight Check** to verify the MySQL connection
3. Click **Run Migrations on MySQL**

### Via Command Line

```bash
php artisan migrate --database=mysql --force
```

> The `--force` flag is required in production environments.

---

## Step 6: Import Data into MySQL

### Method 1: SQL Import (Recommended)

If you exported as SQL:

```bash
mysql -u elearning_user -p elearning_db < storage/exports/sqlite_export.sql
```

Or through phpMyAdmin / MySQL Workbench:
1. Select the `elearning_db` database
2. Click **Import**
3. Choose the `sqlite_export.sql` file
4. Click **Go**

### Method 2: CSV Import

For each CSV file, use:

```bash
# Example for users table
mysql -u elearning_user -p elearning_db \
  -e "LOAD DATA LOCAL INFILE 'storage/exports/csv/users.csv'
      INTO TABLE users
      FIELDS TERMINATED BY ',' 
      ENCLOSED BY '\"'
      LINES TERMINATED BY '\n'
      IGNORE 1 ROWS;"
```

Or use phpMyAdmin's CSV import feature.

### Method 3: JSON Import

Use a custom script to parse `database_backup.json` and insert into MySQL. You can write a one-off PHP script or use phpMyAdmin's import feature.

---

## Step 7: Clear Cache & Test

After migration, clear all caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
```

Then test your application:

```bash
php artisan serve
```

Visit `http://localhost:8000` and verify:
- ✅ Login works with existing accounts
- ✅ Courses display with content
- ✅ Enrollments and progress are intact
- ✅ Certificates are accessible
- ✅ Quiz results are preserved

---

## Troubleshooting Guide

### Foreign Key Errors

**Problem:** `Cannot add or update a child row: a foreign key constraint fails`

**Solution:**
1. Temporarily disable foreign key checks during import:
   ```sql
   SET FOREIGN_KEY_CHECKS = 0;
   -- Import your data here
   SET FOREIGN_KEY_CHECKS = 1;
   ```
2. Or import tables in dependency order (parent tables first):
   ```
   users → instructors → courses → modules → lessons → enrollments
   ```

### Connection Errors

**Problem:** `SQLSTATE[HY000] [2002] Connection refused`

**Solutions:**
- Ensure MySQL is running: `sudo systemctl status mysql`
- Check the host and port in `.env` (default: `127.0.0.1:3306`)
- Verify MySQL is listening on the correct interface:
  ```sql
  SHOW VARIABLES LIKE 'bind_address';
  ```
- On some systems, use `DB_HOST=localhost` instead of `127.0.0.1`

**Problem:** `SQLSTATE[HY000] [1045] Access denied for user`

**Solutions:**
- Verify username and password in `.env`
- Ensure the user has access from the correct host:
  ```sql
  CREATE USER 'user'@'localhost' IDENTIFIED BY 'password';
  GRANT ALL ON elearning_db.* TO 'user'@'localhost';
  ```

### Charset Issues

**Problem:** Characters display as `????` or garbled text

**Solutions:**
- Ensure your MySQL database uses `utf8mb4`:
  ```sql
  ALTER DATABASE elearning_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```
- Set charset in `.env`:
  ```env
  DB_CHARSET=utf8mb4
  DB_COLLATION=utf8mb4_unicode_ci
  ```
- For existing tables:
  ```sql
  ALTER TABLE table_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```

### Migration Failures

**Problem:** `Base table or view already exists`

**Solutions:**
- Rollback and retry:
  ```bash
  php artisan migrate:rollback --database=mysql
  php artisan migrate --database=mysql
  ```
- Or reset entirely (⚠️ drops all tables):
  ```bash
  php artisan migrate:fresh --database=mysql --seed
  ```

**Problem:** `Syntax error or access violation` during migration

**Solutions:**
- Check if your MySQL version supports the features used in migrations (MySQL 8.0+ recommended)
- Ensure no database-specific SQL is used in migration files
- For JSON columns, MySQL 5.7+ is required

### Duplicate Key Conflicts

**Problem:** `Duplicate entry '...' for key '...'`

**Solution:**
- Clear the target table before re-importing:
  ```sql
  TRUNCATE TABLE table_name;
  ```
- Or use `INSERT IGNORE` to skip duplicates:
  ```sql
  INSERT IGNORE INTO table_name VALUES (...);
  ```

### Missing pdo_mysql Extension

**Problem:** `Class "PDO" not found` or `could not find driver`

**Solutions:**
- Install the PHP MySQL extension:
  ```bash
  # Ubuntu/Debian
  sudo apt install php-mysql

  # CentOS/RHEL
  sudo yum install php-mysqlnd

  # Windows: Uncomment in php.ini
  extension=pdo_mysql
  ```
- Restart your web server:
  ```bash
  sudo systemctl restart apache2   # or nginx / php-fpm
  ```

---

## Production Deployment Checklist

Use this checklist before deploying to production:

### 1. Database
- [ ] ✅ MySQL 8.0+ installed and running
- [ ] ✅ Database `elearning_db` created with `utf8mb4` charset
- [ ] ✅ Database user created with strong password
- [ ] ✅ MySQL user has `SELECT`, `INSERT`, `UPDATE`, `DELETE`, `CREATE`, `ALTER`, `INDEX`, `DROP` privileges
- [ ] ✅ Remote access configured if needed (bind address, firewall)
- [ ] ✅ MySQL port (3306) accessible from application server

### 2. Environment Configuration
- [ ] ✅ `.env` updated with MySQL connection details
- [ ] ✅ `DB_CONNECTION=mysql` set
- [ ] ✅ `SESSION_CONNECTION=mysql` set (if using DB sessions)
- [ ] ✅ `DB_CACHE_CONNECTION=mysql` set (if using DB cache)
- [ ] ✅ `DB_QUEUE_CONNECTION=mysql` set (if using DB queues)
- [ ] ✅ `APP_ENV=production` set
- [ ] ✅ `APP_DEBUG=false` set

### 3. Migration
- [ ] ✅ Full SQLite backup created (via Database Tools → Full Backup)
- [ ] ✅ Migrations executed on MySQL (`php artisan migrate --database=mysql --force`)
- [ ] ✅ SQLite data imported into MySQL
- [ ] ✅ All tables present and populated

### 4. Post-Migration
- [ ] ✅ Config cache cleared (`php artisan optimize`)
- [ ] ✅ Application tested end-to-end
- [ ] ✅ Login, registration working
- [ ] ✅ Course CRUD working
- [ ] ✅ Enrollments and progress showing correctly
- [ ] ✅ Quizzes and results functional
- [ ] ✅ Certificates generating correctly
- [ ] ✅ Reports displaying data
- [ ] ✅ Notifications working

### 5. Performance & Security
- [ ] ✅ MySQL `query_cache` configured (if using MySQL < 8.0)
- [ ] ✅ `innodb_buffer_pool_size` set appropriately (recommended: 70% of available RAM)
- [ ] ✅ Database connection pooling configured (optional: pgbouncer-compatible for MySQL)
- [ ] ✅ Regular backup schedule established
- [ ] ✅ Read-only replicas configured (optional, for high-traffic sites)

### 6. Rollback Plan

If anything goes wrong, restore from backup:

```bash
# 1. Switch .env back to SQLite
DB_CONNECTION=sqlite

# 2. Clear cache
php artisan optimize:clear

# 3. Restore SQLite database from backup ZIP
# Extract database.sqlite from the backup and copy to database/

# 4. Test
php artisan serve
```

---

## Quick Reference: `.env` Changes

```diff
# ❌ SQLite (Development)
- DB_CONNECTION=sqlite
- DB_DATABASE=

# ✅ MySQL (Production)
+ DB_CONNECTION=mysql
+ DB_HOST=127.0.0.1
+ DB_PORT=3306
+ DB_DATABASE=elearning_db
+ DB_USERNAME=elearning_user
+ DB_PASSWORD=your_strong_password
+ DB_CHARSET=utf8mb4
+ DB_COLLATION=utf8mb4_unicode_ci
```

---

## Need Help?

- Check the [Laravel Database Documentation](https://laravel.com/docs/database)
- Verify your MySQL version: `mysql --version`
- Check Laravel logs: `storage/logs/laravel.log`
- Use the **Database Tools** admin panel for guided migration
