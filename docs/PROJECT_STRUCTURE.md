# LMS Olympia - Project Structure

## 📁 Directory Organization

This document outlines the professional organization of the LMS Olympia project files and directories.

### 🏗️ Core Application Structure
```
LMS_Olympia/
├── app/                          # Laravel application core
│   ├── Console/Commands/         # Artisan commands
│   ├── Http/Controllers/         # MVC controllers
│   ├── Imports/                  # Excel import classes
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic services
│   └── Providers/                # Service providers
├── config/                       # Configuration files
├── database/                     # Migrations, seeders, factories
├── public/                       # Web-accessible files
├── resources/                    # Views, assets, language files
├── routes/                       # Route definitions
├── storage/                      # File storage, logs, cache
└── vendor/                       # Composer dependencies
```

### 📚 Documentation
```
docs/
├── setup/                        # Setup and configuration guides
│   ├── GOOGLE_SHEETS_API_SETUP.md
│   ├── GOOGLE_SHEETS_INTEGRATION_README.md
│   └── ONEDRIVE_AUTOMATION_SETUP.md
├── testing/                      # Testing documentation
│   └── (testing guides and documentation)
├── AUTOMATION_REORGANIZATION_SUMMARY.md
├── ONLINE_IMPORT_SOLUTIONS.md
├── PROJECT_STRUCTURE.md          # This file
└── STUDENT_MANAGEMENT_README.md
```

### 🛠️ Scripts and Utilities
```
scripts/
├── debug/                        # Debug and troubleshooting scripts
│   ├── debug_column_mismatch.php
│   ├── debug_column_structures.php
│   ├── debug_detailed_import.php
│   ├── debug_missing_email.php
│   ├── debug_name_mapping.php
│   ├── debug_onedrive_import.php
│   ├── debug_problematic_sheets.php
│   ├── debug_remaining_issues.php
│   ├── debug_string_matching.php
│   └── debug_student_data.php
├── setup/                        # Setup and configuration scripts
│   ├── configure_google_sheets.php
│   ├── get_sheet_ids.php
│   ├── google_sheets_automation_watcher.php
│   ├── setup_auto_import.php
│   ├── setup_online_import.php
│   └── update_env.php
└── testing/                      # Test scripts and utilities
    ├── check_columns.php
    ├── simple_import_test.php
    ├── test_all_sheets_import.php
    ├── test_excel_import.php
    ├── test_google_sheets_import.php
    ├── test_hybrid_import.php
    ├── test_import_logic.php
    ├── test_import_step_by_step.php
    ├── test_manual_import.php
    ├── test_new_onedrive.php
    ├── test_onedrive_debug.php
    ├── test_onedrive_direct.php
    ├── test_onedrive_import.php
    ├── test_onedrive_online.php
    ├── test_onedrive_redirect.php
    ├── test_onedrive_urls.php
    └── test_single_sheet_import.php
```

### 📊 Data Files
```
data/                             # Sample and reference data
├── Enrollment OEM.xlsx          # Main enrollment data
└── sample_students.csv          # Sample student data
```

### 🔧 Automation
```
automation/                       # Automation system
├── batch/                        # Batch files and PowerShell scripts
├── config/                       # Automation configuration
├── logs/                         # Automation logs
├── scripts/                      # Automation PHP scripts
└── test/                         # Automation test files
```

### 🗂️ Temporary Files
```
temp/                             # Temporary and working files
├── debug/                        # Debug output files
├── testing/                      # Test output files
└── temp_mail.txt                # Temporary email file
```

## 🎯 File Categories

### ✅ **Production Files** (Keep in root)
- `composer.json` & `composer.lock` - Dependency management
- `package.json` & `package-lock.json` - Node.js dependencies
- `artisan` - Laravel command-line interface
- `phpunit.xml` - Testing configuration
- `vite.config.js` - Asset compilation configuration

### 📁 **Organized Files** (Moved to appropriate directories)
- **Documentation** → `docs/`
- **Setup Scripts** → `scripts/setup/`
- **Debug Scripts** → `scripts/debug/`
- **Test Scripts** → `scripts/testing/`
- **Data Files** → `data/`
- **Temporary Files** → `temp/`

### 🗑️ **Files to Consider Removing** (Optional cleanup)
- Debug scripts in `scripts/debug/` (can be removed after issues are resolved)
- Test scripts in `scripts/testing/` (can be archived)
- Temporary files in `temp/` (can be cleaned periodically)

## 🔄 Maintenance Guidelines

### Regular Cleanup
1. **Weekly**: Clean `temp/` directory
2. **Monthly**: Review and archive old debug scripts
3. **Quarterly**: Update documentation in `docs/`

### File Naming Conventions
- **Debug files**: `debug_[purpose].php`
- **Test files**: `test_[feature].php`
- **Setup files**: `setup_[service].php` or `configure_[service].php`
- **Documentation**: `[TOPIC]_[TYPE].md`

### Directory Permissions
- `storage/` and `temp/` should be writable
- `public/` should be web-accessible
- `scripts/` should be executable

## 📋 Quick Reference

| Purpose | Location | Example |
|---------|----------|---------|
| Setup guides | `docs/setup/` | `ONEDRIVE_AUTOMATION_SETUP.md` |
| Debug scripts | `scripts/debug/` | `debug_missing_email.php` |
| Test scripts | `scripts/testing/` | `test_onedrive_import.php` |
| Data files | `data/` | `Enrollment OEM.xlsx` |
| Temp files | `temp/` | `temp_mail.txt` |

---

**Last Updated**: September 25, 2025  
**Maintained By**: LMS Development Team
