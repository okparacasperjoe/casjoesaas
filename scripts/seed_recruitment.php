<?php
require_once __DIR__ . '/../app/core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();
$tenantId = 1;

echo "Seeding Recruitment Module...\n";

// 1. Create Tables
$pdo->exec("
    CREATE TABLE IF NOT EXISTS erp_job_postings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        department VARCHAR(100),
        salary VARCHAR(100),
        description TEXT,
        status ENUM('active', 'closed', 'draft') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Ensure salary column exists if table was already created
    ALTER TABLE erp_job_postings ADD COLUMN IF NOT EXISTS salary VARCHAR(100) AFTER department;

    CREATE TABLE IF NOT EXISTS erp_job_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        job_id INT NOT NULL,
        candidate_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        status ENUM('pending', 'interview', 'hired', 'rejected') DEFAULT 'pending',
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (job_id) REFERENCES erp_job_postings(id) ON DELETE CASCADE
    );
");

// 2. Clear old data (optional, maybe just ignore if exists)
// $pdo->exec("DELETE FROM erp_job_postings"); 

// 3. Seed Jobs
$stmt = $pdo->prepare("INSERT IGNORE INTO erp_job_postings (id, tenant_id, title, department, salary, description, status) VALUES 
(1, ?, 'Senior Frontend Developer', 'Engineering', '$120k - $150k', 'We are looking for a React expert...', 'active'),
(2, ?, 'Sales Representative', 'Sales', '$60k + Commission', 'Join our dynamic sales team...', 'active'),
(3, ?, 'HR Manager', 'Human Resources', '$80k - $100k', 'Lead our HR operations...', 'active')
");
$stmt->execute([$tenantId, $tenantId, $tenantId]);

// 4. Seed Applications
$stmt = $pdo->prepare("INSERT IGNORE INTO erp_job_applications (tenant_id, job_id, candidate_name, email, status) VALUES 
(?, 1, 'John Doe', 'john@example.com', 'interview'),
(?, 1, 'Jane Smith', 'jane@example.com', 'pending'),
(?, 2, 'Mike Johnson', 'mike@sales.com', 'rejected')
");
$stmt->execute([$tenantId, $tenantId, $tenantId]);

echo "Recruitment Data Seeded.\n";
