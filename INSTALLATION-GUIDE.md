# SIMS Installation & Setup Guide

## Quick Start Guide

### Prerequisites
- **OS:** Windows, Linux, or macOS
- **Web Server:** Apache or Nginx
- **PHP:** Version 7.0 or higher with MySQLi extension
- **Database:** MySQL 5.7+ or MariaDB 10.2+
- **Browser:** Modern web browser (Chrome, Firefox, Safari, Edge)

## Step-by-Step Installation

### Step 1: Installation on Windows (XAMPP)

#### 1.1 Download and Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Run the installer and follow the installation wizard
3. Install to default location: `C:\xampp`

#### 1.2 Start XAMPP Services
1. Open XAMPP Control Panel
2. Click **Start** next to Apache
3. Click **Start** next to MySQL

### Step 2: Create the Database

#### 2.1 Using phpMyAdmin
1. Click **Admin** button next to MySQL in XAMPP Control Panel
2. phpMyAdmin will open in your browser
3. Click on **SQL** tab
4. Copy all content from `setup/database.sql`
5. Paste it into the SQL editor
6. Click **Go** to execute

#### 2.2 Using Command Line (Alternative)
```bash
cd "C:\xampp\mysql\bin"
mysql -u root -p < "C:\path\to\setup\database.sql"
```
(Press Enter when prompted for password if none is set)

### Step 3: Copy Project Files

1. Extract/Copy all SIMS files to:
   ```
   C:\xampp\htdocs\sims
   ```

2. Verify folder structure:
   ```
   C:\xampp\htdocs\sims\
   ├── admin/
   ├── student/
   ├── teacher/
   ├── includes/
   ├── css/
   ├── setup/
   ├── config.php
   ├── database.php
   ├── auth.php
   ├── functions.php
   ├── login.php
   └── ...other files
   ```

### Step 4: Configure PHP Settings

Edit `php.ini` (usually in `C:\xampp\php\`):

```ini
; Increase upload size
upload_max_filesize = 20M
post_max_size = 20M

; Ensure MySQLi is enabled
extension=php_mysqli.dll
```

### Step 5: Verify Configuration

1. Open browser and go to:
   ```
   http://localhost/xampp
   ```

2. Click on **phpInfo** in the left menu

3. Look for:
   - PHP version ≥ 7.0
   - MySQLi extension loaded (search for "mysqli")

### Step 6: Test Database Connection

Create a test file `C:\xampp\htdocs\test.php`:

```php
<?php
$conn = new mysqli('localhost', 'root', '', 'sims_db');
if ($conn->connect_error) {
    echo "Connection Failed: " . $conn->connect_error;
} else {
    echo "Database Connection Successful!";
}
$conn->close();
?>
```

Access it at `http://localhost/test.php`

## First Time Access

### 1. Go to Login Page
```
http://localhost/sims/login.php
```

### 2. Default Admin Credentials
- **Username:** `admin`
- **Password:** `admin123`

### 3. Change Default Password
1. Login with default credentials
2. Go to **Profile** → **Change Password**
3. Enter new secure password
4. Save changes

## Troubleshooting

### Issue: "Connection refused" Error

**Solution:**
- Ensure MySQL is started in XAMPP Control Panel
- Check if port 3306 is not blocked by firewall
- Run XAMPP as Administrator

### Issue: "Access denied for user 'root'"

**Solution:**
- Check MySQL password in `config.php`
- Reset MySQL password:
  ```bash
  cd C:\xampp\mysql\bin
  mysql -u root
  ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
  ```

### Issue: "Table doesn't exist" Error

**Solution:**
- Run database.sql again:
  ```bash
  mysql -u root -pYourPassword sims_db < setup/database.sql
  ```

### Issue: File Uploads Not Working

**Solution:**
- Create `uploads` folder: `C:\xampp\htdocs\sims\uploads`
- Set folder permissions: Right-click → Properties → Security → Full Control

### Issue: White Blank Page

**Solution:**
1. Enable error reporting in `config.php`
2. Check error logs in `logs/` folder
3. Verify PHP syntax:
   ```bash
   php -l filename.php
   ```

## Linux Installation (Ubuntu)

### 1. Install LAMP Stack
```bash
sudo apt update
sudo apt install apache2 php php-mysql mysql-server
sudo systemctl start apache2
sudo systemctl start mysql
```

### 2. Create Database
```bash
mysql -u root -p < setup/database.sql
```

### 3. Copy Files
```bash
sudo cp -r . /var/www/html/sims
sudo chown -R www-data:www-data /var/www/html/sims
```

### 4. Enable mod_rewrite
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 5. Access Application
```
http://localhost/sims/login.php
```

## macOS Installation

### 1. Install MAMP
Download from [https://www.mamp.info/](https://www.mamp.info/)

### 2. Start MAMP
- Open MAMP preferences
- Start Apache and MySQL

### 3. Copy Files
```bash
cp -r sims /Applications/MAMP/htdocs/
```

### 4. Create Database
```bash
mysql -u root -proot < setup/database.sql
```

### 5. Access Application
```
http://localhost:8888/sims/login.php
```

## Production Deployment Checklist

### Security
- [ ] Change all default passwords
- [ ] Set `ENABLE_HTTPS = true` in config.php
- [ ] Install SSL certificate
- [ ] Disable directory listing in .htaccess
- [ ] Remove setup folder
- [ ] Set proper file permissions (644 for files, 755 for folders)
- [ ] Move config.php outside web root (optional)
- [ ] Disable PHP error display in production

### Performance
- [ ] Enable database query caching
- [ ] Optimize MySQL configuration
- [ ] Enable gzip compression in Apache
- [ ] Set up proper logging
- [ ] Configure backup strategy

### Monitoring
- [ ] Set up error log monitoring
- [ ] Enable activity logging
- [ ] Regular database backups
- [ ] Monitor disk space
- [ ] Set up alerts for critical errors

## Backup & Restore

### Backup Database
```bash
mysqldump -u root -pPassword sims_db > sims_backup.sql
```

### Restore Database
```bash
mysql -u root -pPassword sims_db < sims_backup.sql
```

### Backup Files
```bash
zip -r sims_backup.zip sims/
tar -czf sims_backup.tar.gz sims/
```

## Contact & Support

For setup assistance or issues:
1. Review the SIMS-README.md file
2. Check error logs
3. Test database connectivity
4. Verify all file permissions
5. Ensure all dependencies are installed

---

**Last Updated:** March 2024
**Version:** 1.0.0
