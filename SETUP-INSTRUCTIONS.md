# How to Create Database and Configure SIMS

## Task 1: Create Database Using phpMyAdmin

### Step 1: Start XAMPP Services
1. Open **XAMPP Control Panel** (should be on your desktop or in Program Files)
2. Click the **Start** button next to **Apache** (if not already running)
3. Click the **Start** button next to **MySQL** (if not already running)

   You should see green indicators showing both services are running.

### Step 2: Access phpMyAdmin
**Method A: From XAMPP Control Panel**
1. Click the **Admin** button next to MySQL in XAMPP Control Panel
2. phpMyAdmin will open automatically in your default browser

**Method B: Manual Access**
1. Open your browser (Chrome, Firefox, Safari, etc.)
2. Go to this URL: `http://localhost/phpmyadmin`
3. You should see the phpMyAdmin login/interface

### Step 3: Copy the Database SQL

1. Navigate to your SIMS project folder:
   ```
   D:\BOOKS\PROJECTS\REPOSITORIES\Learn advanced C programing\setup\
   ```

2. Open `database.sql` file with Notepad or any text editor

3. **Select All** content (Ctrl+A)

4. **Copy** the content (Ctrl+C)

### Step 4: Execute SQL in phpMyAdmin

1. In phpMyAdmin, you should see the left panel with databases
2. Click on the **SQL** tab at the top

   ![Where to find SQL tab in phpMyAdmin]
   ```
   Left panel (shows databases)  |  Main panel (SQL tab at top)
   ```

3. You'll see a large text area for entering SQL commands

4. **Paste** your database SQL (Ctrl+V) into the text area

5. Click the **Go** button at the bottom right to execute

6. You should see a success message:
   ```
   ✓ Query executed successfully
   Database 'sims_db' created
   Table 'users' created
   ...etc
   ```

### Verification
1. In the left panel, you should now see **sims_db** database listed
2. Click on it to expand and see all the tables:
   - users
   - students
   - teachers
   - courses
   - enrollments
   - grades
   - attendance
   - academic_sessions
   - notifications
   - activity_log

---

## Task 2: Update config.php with Database Credentials

### Step 1: Locate config.php File

1. Open your SIMS project folder:
   ```
   D:\BOOKS\PROJECTS\REPOSITORIES\Learn advanced C programing\
   ```

2. Find and open **config.php** with Notepad, VS Code, or PHPStorm

### Step 2: Find Database Configuration Section

Look for this section in config.php (should be at the top):

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'Bright@2025');
define('DB_NAME', 'sims_db');
define('DB_PORT', 3306);
```

### Step 3: Update Credentials

#### A. Update DB_HOST (Usually No Change Needed)
```php
define('DB_HOST', 'localhost');  // Keep this as is
```

#### B. Update DB_USER
```php
// Change from:
define('DB_USER', 'root');

// To (usually stays 'root' for XAMPP):
define('DB_USER', 'root');
```

#### C. Update DB_PASSWORD ⭐ IMPORTANT
```php
// Change from:
define('DB_PASSWORD', 'Bright@2025');

// To your actual MySQL password:
define('DB_PASSWORD', 'YOUR_MYSQL_PASSWORD');
```

**How to find your MySQL password:**
- If you just installed XAMPP, the default is usually BLANK (empty)
- So change it to:
  ```php
  define('DB_PASSWORD', '');  // Leave empty for XAMPP default
  ```

#### D. Update DB_NAME
```php
// Keep this as:
define('DB_NAME', 'sims_db');  // This matches the database we created
```

#### E. Update DB_PORT
```php
define('DB_PORT', 3306);  // This is the default, keep it
```

### Step 4: Save the File

1. Press **Ctrl+S** to save
2. You should see the file is saved (no asterisk in the title)

### Example Configuration (XAMPP Default)

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');              // Empty for XAMPP default
define('DB_NAME', 'sims_db');
define('DB_PORT', 3306);
```

### Example Configuration (If You Set a Password)

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'mySecurePassword123');  // Your MySQL password
define('DB_NAME', 'sims_db');
define('DB_PORT', 3306);
```

---

## Step 5: Test the Connection

### Method 1: Using SIMS Login Page

1. Start Apache and MySQL in XAMPP
2. Go to browser and type:
   ```
   http://localhost/sims/login.php
   ```

3. If you see the login page without errors, your connection works! ✓

4. Login with:
   - **Username:** `admin`
   - **Password:** `admin123`

### Method 2: Create a Test File (Advanced)

Create a file called `test.php` in your SIMS folder:

```php
<?php
// Test database connection
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'sims_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    echo "❌ Connection Failed: " . $conn->connect_error;
    die();
}

echo "✓ Database Connection Successful!";
echo "<br>";
echo "✓ Host: " . DB_HOST;
echo "<br>";
echo "✓ Database: " . DB_NAME;

$conn->close();
?>
```

Then visit: `http://localhost/sims/test.php`

---

## Common Issues & Solutions

### Issue 1: "Access Denied for user 'root'@'localhost'"

**Problem:** Wrong password in config.php

**Solution:**
1. In XAMPP, MySQL usually has NO password
2. Change config.php to:
   ```php
   define('DB_PASSWORD', '');  // Leave EMPTY
   ```

### Issue 2: "Unknown Database 'sims_db'"

**Problem:** database.sql wasn't executed properly

**Solution:**
1. Go back to phpMyAdmin
2. Re-paste all of database.sql
3. Click Go to execute
4. Check the left panel to confirm sims_db exists

### Issue 3: "Connection refused"

**Problem:** MySQL isn't running

**Solution:**
1. Open XAMPP Control Panel
2. Make sure MySQL shows a green indicator
3. If red, click Start button next to MySQL

### Issue 4: Can't Find phpMyAdmin

**Solution:**
1. Make sure Apache is running in XAMPP
2. Go to: `http://localhost`
3. You should see XAMPP page with links to phpMyAdmin

### Issue 5: Files Won't Copy to htdocs

**Problem:** Permission denied

**Solution:**
1. Run XAMPP Control Panel as Administrator
2. Or manually move files using File Manager (drag & drop)
3. Ensure files are in: `C:\xampp\htdocs\sims\`

---

## Quick Checklist

Before logging in to SIMS, verify these steps:

- [ ] XAMPP Apache is running (green indicator)
- [ ] XAMPP MySQL is running (green indicator)
- [ ] phpMyAdmin accessible at `http://localhost/phpmyadmin`
- [ ] database.sql executed successfully in phpMyAdmin
- [ ] sims_db database visible in phpMyAdmin left panel
- [ ] config.php has correct DB_PASSWORD (usually empty for XAMPP)
- [ ] SIMS files in `C:\xampp\htdocs\sims\`
- [ ] Can access `http://localhost/sims/login.php`

---

## Next Steps

Once you've completed these two tasks:

1. ✓ Access `http://localhost/sims/login.php`
2. Login with:
   - Username: `admin`
   - Password: `admin123`
3. Go to Admin Panel
4. **Change the admin password immediately** (for security)

---

**Need Help?**
- Check the error message carefully
- Verify MySQL is running
- Check config.php was saved properly
- Make sure all SIMS files were copied
