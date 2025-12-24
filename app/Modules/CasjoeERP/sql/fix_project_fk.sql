ALTER TABLE erp_projects DROP FOREIGN KEY erp_projects_ibfk_1;
ALTER TABLE erp_projects
ADD CONSTRAINT fk_project_customer FOREIGN KEY (client_id) REFERENCES erp_crm_customers(id) ON DELETE
SET NULL;