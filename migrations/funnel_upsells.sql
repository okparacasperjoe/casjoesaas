-- Funnel Upsells & Step Types Migration
ALTER TABLE funnel_steps MODIFY COLUMN step_type VARCHAR(50) NOT NULL;
ALTER TABLE sales_funnels MODIFY COLUMN type VARCHAR(50) NOT NULL;
