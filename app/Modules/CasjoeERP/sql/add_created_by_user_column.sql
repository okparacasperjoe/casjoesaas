-- Add created_by_user column to erp_tasks table
-- This column tracks which user created each task for permission checks
ALTER TABLE erp_tasks
ADD COLUMN created_by_user INT DEFAULT NULL
AFTER assigned_to;