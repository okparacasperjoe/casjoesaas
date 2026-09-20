-- Migration: Rebrand Casjoe Academy to Casjoe Business School
-- This script updates the modules table to reflect the new branding
UPDATE modules
SET name = 'Casjoe Business School'
WHERE slug = 'casjoe-academy';
-- Verify the change
SELECT *
FROM modules
WHERE slug = 'casjoe-academy';