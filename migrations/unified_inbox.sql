-- Unified Inbox: Conversations
CREATE TABLE IF NOT EXISTS inbox_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    contact_id INT NULL COMMENT 'FK to erp_crm_leads.id',
    contact_name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255) NULL,
    contact_phone VARCHAR(50) NULL,
    channel ENUM('email','whatsapp','support','form','chat','sms','webhook') NOT NULL,
    subject VARCHAR(255) NULL,
    status ENUM('open','closed','snoozed') DEFAULT 'open',
    assigned_to INT NULL COMMENT 'FK to users.id',
    last_message_at DATETIME NULL,
    last_message_preview VARCHAR(500) NULL,
    unread_count INT DEFAULT 0,
    source_type VARCHAR(50) NULL COMMENT 'e.g. cs_tickets, smart_form_submissions',
    source_id INT NULL COMMENT 'FK to source record',
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_inbox_tenant (tenant_id),
    INDEX idx_inbox_contact (contact_id),
    INDEX idx_inbox_status (tenant_id, status),
    INDEX idx_inbox_channel (tenant_id, channel),
    INDEX idx_inbox_last_msg (tenant_id, last_message_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Unified Inbox: Messages
CREATE TABLE IF NOT EXISTS inbox_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    tenant_id INT NOT NULL,
    direction ENUM('inbound','outbound') NOT NULL,
    sender_type ENUM('contact','user','system','ai') NOT NULL,
    sender_id INT NULL,
    sender_name VARCHAR(255) NULL,
    content TEXT NOT NULL,
    content_type ENUM('text','html','form_data','attachment') DEFAULT 'text',
    channel ENUM('email','whatsapp','support','form','chat','sms','webhook') NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_msg_conversation (conversation_id),
    INDEX idx_msg_tenant (tenant_id),
    INDEX idx_msg_read (conversation_id, is_read),
    FOREIGN KEY (conversation_id) REFERENCES inbox_conversations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
