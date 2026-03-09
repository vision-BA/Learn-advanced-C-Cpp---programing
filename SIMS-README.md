# Student Information Management System (SIMS)

## Overview

A complete Student Information Management System built with **PHP, MySQL, and HTML/CSS** without using any PHP framework. This system provides comprehensive functionality for managing students, teachers, courses, grades, attendance, and more.

## Features

✅ **User Authentication & Authorization**
- Role-based access control (Admin, Teacher, Student)
- Secure password hashing with bcrypt
- Session management with automatic timeout
- CSRF protection

✅ **Student Management**
- Complete student profiles with personal information
- Student registration and enrollment
- GPA calculation
- Attendance tracking

✅ **Teacher/Staff Management**
- Teacher profiles and qualifications
- Course assignment
- Grade management
- Attendance recording

✅ **Course Management**
- Create and manage courses
- Assign teachers to courses
- Course enrollment
- Class level organization

✅ **Enrollment Management**
- Enroll students in courses
- Track enrollment status
- Course prerequisites

✅ **Grades & Marks**
- Record assignment, midterm, and final marks
- Automatic grade calculation
- GPA computation
- Grade reports

✅ **Attendance Management**
- Mark attendance for each class
- Attendance percentage calculation
- Attendance reports

✅ **Admin Dashboard**
- System statistics
- Recent activity logs
- User management
- Comprehensive reports

✅ **Reports & Analytics**
- Student performance reports
- Class-wise reports
- Attendance reports
- Grade distribution

## Technology Stack

- **Backend:** PHP 7.0+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3
- **Security:** bcrypt password hashing, prepared statements, CSRF tokens

## System Requirements

- PHP 7.0 or higher with MySQLi extension
- MySQL 5.7 or MariaDB 10.2+
- Apache, Nginx, or any web server supporting PHP
- Minimum 100MB disk space

## Installation & Setup

### Step 1: Download XAMPP (or equivalent)

Download and install [XAMPP](https://www.apachefriends.org/) for your operating system. This includes Apache, PHP, and MySQL.

### Step 2: Create Database

1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL**
3. Click **Admin** button next to MySQL to open phpMyAdmin
4. Go to the **SQL** tab
5. Copy and paste the content from `setup/database.sql`
6. Click **Go** to execute

Alternatively, use command line:
```bash
mysql -u root -p < setup/database.sql
```

### Step 3: Copy Files to Web Root

Copy all project files to your web root directory:
- **XAMPP:** `C:\xampp\htdocs\sims`
- **Other servers:** Your configured web root

### Step 4: Configure Database Connection

Edit `config.php` and verify database settings:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'Bright@2025');  // Your MySQL password
define('DB_NAME', 'sims_db');
```

### Step 5: Access the Application

Open your browser and navigate to:
```
http://localhost/sims/login.php
```

## Default Login Credentials

| Role   | Username | Password  |
|--------|----------|-----------|
| Admin  | admin    | admin123  |

**⚠️ SECURITY NOTE:** Change the default admin password immediately after first login!

## File Structure

```
sims/
├── admin/                    # Admin management pages
│   ├── dashboard.php        # Admin dashboard
│   ├── students.php         # Student management
│   ├── add-student.php      # Add new student
│   ├── teachers.php         # Teacher management
│   ├── courses.php          # Course management
│   ├── add-course.php       # Add new course
│   ├── enrollments.php      # Enrollment management
│   ├── add-enrollment.php   # Add new enrollment
│   ├── users.php            # User management
│   └── reports.php          # Generate reports
├── teacher/                 # Teacher pages
│   ├── dashboard.php        # Teacher dashboard
│   ├── courses.php          # Assigned courses
│   ├── grades.php           # Manage grades
│   └── attendance.php       # Mark attendance
├── student/                 # Student pages
│   ├── dashboard.php        # Student dashboard
│   ├── courses.php          # Enrolled courses
│   └── grades.php           # View grades
├── includes/                # Reusable components
│   ├── navbar.php          # Navigation bar
│   └── footer.php          # Footer
├── css/                     # Stylesheets
│   └── style.css           # Main stylesheet
├── setup/                   # Setup files
│   └── database.sql        # Database schema
├── config.php              # Configuration file
├── database.php            # Database connection class
├── auth.php                # Authentication class
├── functions.php           # Utility functions
├── login.php               # Login page
├── logout.php              # Logout
└── README.md               # This file
```

## User Roles & Permissions

### Admin
- Full system access
- Manage all users (students, teachers, admin)
- Create and manage courses
- Manage enrollments
- View all reports
- System configuration

### Teacher
- View assigned courses
- Record and manage grades
- Mark attendance
- View student performance
- Update profile

### Student
- View enrolled courses
- View grades and academic records
- Check attendance
- View GPA
- Update profile

## Database Schema

### Key Tables

1. **users** - User authentication and basic info
2. **students** - Student-specific information
3. **teachers** - Teacher-specific information
4. **courses** - Course catalog
5. **enrollments** - Student course enrollments
6. **grades** - Student grades and marks
7. **attendance** - Class attendance records
8. **academic_sessions** - Academic terms/sessions
9. **notifications** - User notifications
10. **activity_log** - System audit trail

## Common Operations

### Add a New Student
1. Login as Admin
2. Go to **Management → Students**
3. Click **+ Add New Student**
4. Fill in all required fields
5. Click **Add Student**

### Add a New Course
1. Login as Admin
2. Go to **Management → Courses**
3. Click **+ Add New Course**
4. Fill in course details
5. Assign an instructor
6. Click **Add Course**

### Enroll Student in Course
1. Login as Admin
2. Go to **Management → Enrollments**
3. Click **+ Add New Enrollment**
4. Select student and course
5. Set enrollment date
6. Click **Add Enrollment**

### Record Grades
1. Login as Teacher
2. Go to **Grades** for your course
3. Enter assignment, midterm, and final marks
4. System automatically calculates total and grade
5. Save grades

### Mark Attendance
1. Login as Teacher
2. Go to **Attendance** for your course
3. Check present/absent/late for each student
4. Save attendance record

## Security Features

✅ **Password Security**
- Bcrypt hashing algorithm
- Minimum password requirements
- Password change on first login

✅ **SQL Injection Prevention**
- Prepared statements for all database queries
- Input validation and sanitization

✅ **CSRF Protection**
- CSRF token generation and verification
- Unique tokens per session

✅ **Session Management**
- Automatic session timeout (1 hour)
- Secure session handling
- Activity logging

✅ **Access Control**
- Role-based permission checking
- Protected admin pages
- User-specific data isolation

## Troubleshooting

### Database Connection Error
```
Solution: Check DB_HOST, DB_USER, DB_PASSWORD in config.php
Make sure MySQL is running and database 'sims_db' exists
```

### Login Issues
```
Solution: Verify database.sql was executed successfully
Default admin user might not be created
Run database.sql again
```

### Permission Denied Errors
```
Solution: Check folder permissions
Upload directory needs to be writable (uploads/)
Logs directory needs to be writable (logs/)
```

### Pages Not Loading
```
Solution: Verify PHP version is 7.0 or higher
Check phpinfo() for MySQLi extension
Ensure web server is running
```

## Performance Optimization

- Database queries optimized with indexes
- Compiled CSS for faster loading
- Minimal external dependencies
- Efficient session management
- Query result caching where appropriate

## Future Enhancements

- Email notifications for grades and announcements
- Online exam system
- Student fee management
- Hostel management
- Library management integration
- Advanced analytics dashboard
- Mobile app version
- API for third-party integrations

## Support & Documentation

For issues or questions:
1. Check this README thoroughly
2. Review database schema in `setup/database.sql`
3. Check error logs in `logs/` directory
4. Review activity logs in admin dashboard

## License

This project is provided as-is for educational and institutional use.

## Version History

**Version 1.0.0** (March 2024)
- Initial release
- Complete CRUD operations for all modules
- User authentication and authorization
- Grade and attendance management
- Comprehensive reporting

---

**Last Updated:** March 9, 2024
