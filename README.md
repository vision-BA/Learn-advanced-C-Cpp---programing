# Student Portal - Web Application

A PHP web application for managing student information with MySQL database.

## Features

✅ Create database automatically (`studentPortal`)
✅ Create table automatically (`studentinfo`)
✅ Add new students with first name and last name
✅ View all registered students
✅ SQL injection protection (prepared statements)
✅ Responsive design
✅ Real-time feedback messages

## Requirements

- **PHP 7.0+** (with MySQLi extension)
- **MySQL/MariaDB** server running
- **Web server** (Apache, Nginx, etc.) or PHP built-in server

## Installation & Setup

### 1. Install XAMPP or MySQL

**On Windows:**
- Download and install [XAMPP](https://www.apachefriends.org/) (includes Apache, PHP, and MySQL)
- Start Apache and MySQL from XAMPP Control Panel

### 2. Place Files

- Copy `index.php` to your web root folder:
  - **XAMPP:** `C:\xampp\htdocs\`
  - Example: `C:\xampp\htdocs\student-portal\`

### 3. Database Configuration

Open `index.php` and update these lines if needed:

```php
$servername = "localhost";
$username = "root";           // MySQL username (default: root)
$password = "";               // MySQL password (leave empty for default)
$dbname = "studentPortal";
```

### 4. Run the Application

**Option A: Using XAMPP**
- Open browser and go to: `http://localhost/student-portal/`

**Option B: Using PHP Built-in Server**
```bash
cd d:\BOOKS\PROJECTS\REPOSITORIES\Learn advanced C programing
php -S localhost:8000
```
Then open: `http://localhost:8000/index.php`

## Database Structure

### Database
- **Name:** `studentPortal`

### Table: `studentinfo`

```sql
CREATE TABLE studentinfo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

- `id` - Unique identifier (auto-increment)
- `firstName` - Student's first name
- `lastName` - Student's last name
- `created_at` - Timestamp of record creation

## Usage

1. Enter student's **First Name** in the form
2. Enter student's **Last Name** in the form
3. Click **"Add Student"** button
4. View all registered students in the table below
5. Records are displayed in reverse chronological order

## Security Features

- ✅ **Prepared Statements** - Prevents SQL injection
- ✅ **Input Validation** - Checks for empty fields
- ✅ **HTML Escaping** - Prevents XSS attacks
- ✅ **Sanitized Output** - All data is properly escaped

## Troubleshooting

### Error: "Connection failed"
- Make sure MySQL server is running
- Check username and password in the code

### Error: "Error creating database"
- Ensure your MySQL user has database creation privileges

### Table not showing data
- Clear browser cache (Ctrl + F5)
- Check browser console for errors

## File Structure

```
student-portal/
├── index.php       (Main application - form + database handling)
└── README.md       (This file)
```

## MySQL Credentials

Default MySQL credentials (XAMPP):
- **Host:** localhost
- **User:** root
- **Password:** (empty)

If you set a password, update line in `index.php`:
```php
$password = "your_password_here";
```

---

**Created:** PHP Web Application for Student Management
