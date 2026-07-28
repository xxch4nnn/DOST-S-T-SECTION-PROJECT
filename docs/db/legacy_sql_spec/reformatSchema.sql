-- 1. Disable foreign key checks to prevent relationship errors
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Drop the tables
DROP TABLE IF EXISTS 
    scholars, 
    scholarships, 
    scholarship_types, 
    schools, 
    courses, 
    regions, 
    clearance_statuses, 
    file_groups,
    file_types,
    files_metadata,
    audit_logs;

-- 3. Re-enable foreign key checks to protect your database structure moving forward
SET FOREIGN_KEY_CHECKS = 1;