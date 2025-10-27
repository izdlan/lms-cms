# How to Test Student Enrollments

## Quick Test Commands

### 1. Check Enrollment Counts
```bash
php artisan tinker
```
Then run:
```php
// Count total enrollments
App\Models\StudentEnrollment::count();

// Count by program
App\Models\StudentEnrollment::where('program_code', 'EMBA')->count(); // Should be 84
App\Models\StudentEnrollment::where('program_code', 'EBBA')->count(); // Should be 72
App\Models\StudentEnrollment::where('program_code', 'EDBA')->count(); // Should be 12

// Check a specific student (e.g., SAIF ALI HMOUD RASHED ALDEREI)
$student = App\Models\User::where('name', 'like', '%SAIF ALI HMOUD%')->first();
App\Models\StudentEnrollment::where('user_id', $student->id)->count(); // Should be 12
```

### 2. Test in Browser
1. **Login as student** (e.g., SAIF ALI HMOUD RASHED ALDEREI)
   - Password: their IC number (from the database)
2. **Go to Dashboard**
   - Should see "MY SUBJECTS" count = 12
   - Should see "MY LECTURERS" count = 12
3. **Check "My Subjects"**
   - Should show 12 enrolled subjects
   - Each subject should have a lecturer assigned
4. **Check Lecturer Info**
   - Click on "My Lecturers"
   - Should see all 12 lecturers for the subjects

### 3. Login Credentials
Find student login info:
```bash
php artisan tinker
```
```php
$student = App\Models\User::where('name', 'like', '%SAIF ALI HMOUD%')->first();
echo "Email: " . $student->email . "\n";
echo "IC: " . $student->ic . "\n";
```

## Enrollment Summary
- **EMBA Students**: 7 students × 12 subjects = 84 enrollments
- **EBBA Students**: 6 students × 12 subjects = 72 enrollments  
- **EDBA Student**: 1 student × 12 subjects = 12 enrollments
- **Total**: 168 enrollments

## Re-Running Enrollment
If you need to add more students later:
```bash
php artisan students:enroll-all
```
This will only enroll students who are not already enrolled.

## Alternative: Use the Admin UI
1. Go to **Admin Panel** → **Students**
2. Click on a student
3. View their enrollments
4. You can manually add/remove enrollments from the UI

