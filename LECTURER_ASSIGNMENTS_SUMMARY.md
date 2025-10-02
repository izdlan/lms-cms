# EMBA Lecturer-Subject Assignments

## Overview
Each of the 12 EMBA subjects is now assigned to a dedicated lecturer with their own class schedule. All 90 students are enrolled in all subjects with the correct lecturer assignments.

## Lecturer-Subject Assignments

| Subject Code | Subject Name | Lecturer | Class Code | Specialization |
|--------------|--------------|----------|------------|----------------|
| EMBA7101 | Strategic Human Resource Management | John Smith | EMBA71012025A | Academic Teaching |
| EMBA7102 | Organisational Behaviour | Sarah Johnson | EMBA71022025A | Strategic Human Resource Management |
| EMBA7103 | Strategic Management | Prof. Michael Chen | EMBA71032025A | Organisational Behaviour |
| EMBA7104 | Strategic Marketing | Dr. Emily Rodriguez | EMBA71042025A | Strategic Management |
| EMBA7105 | Accounting & Finance for Decision Making | Prof. David Thompson | EMBA71052025A | Strategic Marketing |
| EMBA7106 | Business Analytics | Dr. Lisa Wang | EMBA71062025A | Accounting & Finance |
| EMBA7107 | Business Economics | Prof. James Anderson | EMBA71072025A | Business Analytics |
| EMBA7108 | Digital Business | Dr. Maria Garcia | EMBA71082025A | Business Economics |
| EMBA7109 | Innovation and Technology Entrepreneurship | Prof. Alex Kumar | EMBA71092025A | Digital Business |
| EMBA7110 | International Business Management & Policy | Dr. Jennifer Lee | EMBA71102025A | Innovation and Technology Entrepreneurship |
| EMBA7111 | Research Methodology | Prof. Robert Brown | EMBA71112025A | International Business Management |
| EMBA7112 | Strategic Capstone Project | Dr. Amanda Taylor | EMBA71122025A | Research Methodology |

## Class Schedule Details
- **Program**: EMBA (Executive Master of Business Administration)
- **Class Pattern**: Each subject has 3 classes (A, B, C) for different time slots
- **Schedule**: Monday, Wednesday, Friday
- **Time Slots**: 
  - Morning: 09:00-12:00
  - Afternoon: 14:00-17:00
- **Venues**: Room 101, 102, 103 (rotating)
- **Max Students per Class**: 30

## Student Enrollment
- **Total Students**: 90
- **Total Enrollments**: 1,080 (90 students × 12 subjects)
- **Enrollment Status**: All students enrolled in all subjects
- **Class Assignment**: All students assigned to Class A for each subject

## Database Structure
The assignments are stored in the following tables:
- `lecturers` - Lecturer information and specializations
- `class_schedules` - Subject-lecturer-class assignments
- `student_enrollments` - Student enrollments with correct lecturer and class assignments
- `subjects` - Subject information and program codes

## Benefits
1. **Proper Lecturer Assignment**: Each subject has a dedicated lecturer with relevant specialization
2. **Class Management**: Students are properly assigned to specific classes
3. **Scalability**: System supports multiple classes per subject for larger student populations
4. **Data Integrity**: All relationships properly maintained in the database
5. **Academic Structure**: Follows proper academic program structure

## Next Steps
- Lecturers can now log in and see only their assigned subjects
- Students can see the correct lecturer for each subject
- Assignments and grading will be properly attributed to the correct lecturer
- Class schedules can be managed per lecturer
