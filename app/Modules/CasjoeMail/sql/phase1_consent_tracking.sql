-- CasjoeMail Phase 1: Consent Tracking Migration
-- Adds consent fields to cm_subscribers table
ALTER TABLE cm_subscribers
ADD COLUMN consent_method VARCHAR(50) DEFAULT NULL COMMENT 'form, whatsapp, manual, import';
ALTER TABLE cm_subscribers
ADD COLUMN consent_proof TEXT DEFAULT NULL COMMENT 'JSON: {ip, timestamp, source_url, message}';
ALTER TABLE cm_subscribers
ADD COLUMN consent_date TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE cm_subscribers
ADD COLUMN consent_ip VARCHAR(45) DEFAULT NULL;
-- Add company, phone, tags if they don't exist (safety check)
-- Based on the existing schema, these already exist but we'll add indexes
ALTER TABLE cm_subscribers
ADD INDEX idx_consent_method (consent_method);
ALTER TABLE cm_subscribers
ADD INDEX idx_consent_date (consent_date);