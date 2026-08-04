-- Add indexes to existing tables for performance optimization
-- This script should be run in phpMyAdmin or MySQL command line

-- Users table indexes
ALTER TABLE users ADD INDEX idx_username (username);
ALTER TABLE users ADD INDEX idx_role (role);

-- Projects table indexes
ALTER TABLE projects ADD INDEX idx_user_id (user_id);
ALTER TABLE projects ADD INDEX idx_status (status);
ALTER TABLE projects ADD INDEX idx_created_at (created_at);

-- Project Milestones indexes
ALTER TABLE project_milestones ADD INDEX idx_project_id (project_id);
ALTER TABLE project_milestones ADD INDEX idx_stage (stage);

-- Transactions table indexes
ALTER TABLE transactions ADD INDEX idx_project_id (project_id);
ALTER TABLE transactions ADD INDEX idx_status (status);
ALTER TABLE transactions ADD INDEX idx_type (type);
ALTER TABLE transactions ADD INDEX idx_created_at (created_at);
ALTER TABLE transactions ADD INDEX idx_reviewed_by (reviewed_by);
ALTER TABLE transactions ADD INDEX idx_recorded_by (recorded_by);

-- Reports table indexes
ALTER TABLE reports ADD INDEX idx_user_id (user_id);
ALTER TABLE reports ADD INDEX idx_year_month (year, month);
ALTER TABLE reports ADD INDEX idx_status (status);
ALTER TABLE reports ADD INDEX idx_submitted_at (submitted_at);

-- Certifications table indexes
ALTER TABLE certifications ADD INDEX idx_transaction_id (transaction_id);
ALTER TABLE certifications ADD INDEX idx_issued_by (issued_by);
ALTER TABLE certifications ADD INDEX idx_type (type);

-- Feedback table indexes
ALTER TABLE feedback ADD INDEX idx_project_id (project_id);
ALTER TABLE feedback ADD INDEX idx_read_at (read_at);
ALTER TABLE feedback ADD INDEX idx_created_at (created_at);
