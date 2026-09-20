-- Migration: Rebrand Casjoe ERP to Casjoe BOS
-- This script updates the modules table to reflect the new branding
UPDATE modules
SET name = 'Casjoe BOS'
WHERE slug = 'casjoe-erp';
-- Verify the change
SELECT *
FROM modules
WHERE slug = 'casjoe-erp';