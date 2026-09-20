-- Add metadata column to erp_crm_opportunities if it doesn't exist
ALTER TABLE erp_crm_opportunities
ADD COLUMN IF NOT EXISTS metadata TEXT;
-- Add source column to erp_crm_opportunities if it doesn't exist
ALTER TABLE erp_crm_opportunities
ADD COLUMN IF NOT EXISTS source VARCHAR(100);
-- Add closed_at column to erp_crm_opportunities if it doesn't exist
ALTER TABLE erp_crm_opportunities
ADD COLUMN IF NOT EXISTS closed_at DATETIME;
-- Update existing opportunities to have empty metadata
UPDATE erp_crm_opportunities
SET metadata = '{}'
WHERE metadata IS NULL;