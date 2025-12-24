CREATE TABLE IF NOT EXISTS erp_job_postings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    description TEXT,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS erp_job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    job_id INT NOT NULL,
    candidate_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    resume_path VARCHAR(255),
    status ENUM('received', 'shortlisted', 'rejected', 'hired') DEFAULT 'received',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES erp_job_postings(id) ON DELETE CASCADE
);