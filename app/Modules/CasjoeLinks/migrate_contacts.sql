-- Add metadata column to erp_crm_leads if it doesn't exist
ALTER TABLE erp_crm_leads
ADD COLUMN IF NOT EXISTS metadata TEXT;
-- Add source column to erp_crm_leads if it doesn't exist  
ALTER TABLE erp_crm_leads
ADD COLUMN IF NOT EXISTS source VARCHAR(100);
-- Update existing leads to have empty metadata
UPDATE erp_crm_leads
SET metadata = '{}'
WHERE metadata IS NULL;