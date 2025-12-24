CREATE TABLE IF NOT EXISTS erp_crm_opportunities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    value DECIMAL(10, 2) DEFAULT 0.00,
    stage ENUM(
        'prospecting',
        'negotiation',
        'closed-won',
        'closed-lost'
    ) DEFAULT 'prospecting',
    lead_id INT,
    customer_id INT,
    close_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES erp_crm_leads(id) ON DELETE
    SET NULL,
        FOREIGN KEY (customer_id) REFERENCES erp_crm_customers(id) ON DELETE
    SET NULL
);
CREATE TABLE IF NOT EXISTS erp_crm_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES erp_crm_customers(id)
);
CREATE TABLE IF NOT EXISTS erp_crm_sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    inventory_item_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES erp_crm_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_item_id) REFERENCES erp_inventory_items(id)
);