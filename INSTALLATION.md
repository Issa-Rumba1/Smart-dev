# SMART DEV – Installation Guide

## Requirements
- XAMPP (Apache + MySQL 5.7+ or MariaDB 10.3+)
- PHP 8.0 or newer
- A modern web browser

---

## Step 1 — Install and Start XAMPP

1. Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Install it (default path: `C:\xampp` on Windows)
3. Open the **XAMPP Control Panel**
4. Start **Apache** and **MySQL**

---

## Step 2 — Copy Project Files

Copy the entire `smart-dev` folder into:
```
C:\xampp\htdocs\smart-dev\
```

Your folder structure should look like:
```
C:\xampp\htdocs\smart-dev\
├── index.php
├── about.php
├── admin\
├── config\
├── includes\
├── assets\
├── database\
└── ...
```

---

## Step 3 — Create the Database

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **"New"** in the left panel
3. Enter database name: `smart_dev_db`
4. Click **Create**
5. Select `smart_dev_db` in the left panel
6. Click the **Import** tab at the top
7. Click **"Choose File"** and select:
   ```
   C:\xampp\htdocs\smart-dev\database\smart_dev_db.sql
   ```
8. Click **Go** to import

---

## Step 4 — Configure Database Connection

Open `config/database.php` and verify these settings (default XAMPP settings work out of the box):

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_dev_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // Default XAMPP has no password
```

---

## Step 5 — Set Folder Permissions

Ensure these folders are writable by Apache:
- `uploads/projects/`
- `uploads/profile/`
- `uploads/cv/`
- `logs/`

On Windows with XAMPP, these are typically writable by default.

---

## Step 6 — Access the Website

Open your browser and navigate to:
```
http://localhost/smart-dev/
```

---

## Step 7 — Access the Admin Panel

1. Go to: `http://localhost/smart-dev/admin/login.php`
2. Login with the default credentials:
   - **Username:** `admin`
   - **Password:** `password`

⚠️ **IMPORTANT: Change the default password IMMEDIATELY after first login!**
   - Go to: Admin → Profile → Change Password

---

## Step 8 — Customise Your Profile

After logging in to the admin panel:

1. **Admin → Profile** — Update your name, bio, photo, social links, and CV
2. **Admin → Education** — Edit or add your qualifications
3. **Admin → Skills** — Add, edit or remove skills
4. **Admin → Services** — Manage your service offerings
5. **Admin → Projects** — Add your portfolio projects
6. **Admin → Testimonials** — Add client reviews
7. **Admin → Settings** — Configure site settings

---

## Troubleshooting

### "Connection refused" or database error
- Ensure MySQL is running in XAMPP Control Panel
- Check `config/database.php` credentials

### White blank page
- Enable PHP error display in `includes/init.php`:
  ```php
  ini_set('display_errors', '1');
  ```

### File uploads not working
- Check that `uploads/` subdirectories exist and are writable
- Verify PHP `upload_max_filesize` and `post_max_size` in `php.ini` (XAMPP default: 2MB; increase to 10MB)

### Admin login not working
- Clear your browser cookies and try again
- Check if the `administrators` table was imported correctly in phpMyAdmin

---

## Production Deployment Checklist

Before going live:

- [ ] Set `display_errors = Off` in `includes/init.php`
- [ ] Change `DB_USER` and `DB_PASS` to secure values
- [ ] Create a dedicated MySQL user with minimum permissions (not root)
- [ ] Enable HTTPS on your server
- [ ] Update `robots.txt` and `sitemap.xml` with your real domain
- [ ] Change the admin password from the default
- [ ] Set strong Content Security Policy headers
- [ ] Delete the `INSTALLATION.md` and `database/smart_dev_db.sql` files from public access
- [ ] Back up the database regularly

---

## Security Notes

The following security measures are already implemented:

✅ Password hashing with `password_hash()` (bcrypt cost 12)
✅ CSRF tokens on all forms
✅ PDO prepared statements (SQL injection protection)
✅ Output escaping with `htmlspecialchars()` (XSS protection)
✅ Server-side form validation
✅ File upload validation (MIME type + extension + size)
✅ PHP execution blocked in uploads directory
✅ Login rate limiting (5 attempts per 15 minutes)
✅ Session fixation protection
✅ Security HTTP headers
✅ Directory listing disabled
✅ Admin area restricted to authenticated users
✅ Activity logging for admin operations

This is not a claim of complete security — production deployments require HTTPS,
regular updates, security audits, and proper server hardening.
