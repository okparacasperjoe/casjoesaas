<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Seeding Corporate Courses...\n";

// Clear existing system courses to avoid duplicates (optional, use with caution)
// $pdo->exec("DELETE FROM academy_courses WHERE is_system_course = 1");

try {

$courses = [
    [
        'title' => 'Business Finance & Bookkeeping for SMEs',
        'category' => 'Operations',
        'price' => 10.00,
        'description' => 'Master cash flow, expense tracking, and digital bookkeeping.',
        'thumbnail' => 'https://images.pexels.com/photos/53621/calculator-calculation-insurance-finance-53621.jpeg?auto=compress&cs=tinysrgb&w=600',
        'sections' => [
            'Understanding Income & Expenses', 
            'Cash Flow Management',
            'Digital Bookkeeping Systems'
        ]
    ],
    [
        'title' => 'HR & Team Management Using ERP',
        'category' => 'Operations',
        'price' => 10.00,
        'description' => 'Streamline onboarding, payroll, and performance tracking.',
        'thumbnail' => 'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=600',
        'sections' => [
            'Staff Onboarding',
            'Payroll & Salary Management',
            'Performance Tracking'
        ]
    ],
    [
        'title' => 'Selling Physical Products Online',
        'category' => 'Sales',
        'price' => 10.00,
        'description' => 'From sourcing to fulfillment: A complete guide to e-commerce logistics.',
        'thumbnail' => 'https://images.pexels.com/photos/4481259/pexels-photo-4481259.jpeg?auto=compress&cs=tinysrgb&w=600',
        'sections' => [
            'Product Sourcing & Pricing',
            'Order Fulfillment',
            'Customer Support Systems'
        ]
    ],
    [
        'title' => 'Selling Digital Products & Services',
        'category' => 'Sales',
        'price' => 10.00,
        'description' => 'Monetize expertise with digital delivery and automated checkout flows.',
        'thumbnail' => 'https://images.pexels.com/photos/577585/pexels-photo-577585.jpeg?auto=compress&cs=tinysrgb&w=600',
        'sections' => [
            'Digital Product Types',
            'Pricing Strategies',
            'Automated Delivery'
        ]
    ],
    [
        'title' => 'Lead Generation & Email Marketing',
        'category' => 'Marketing',
        'price' => 10.00,
        'description' => 'Build high-converting funnels and automate your customer communication.',
        'thumbnail' => 'https://images.pexels.com/photos/590022/pexels-photo-590022.jpeg?auto=compress&cs=tinysrgb&w=600',
        'sections' => [
            'Building Lead Funnels',
            'Email Campaigns',
            'CRM Integration'
        ]
    ],
    [
        'title' => 'Business Automation & AI for SMEs',
        'category' => 'Technology',
        'price' => 10.00,
        'description' => 'Leverage AI to remove repetitive tasks and scale your operations.',
        'thumbnail' => 'https://images.pexels.com/photos/2599244/pexels-photo-2599244.jpeg?auto=compress&cs=tinysrgb&w=600',
        'sections' => [
            'Identifying Repetitive Tasks',
            'Automation Tools',
            'AI Use Cases'
        ]
    ]
];

// Use a System Tenant ID (e.g., 0 or 1) for these global courses
// Fetch a valid system tenant (usually ID 1)
$stmt = $pdo->query("SELECT id FROM tenants ORDER BY id ASC LIMIT 1");
$systemTenantId = $stmt->fetchColumn() ?: 1; // Default to 1 if empty
echo "Using System Tenant ID: $systemTenantId\n"; 

foreach ($courses as $c) {
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM academy_courses WHERE title = ? AND is_system_course = 1");
    $stmt->execute([$c['title']]);
    $exists = $stmt->fetch();

    if (!$exists) {
        // Insert Course
        $stmt = $pdo->prepare("INSERT INTO academy_courses (tenant_id, title, description, category, price_per_seat, thumbnail, is_system_course, status) VALUES (?, ?, ?, ?, ?, ?, 1, 'published')");
        $stmt->execute([$systemTenantId, $c['title'], $c['description'], $c['category'], $c['price'], $c['thumbnail']]);
        $courseId = $pdo->lastInsertId();
        echo "Created Course: {$c['title']} (ID: $courseId)\n";

        // Create Sections & Dummy Lessons
        foreach ($c['sections'] as $i => $sectionTitle) {
            echo "  > Section: $sectionTitle\n";
            $stmt = $pdo->prepare("INSERT INTO academy_sections (course_id, title, sort_order) VALUES (?, ?, ?)");
            $stmt->execute([$courseId, $sectionTitle, $i]);
            $sectionId = $pdo->lastInsertId();

            // Add a lesson
            $pdo->prepare("INSERT INTO academy_lessons (section_id, title, content, sort_order) VALUES (?, ?, ?, 0)")
                ->execute([$sectionId, "Introduction to $sectionTitle", "Welcome to this lesson..."]);
        }
        
        // Create a Quiz
        $stmt = $pdo->prepare("INSERT INTO academy_quizzes (course_id, title, passing_score) VALUES (?, ?, 70)");
        $stmt->execute([$courseId, "Final Assessment: {$c['title']}"]);
    } else {
        echo "Skipping existing: {$c['title']}\n";
    }
}

} catch (PDOException $e) {
    echo "SEED ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Seeding Complete!\n";
