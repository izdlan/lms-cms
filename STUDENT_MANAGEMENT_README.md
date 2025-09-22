# LMS Olympia - Student Management System

This system allows you to use Excel as a database for student information and provides separate login portals for students and administrators.

## Features

### Excel as Database
- Students are imported from Excel files
- IC number is used as the default password for all students
- Automatic synchronization when Excel file is updated
- Support for courses, contact information, and other student data

### Dual Login System
- **Student Login**: Uses IC number and password
- **Admin Login**: Uses email and password
- Separate dashboards and functionalities for each role

### Admin Features
- Import students from Excel files
- Manage student information
- View student statistics
- Sync students automatically from Excel

### Student Features
- View assigned courses
- Change password via email
- Access to student dashboard

## Setup Instructions

### 1. Database Setup
```bash
# Run migrations to add student fields
php artisan migrate

# Create admin user
php artisan db:seed --class=AdminUserSeeder
```

### 2. Excel File Format
Your Excel file should have these columns:
- `name` - Student's full name
- `ic` - IC number (used as password)
- `email` - Email address
- `number` - Phone number (optional)
- `courses` - Comma-separated courses (optional)

### 3. Configuration
Add to your `.env` file:
```env
STUDENTS_EXCEL_PATH=/path/to/your/students.xlsx
STUDENTS_AUTO_SYNC_INTERVAL=60
```

### 4. Admin User
Default admin credentials:
- Email: admin@lms-olympia.com
- Password: admin123

## Usage

### For Administrators

1. **Login**: Go to `/login` and select "Admin Login"
2. **Import Students**: 
   - Go to Admin Dashboard → Import Students
   - Upload your Excel file
   - Students will be imported with IC as password

3. **Manage Students**:
   - View all students in the Students section
   - Edit student information
   - Add new students manually

4. **Auto Sync**:
   - Place your Excel file in the configured path
   - The system will automatically sync changes every hour

### For Students

1. **Login**: Go to `/login` and select "Student Login"
2. **Credentials**: Use IC number as both username and password
3. **Change Password**: Use the "Forgot Password" link to reset via email

## Commands

### Manual Import
```bash
# Import from specific file
php artisan import:students /path/to/students.xlsx

# Sync from configured file
php artisan students:sync /path/to/students.xlsx

# Auto-sync (checks for updates)
php artisan students:auto-sync
```

### Scheduled Tasks
The system automatically runs `students:auto-sync` every hour. To enable this:
```bash
# Add to your crontab
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## File Structure

```
app/
├── Console/Commands/
│   ├── ImportStudents.php          # Manual import command
│   ├── SyncStudentsFromExcel.php   # Sync command
│   └── AutoSyncStudents.php        # Auto-sync command
├── Http/Controllers/
│   ├── Auth/
│   │   ├── StudentAuthController.php
│   │   └── AdminAuthController.php
│   └── AdminController.php
├── Imports/
│   └── StudentsImport.php          # Excel import logic
├── Models/
│   └── User.php                    # Updated with student fields
└── Services/
    └── StudentSyncService.php      # Sync service

resources/views/
├── auth/
│   ├── login-selection.blade.php
│   ├── student-login.blade.php
│   └── admin-login.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── students.blade.php
│   └── import-students.blade.php
└── student/
    └── dashboard.blade.php
```

## Security Notes

- IC numbers are used as passwords initially
- Students can change passwords via email
- Admin access is protected by role-based middleware
- All passwords are hashed using Laravel's Hash facade

## Troubleshooting

### Database Connection Issues
- Ensure your database is running
- Check `.env` database configuration
- Run `php artisan config:clear`

### Excel Import Issues
- Verify Excel file format matches requirements
- Check file permissions
- Review Laravel logs for detailed error messages

### Auto-Sync Not Working
- Ensure cron is set up correctly
- Check if Excel file path is correct
- Verify file permissions
- Run `php artisan schedule:list` to see scheduled tasks

## Support

For issues or questions, check the Laravel logs in `storage/logs/laravel.log` or contact your system administrator.
