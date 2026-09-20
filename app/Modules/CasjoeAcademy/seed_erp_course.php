<?php
// ERP Management Mastery Course Seeder
// Run: php app/Modules/CasjoeAcademy/seed_erp_course.php
// Or visit via browser after deploying to public/

require_once __DIR__ . '/../../Core/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();
$tenantId = 1;

// --- HTML Helper Functions ---
function tip($text) {
    return "<div style='background:#fff8e1;border-left:4px solid #FFA600;padding:15px 20px;margin:20px 0;border-radius:0 8px 8px 0;'><strong>💡 Pro Tip:</strong> $text</div>";
}
function warn($text) {
    return "<div style='background:#fce4ec;border-left:4px solid #e74a3b;padding:15px 20px;margin:20px 0;border-radius:0 8px 8px 0;'><strong>🚨 Important:</strong> $text</div>";
}
function objectives($items) {
    $li = implode('', array_map(fn($i) => "<li style='margin:5px 0;'>$i</li>", $items));
    return "<div style='background:linear-gradient(135deg,#000066,#1a1a8e);color:white;padding:25px 30px;border-radius:12px;margin-bottom:25px;'><h3 style='margin:0 0 10px;color:#FFA600;'>🎯 Learning Objectives</h3><ul style='margin:0;padding-left:20px;'>$li</ul></div>";
}
function takeaways($items) {
    $li = implode('', array_map(fn($i) => "<li style='margin:5px 0;'>$i</li>", $items));
    return "<div style='background:#e8f5e9;border-left:4px solid #2e7d32;padding:20px;margin:25px 0;border-radius:0 8px 8px 0;'><h3 style='margin:0 0 10px;color:#2e7d32;'>✅ Key Takeaways</h3><ul style='margin:0;padding-left:20px;'>$li</ul></div>";
}
function exercise($text) {
    return "<div style='background:#e3f2fd;border:2px dashed #1976d2;padding:20px;margin:25px 0;border-radius:12px;'><h3 style='margin:0 0 10px;color:#1976d2;'>🎯 Hands-On Exercise</h3><p style='margin:0;'>$text</p></div>";
}
function nav($url) {
    return "<div style='background:#f5f5f5;padding:12px 20px;border-radius:8px;margin:15px 0;font-family:monospace;font-size:0.95rem;'>📍 Navigate to: <strong>$url</strong></div>";
}
function steps($items) {
    $out = "<ol style='line-height:1.8;'>";
    foreach($items as $i) $out .= "<li style='margin:8px 0;'>$i</li>";
    return $out . "</ol>";
}

// --- Course Definition ---
$courseTitle = "ERP Management Mastery — Casjoe BOS";
$courseDesc = "Master every module of the Casjoe BOS (Business Operating System). This free, comprehensive course takes you from zero to expert across all ERP modules — HR, Finance, CRM, Projects, Inventory, Scheduling and more. Complete all lessons to earn your official ERP Management Certificate!";

// Check if course exists
$stmt = $db->prepare("SELECT id FROM academy_courses WHERE title = ? AND tenant_id = ?");
$stmt->execute([$courseTitle, $tenantId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if ($course) {
    $courseId = $course['id'];
    echo "Updating existing course ID: $courseId<br>";
    $db->prepare("UPDATE academy_courses SET description = ?, status = 'published', price = 0.00 WHERE id = ?")->execute([$courseDesc, $courseId]);
    // Clear old sections/lessons for clean re-seed
    $db->prepare("DELETE FROM academy_sections WHERE course_id = ?")->execute([$courseId]);
} else {
    echo "Creating new course...<br>";
    $stmt = $db->prepare("INSERT INTO academy_courses (tenant_id, title, description, status, price, thumbnail) VALUES (?, ?, ?, 'published', 0.00, '/assets/course_placeholder.jpg')");
    $stmt->execute([$tenantId, $courseTitle, $courseDesc]);
    $courseId = $db->lastInsertId();
    echo "Created course ID: $courseId<br>";
}

// --- Full Curriculum ---
$curriculum = [

// ===================== SECTION 1 =====================
'Getting Started with Casjoe BOS' => [
    ['title' => 'Welcome & Overview', 'content' =>
        objectives(['Understand what Casjoe BOS is and what it can do', 'Learn the core modules available', 'Know who this course is for']) .
        "<h2>Welcome to Casjoe BOS! 🎉</h2>
        <p><strong>Casjoe BOS (Business Operating System)</strong> is an all-in-one ERP platform that helps you manage every aspect of your business — from finances and human resources to customer relationships and project management.</p>
        <p>Think of it as your company's <strong>digital command center</strong>. Instead of using 10 different apps, Casjoe BOS puts everything in one place.</p>
        <h3>What You'll Learn in This Course</h3>
        <p>This course covers <strong>every single module</strong> of the ERP system:</p>
        <ul><li>📊 <strong>Dashboard</strong> — Your business at a glance</li>
        <li>🎯 <strong>Business Goals</strong> — Track your targets</li>
        <li>🤖 <strong>AI Business Manager</strong> — Get AI-powered insights</li>
        <li>👥 <strong>HR</strong> — Employees, payroll, attendance, leave, and more</li>
        <li>💰 <strong>Finance</strong> — Invoices, expenses, transactions</li>
        <li>🤝 <strong>CRM</strong> — Customers, leads, sales pipeline</li>
        <li>📦 <strong>Inventory</strong> — Products and stock</li>
        <li>📋 <strong>Projects</strong> — Tasks, Kanban boards, timesheets</li>
        <li>📅 <strong>Scheduler</strong> — Appointment booking with Google Calendar</li>
        <li>⚙️ <strong>System</strong> — Settings, roles, announcements</li></ul>" .
        tip('This course is completely free. Complete all lessons to earn your <strong>ERP Management Certificate</strong>!') .
        takeaways(['Casjoe BOS is a multi-module ERP system', 'It covers HR, Finance, CRM, Projects, Inventory, and more', 'All modules are accessible from a single dashboard'])
    ],
    ['title' => 'Navigating the Dashboard', 'content' =>
        objectives(['Navigate to the ERP dashboard', 'Understand each KPI card', 'Read the AI Business Health Score']) .
        nav('/erp or /erp/dashboard') .
        "<h2>The Dashboard — Your Command Center</h2>
        <p>The dashboard is the first thing you see when you open the ERP. It gives you a <strong>real-time snapshot</strong> of your entire business.</p>
        <h3>KPI Cards</h3>
        <p>At the top you'll see key metric cards:</p>
        <table style='width:100%;border-collapse:collapse;margin:15px 0;'><tr style='background:#000066;color:white;'><th style='padding:10px;text-align:left;'>Metric</th><th style='padding:10px;text-align:left;'>What It Shows</th></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'><strong>Revenue</strong></td><td style='padding:10px;'>Total income from all transactions</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'><strong>Employees</strong></td><td style='padding:10px;'>Active employee count</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'><strong>Customers</strong></td><td style='padding:10px;'>Total CRM customers</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'><strong>Pending Tasks</strong></td><td style='padding:10px;'>Tasks awaiting completion</td></tr></table>
        <h3>Business Health Score</h3>
        <p>The <strong>AI-powered health score</strong> (0–100) analyzes your revenue, efficiency, staff performance, and financial health to give you a comprehensive business rating.</p>" .
        tip('If your health score drops below 60, click through to the AI Manager for specific recommendations.') .
        exercise('Log into your ERP at <strong>/erp</strong> and identify all the KPI cards on your dashboard. What is your current business health score?') .
        takeaways(['The dashboard shows Revenue, Employees, Customers, and Pending Tasks', 'The AI Business Health Score rates your business from 0–100', 'Active goals and critical insights appear on the dashboard'])
    ],
    ['title' => 'Understanding Roles & Permissions', 'content' =>
        objectives(['Know the different user roles in the ERP', 'Understand what each role can access', 'Learn how module access works']) .
        "<h2>User Roles in Casjoe BOS</h2>
        <p>Not everyone in your organization needs access to everything. The ERP uses <strong>role-based access control</strong> to ensure the right people see the right things.</p>
        <h3>Available Roles</h3>
        <table style='width:100%;border-collapse:collapse;margin:15px 0;'><tr style='background:#000066;color:white;'><th style='padding:10px;'>Role</th><th style='padding:10px;'>HR</th><th style='padding:10px;'>Finance</th><th style='padding:10px;'>CRM</th><th style='padding:10px;'>Projects</th><th style='padding:10px;'>Settings</th></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'><strong>Admin</strong></td><td style='padding:10px;'>✅</td><td style='padding:10px;'>✅</td><td style='padding:10px;'>✅</td><td style='padding:10px;'>✅</td><td style='padding:10px;'>✅</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'><strong>HR</strong></td><td style='padding:10px;'>✅</td><td style='padding:10px;'>❌</td><td style='padding:10px;'>❌</td><td style='padding:10px;'>✅</td><td style='padding:10px;'>❌</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'><strong>User/Staff</strong></td><td style='padding:10px;'>❌</td><td style='padding:10px;'>❌</td><td style='padding:10px;'>❌</td><td style='padding:10px;'>Own tasks</td><td style='padding:10px;'>❌</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'><strong>Client</strong></td><td style='padding:10px;'>❌</td><td style='padding:10px;'>❌</td><td style='padding:10px;'>❌</td><td style='padding:10px;'>Client portal</td><td style='padding:10px;'>❌</td></tr></table>
        <h3>Module Access</h3>
        <p>When creating employees, you can assign which <strong>platform modules</strong> (not just ERP) they can access — like Casjoe Pay, Academy, etc.</p>" .
        warn('Only Admins can access Finance, CRM management, and System Settings. Regular users can only view their assigned tasks and use the Self-Service Portal.') .
        takeaways(['Admin has full access to everything', 'HR role can manage employees but not finance', 'Staff see only their own tasks', 'Clients get a dedicated portal to view their projects and invoices'])
    ],
],

// ===================== SECTION 2 =====================
'Business Goals' => [
    ['title' => 'Setting Up Business Goals', 'content' =>
        objectives(['Create measurable business goals', 'Track progress with visual indicators', 'Manage goal lifecycle']) .
        nav('/erp/goals') .
        "<h2>Why Goals Matter</h2>
        <p>Every successful business tracks its progress against clear, measurable targets. The <strong>Goals module</strong> lets you set, track, and manage your business objectives.</p>
        <h3>How to Create a Goal</h3>" .
        steps(['Go to <strong>/erp/goals</strong> and click <strong>\"Create Goal\"</strong>',
            'Enter a <strong>Title</strong> — e.g. \"Q1 Revenue Target\" or \"Hire 5 Developers\"',
            'Set the <strong>Target Value</strong> — the number you want to reach',
            'Set the <strong>Current Value</strong> — where you are now',
            'Choose a <strong>Unit</strong> — NGN, USD, customers, hires, etc.',
            'Set a <strong>Deadline</strong>',
            'Click <strong>Save</strong>']) .
        "<h3>Goal Statuses</h3>
        <ul><li>🟢 <strong>Active</strong> — Currently being tracked</li>
        <li>✅ <strong>Completed</strong> — Target achieved!</li>
        <li>❌ <strong>Cancelled</strong> — No longer relevant</li></ul>
        <p>Your top 3 active goals appear on the main ERP dashboard with progress bars.</p>" .
        tip('Update your goal\'s current value regularly. The dashboard progress bars auto-update to reflect your actual progress.') .
        exercise('Create your first business goal: set a revenue target for this month with your current progress.') .
        takeaways(['Goals help you measure business progress', 'Each goal has a target value, current value, and deadline', 'Top 3 active goals display on the dashboard'])
    ],
],

// ===================== SECTION 3 =====================
'AI Business Manager' => [
    ['title' => 'Understanding the Business Health Score', 'content' =>
        objectives(['Understand how the AI health score is calculated', 'Interpret the score breakdown', 'Know what each rating means']) .
        nav('/erp/ai-manager') .
        "<h2>Your AI-Powered Business Analyst 🤖</h2>
        <p>The AI Business Manager automatically analyzes your business data and produces a <strong>Business Health Score from 0 to 100</strong>.</p>
        <h3>Score Breakdown</h3>
        <table style='width:100%;border-collapse:collapse;margin:15px 0;'><tr style='background:#000066;color:white;'><th style='padding:10px;'>Category</th><th style='padding:10px;'>Weight</th><th style='padding:10px;'>What It Measures</th></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'>💰 Revenue</td><td style='padding:10px;'>30%</td><td style='padding:10px;'>Month-over-month revenue growth</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'>⚡ Efficiency</td><td style='padding:10px;'>25%</td><td style='padding:10px;'>Task completion vs overdue rates</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'>👥 Staff</td><td style='padding:10px;'>25%</td><td style='padding:10px;'>Average employee performance</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'>📊 Financial</td><td style='padding:10px;'>20%</td><td style='padding:10px;'>Profit margin (income vs expenses)</td></tr></table>
        <h3>Score Ratings</h3>
        <ul><li>🟢 <strong>90–100: Excellent</strong> — Keep pushing!</li>
        <li>🔵 <strong>75–89: Good</strong> — Room for improvement</li>
        <li>🟡 <strong>60–74: Average</strong> — Mediocre performance</li>
        <li>🔴 <strong>40–59: Poor</strong> — Major issues detected</li>
        <li>⚫ <strong>0–39: Critical</strong> — Business in danger!</li></ul>" .
        warn('A score below 60 requires immediate attention. Use the Stress Tests to identify specific problem areas.') .
        takeaways(['The health score combines 4 weighted categories', 'Revenue growth has the highest weight at 30%', 'Scores below 60 indicate serious business issues'])
    ],
    ['title' => 'Running Stress Tests & Reading Insights', 'content' =>
        objectives(['Run the 4 different stress tests', 'Read and act on AI insights', 'Understand the Weekly Roast']) .
        "<h2>Stress Tests</h2>
        <p>Think of stress tests as <strong>diagnostic scans</strong> for different areas of your business. From the AI Manager dashboard, you can run:</p>
        <table style='width:100%;border-collapse:collapse;margin:15px 0;'><tr style='background:#000066;color:white;'><th style='padding:10px;'>Test</th><th style='padding:10px;'>What It Checks</th></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'>📊 Analytics</td><td style='padding:10px;'>Revenue trends, profitability, operational efficiency</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'>👥 Staff</td><td style='padding:10px;'>Individual employee task completion and overdue rates</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'>🔧 Admin</td><td style='padding:10px;'>System integrity and configuration</td></tr>
        <tr style='border-bottom:1px solid #eee;'><td style='padding:10px;'>🏢 HR</td><td style='padding:10px;'>Pending leave requests and HR operations</td></tr></table>
        <h3>AI Insights</h3>
        <p>After running tests, <strong>insights</strong> appear sorted by severity: 🔴 Critical → 🟡 Warning → 🔵 Info. Click dismiss to mark as read.</p>
        <h3>The Weekly Roast 🔥</h3>
        <p>The AI gives you <strong>brutally honest feedback</strong> on your business performance. If revenue is declining, tasks are overdue, or staff are underperforming — expect to be called out!</p>" .
        exercise('Go to /erp/ai-manager and run all 4 stress tests. Review the insights generated. What areas need improvement?') .
        takeaways(['4 stress tests cover analytics, staff, admin, and HR', 'Insights are sorted by severity — critical first', 'The Weekly Roast provides direct, honest feedback'])
    ],
],

// ===================== SECTION 4 =====================
'HR — Employee Management' => [
    ['title' => 'Adding & Managing Employees', 'content' =>
        objectives(['Add a new employee to the system', 'Create user login accounts for employees', 'Edit and manage employee records']) .
        nav('/erp/employees') .
        "<h2>Employee Management</h2>
        <p>This is where you build your team digitally. Every person who works for you should have an employee record.</p>
        <h3>Adding a New Employee</h3>" .
        steps(['Navigate to <strong>/erp/employees</strong>', 'Click <strong>\"Add Employee\"</strong>',
            'Fill in: <strong>First Name, Last Name, Email</strong> (required)',
            'Select a <strong>Department</strong> from the dropdown',
            'Enter <strong>Job Title, Salary, Hire Date</strong>',
            'Set <strong>Status</strong> to Active',
            '<strong>Optional:</strong> Check \"Create Login\" to give them system access',
            'If creating login: set a <strong>password</strong>, choose a <strong>role</strong> (user/hr/admin), and select <strong>module access</strong>',
            'Click <strong>Save</strong> — a welcome email is automatically sent!']) .
        warn('Free plans are limited to <strong>3 active employees</strong>. Exceeding this redirects to the upgrade page.') .
        "<h3>Editing & Deleting</h3>
        <p>Click <strong>Edit</strong> on any employee to update their details, department, salary, or status. <strong>Delete</strong> removes both the employee record and their user login.</p>" .
        tip('When you create a login for an employee, they receive an email with their credentials. They can then access the Self-Service Portal to check in/out and request leave.') .
        takeaways(['Every team member needs an employee record', 'Creating a login gives them system access', 'Welcome emails are sent automatically', 'Free plans have a 3-employee limit'])
    ],
    ['title' => 'Departments & Designations', 'content' =>
        objectives(['Create and organize departments', 'Assign employees to departments']) .
        nav('/erp/departments') .
        "<h2>Structuring Your Organization</h2>
        <p>Departments help you organize employees into teams. This structure feeds into reports, performance reviews, and task assignments.</p>
        <h3>Managing Departments</h3>" .
        steps(['Go to <strong>/erp/departments</strong>', 'Click <strong>\"Create Department\"</strong>',
            'Enter the department name (e.g. Engineering, Marketing, Sales)',
            'Click Save', 'Employees can now be assigned to this department']) .
        "<h3>Designations</h3>
        <p>View designations (job title categories) at <strong>/erp/designations</strong>. These help standardize roles across your organization.</p>" .
        exercise('Create at least 3 departments for your organization (e.g. Engineering, Marketing, Operations). Then assign existing employees to them.') .
        takeaways(['Departments organize employees into logical groups', 'Every employee should be assigned to a department', 'Designations standardize job titles'])
    ],
],

// ===================== SECTION 5 =====================
'HR — Attendance, Leave & Payroll' => [
    ['title' => 'Tracking Attendance', 'content' =>
        objectives(['Record employee check-ins and check-outs', 'View attendance history', 'Understand self-service attendance']) .
        nav('/erp/attendance') .
        "<h2>Attendance Tracking</h2>
        <p>Monitor when your team members start and end their workday.</p>
        <h3>Admin Check-In (Manual)</h3>" .
        steps(['Go to <strong>/erp/attendance</strong>', 'Select an <strong>employee</strong> from the dropdown',
            'Click <strong>\"Check In\"</strong> — timestamp is recorded',
            'When they leave, click <strong>\"Check Out\"</strong> next to their record']) .
        "<h3>Employee Self-Service</h3>
        <p>Employees with login access can check in/out themselves at <strong>/erp/my-portal</strong> with a single click.</p>" .
        tip('Encourage employees to use the Self-Service Portal for attendance. It reduces admin workload and gives them autonomy.') .
        takeaways(['Attendance records check-in and check-out times', 'Admins can do manual check-ins', 'Employees can self-service at /erp/my-portal'])
    ],
    ['title' => 'Leave Management', 'content' =>
        objectives(['Submit leave requests', 'Approve or reject requests', 'Track leave balances']) .
        nav('/erp/leave') .
        "<h2>Managing Time Off</h2>
        <h3>Requesting Leave</h3>" .
        steps(['Go to <strong>/erp/leave</strong> → Click <strong>\"Request Leave\"</strong>',
            'Select the <strong>Employee</strong>', 'Choose <strong>Leave Type</strong> (Annual, Sick, Personal)',
            'Set <strong>Start Date</strong> and <strong>End Date</strong>', 'Enter a <strong>Reason</strong>',
            'Click <strong>Submit</strong> — status starts as \"Pending\"']) .
        "<h3>Approving / Rejecting</h3>
        <p>As admin/HR, view pending requests and click <strong>Approve</strong> or <strong>Reject</strong>.</p>
        <p>Each employee has <strong>20 leave days per year</strong>. The self-service portal shows their remaining balance.</p>" .
        warn('Pending leave requests trigger alerts in the AI Manager HR stress test. Don\'t leave them hanging!') .
        takeaways(['Leave requests go through an approval workflow', 'Employees get 20 days per year by default', 'Pending requests are flagged by the AI Manager'])
    ],
    ['title' => 'Processing Payroll', 'content' =>
        objectives(['Create payslips for employees', 'Understand gross vs net pay', 'View detailed payslip breakdowns']) .
        nav('/erp/payroll') .
        "<h2>Payroll Processing</h2>
        <h3>Creating a Payslip</h3>" .
        steps(['Go to <strong>/erp/payroll</strong> → Click <strong>\"Create Payslip\"</strong>',
            'Select an <strong>Employee</strong> (salary auto-populates)',
            'Set the <strong>Pay Period</strong> (start and end dates)',
            'Set the <strong>Payment Date</strong>',
            'Enter: <strong>Base Earning</strong> (salary), <strong>Bonus</strong>, <strong>Tax</strong>, <strong>Other Deductions</strong>',
            'System calculates: <strong>Gross = Base + Bonus</strong>, <strong>Net = Gross − Tax − Deductions</strong>',
            'Click <strong>Save</strong>']) .
        "<h3>Viewing Payslips</h3>
        <p>Click <strong>View</strong> on any payroll entry to see the full breakdown: employee details, all earning lines, all deduction lines, and final net pay.</p>
        <p>Employees can view their own payslips in the <strong>Self-Service Portal</strong>.</p>" .
        exercise('Process a payroll for one employee: enter their base salary, add a bonus, apply tax, and review the resulting payslip.') .
        takeaways(['Payroll tracks gross and net pay with line items', 'Tax and deductions are subtracted from gross', 'Employees see payslips in their self-service portal'])
    ],
],

// ===================== SECTION 6 =====================
'HR — Performance, Recruitment & Training' => [
    ['title' => 'Performance Reviews & Recruitment', 'content' =>
        objectives(['Create performance reviews', 'Post job openings', 'Track applications']) .
        "<h2>Performance Management</h2>" . nav('/erp/performance') .
        "<p>Track how well your employees are performing with periodic reviews. Performance scores feed into the <strong>AI Business Manager's staff score</strong>.</p>
        <h3>Recruitment</h3>" . nav('/erp/recruitment') .
        "<p>Post job openings and track incoming applications:</p>" .
        steps(['Go to <strong>/erp/recruitment</strong>', 'Click <strong>\"Post Job\"</strong> and fill in details',
            'View applications at <strong>/erp/recruitment/applications</strong>',
            'Review candidates and move them through the hiring process']) .
        "<h3>Training Programs</h3>" . nav('/erp/training') .
        "<p>Set up training programs for your employees to build skills and track their development.</p>" .
        takeaways(['Performance scores affect the AI health score', 'Post jobs and track applications in one place', 'Training programs help develop your team'])
    ],
    ['title' => 'Employee Lifecycle Events', 'content' =>
        objectives(['Record promotions, resignations, and terminations', 'Maintain a complete employment history']) .
        nav('/erp/lifecycle') .
        "<h2>Tracking Career Events</h2>
        <p>The Employee Lifecycle module records major employment milestones:</p>
        <ul><li>📈 <strong>Promotions</strong> — Document advancement and title changes</li>
        <li>🚪 <strong>Resignations</strong> — Record voluntary departures</li>
        <li>❌ <strong>Terminations</strong> — Document involuntary separations</li></ul>" .
        steps(['Go to <strong>/erp/lifecycle</strong>', 'Click <strong>\"Create Event\"</strong>',
            'Select the type (promotion/resignation/termination)',
            'Add details and dates', 'Save the record']) .
        "<p>Each event is logged with timestamps for a complete audit trail.</p>" .
        takeaways(['Track all major employment events', 'Maintains a complete history for each employee', 'Supports promotions, resignations, and terminations'])
    ],
],

// ===================== SECTION 7 =====================
'Finance — Transactions & Accounts' => [
    ['title' => 'Finance Dashboard & Transactions', 'content' =>
        objectives(['Read the P&L summary', 'Record income and expense transactions', 'View the chart of accounts']) .
        nav('/erp/finance/dashboard') .
        "<h2>Your Financial Overview</h2>
        <p>The Finance Dashboard shows a simple <strong>Profit & Loss</strong>:</p>
        <ul><li>💚 <strong>Total Income</strong> — All income transactions</li>
        <li>🔴 <strong>Total Expenses</strong> — All expense transactions</li>
        <li>📊 <strong>Net Profit</strong> = Income − Expenses</li></ul>
        <h3>Recording Transactions</h3>" . nav('/erp/finance/transactions') .
        steps(['Click <strong>\"New Transaction\"</strong>',
            'Enter a <strong>Description</strong> (e.g. \"Client payment for Project X\")',
            'Enter the <strong>Amount</strong>',
            'Select <strong>Type</strong>: Income or Expense',
            'Set the <strong>Date</strong>', 'Click <strong>Save</strong>']) .
        "<h3>Chart of Accounts</h3>
        <p>At <strong>/erp/finance</strong>, view your chart of accounts (account codes) for structured bookkeeping.</p>" .
        tip('Every invoice payment and expense recorded in other modules automatically creates a transaction here. You don\'t need to double-enter!') .
        takeaways(['The P&L dashboard shows income, expenses, and profit', 'Transactions are the foundation of financial tracking', 'Many modules auto-create transactions'])
    ],
],
]; // End of first half

// --- Insert First Half ---
$sectionOrder = 1;
$insertSection = $db->prepare("INSERT INTO academy_sections (course_id, title, sort_order) VALUES (?, ?, ?)");
$insertLesson = $db->prepare("INSERT INTO academy_lessons (section_id, title, content, sort_order, duration_minutes) VALUES (?, ?, ?, ?, 15)");

foreach ($curriculum as $sectionTitle => $lessons) {
    $insertSection->execute([$courseId, $sectionTitle, $sectionOrder]);
    $sectionId = $db->lastInsertId();
    echo "Section $sectionOrder: $sectionTitle<br>";

    $lessonOrder = 1;
    foreach ($lessons as $l) {
        $insertLesson->execute([$sectionId, $l['title'], $l['content'], $lessonOrder]);
        echo "  Lesson $lessonOrder: {$l['title']}<br>";
        $lessonOrder++;
    }
    $sectionOrder++;
}

// ===================== SECTIONS 8-13 (Second Half) =====================

$curriculum2 = [

// ===================== SECTION 8 =====================
'Finance — Invoices, Estimates & Expenses' => [
    ['title' => 'Creating & Sending Invoices', 'content' =>
        objectives(['Create professional invoices', 'Share public invoice links', 'Process payments']) .
        nav('/erp/finance/invoices') .
        "<h2>Professional Invoicing</h2>
        <p>Send beautiful, shareable invoices to your clients — no third-party tool needed.</p>" .
        steps(['Go to <strong>/erp/finance/invoices</strong> → <strong>\"Create Invoice\"</strong>',
            'Enter <strong>Client Name</strong> and <strong>Email</strong>',
            'Set <strong>Issue Date</strong> and <strong>Due Date</strong>',
            'Add <strong>Line Items</strong>: description, quantity, price',
            'Add optional <strong>Notes</strong>', 'Click <strong>Save</strong>']) .
        "<h3>Public Invoice Link</h3>
        <p>Each invoice gets a unique URL: <code>/invoice/{uuid}</code>. Clients can view and pay — <strong>no login needed</strong>!</p>
        <p>When paid, the invoice status changes to <strong>Paid</strong> and an income transaction is auto-logged.</p>" .
        tip('Share the public invoice link directly with clients via email or WhatsApp for quick payments.') .
        takeaways(['Invoices generate unique public URLs', 'Clients can view and pay without logging in', 'Payments auto-create income transactions'])
    ],
    ['title' => 'Estimates & Expense Tracking', 'content' =>
        objectives(['Create and send estimates', 'Convert accepted estimates to invoices', 'Track expenses with vendor associations']) .
        "<h2>Estimates</h2>" . nav('/erp/finance/estimates') .
        "<p>Send price quotes to potential clients before committing to work.</p>
        <p>Clients can <strong>Accept</strong> or <strong>Reject</strong> estimates via the public link. Once accepted, you can <strong>convert it to an invoice</strong> with one click!</p>
        <h2>Expenses</h2>" . nav('/erp/finance/expenses') .
        steps(['Click <strong>\"New Expense\"</strong>', 'Enter description, amount, date',
            'Select a <strong>Vendor</strong> (optional)', 'Choose a <strong>Category</strong> (Office, Travel, etc.)',
            'Save — an expense transaction is auto-logged']) .
        "<h2>Vendors</h2>" . nav('/erp/finance/vendors') .
        "<p>Manage your suppliers at <strong>/erp/finance/vendors</strong>. Add name, email, phone, and address.</p>" .
        exercise('Create an estimate for a hypothetical client, then practice converting it into an invoice.') .
        takeaways(['Estimates can be accepted, rejected, or converted to invoices', 'Expenses auto-log transactions', 'Vendors help organize your supplier relationships'])
    ],
],

// ===================== SECTION 9 =====================
'CRM — Customers & Leads' => [
    ['title' => 'Managing Customers', 'content' =>
        objectives(['Add and manage customer records', 'Create client portal logins', 'Understand CRM dashboard metrics']) .
        nav('/erp/crm') .
        "<h2>CRM Dashboard</h2>
        <p>Your CRM command center shows: Total Customers, Active Leads, Pipeline Value, Won Deals, and Recent Leads.</p>
        <h3>Adding Customers</h3>" . nav('/erp/crm/customers') .
        steps(['Click <strong>\"Add Customer\"</strong>',
            'Enter <strong>Name, Email, Company, Phone</strong>',
            '<strong>Optional:</strong> Check \"Create Login\" → set a password → customer gets a <strong>Client Portal</strong> account',
            'Click <strong>Save</strong> — a welcome email is sent automatically']) .
        "<p>Customers with portal access can view their projects and invoices at <strong>/erp/client/dashboard</strong>.</p>" .
        tip('Creating portal logins for your key clients builds trust and reduces \"status update\" emails — they can check progress themselves!') .
        takeaways(['CRM centralizes all customer data', 'Portal logins give clients self-service access', 'Welcome emails are sent automatically'])
    ],
    ['title' => 'Lead Tracking & Pipeline', 'content' =>
        objectives(['Add and manage leads', 'Use the visual pipeline board', 'Move leads through stages']) .
        "<h2>Lead Management</h2>" . nav('/erp/crm/leads') .
        steps(['Click <strong>\"Add Lead\"</strong>',
            'Enter: Name, Email, Phone, Company, Source (Website, Referral, etc.)',
            'Add Notes', 'Save — a \"thank you\" email is automatically sent']) .
        "<h3>The Pipeline Board</h3>" . nav('/erp/crm/pipeline') .
        "<p>A <strong>visual Kanban board</strong> showing leads organized by custom stages. Drag leads between columns to update their progress in real-time!</p>
        <h3>Lead Statuses</h3>
        <ul><li>🆕 <strong>New</strong> — Just added</li>
        <li>📞 <strong>Contacted</strong> — First outreach made</li>
        <li>✅ <strong>Converted</strong> — Became a customer</li></ul>" .
        exercise('Add 3 test leads with different sources (Website, Referral, Scheduler). Then use the Pipeline board to move them through stages.') .
        takeaways(['Leads track potential customers from first contact', 'The Pipeline board is a visual drag-and-drop interface', 'Leads from the Scheduler are auto-created'])
    ],
],

// ===================== SECTION 10 =====================
'CRM — Opportunities, Sales & Integrations' => [
    ['title' => 'Opportunities & Recording Sales', 'content' =>
        objectives(['Create and track opportunities', 'Record sales with inventory integration', 'Understand the sales workflow']) .
        "<h2>Opportunities</h2>" . nav('/erp/crm/opportunities') .
        "<p>Track potential deals and their value. Create an opportunity by linking it to a lead or customer with a deal value.</p>
        <p>Stages: <strong>Prospecting → Qualification → Proposal → Negotiation → Won/Lost</strong></p>
        <h2>Recording Sales</h2>" . nav('/erp/crm/sales') .
        steps(['Click <strong>\"New Sale\"</strong>', 'Select a <strong>Customer</strong>',
            'Select an <strong>Inventory Item</strong>', 'Enter <strong>Quantity</strong>',
            'Total is auto-calculated from unit price × quantity',
            'Save — <strong>inventory stock is automatically decremented!</strong>']) .
        warn('Sales directly affect inventory stock levels. Make sure your inventory quantities are accurate before recording sales.') .
        "<h2>Integrations & Webhooks</h2>
        <p>At <strong>/erp/crm/integrations</strong>, connect external services. Generate embed codes and configure webhooks at <code>/api/erp/crm/webhook/{secret}</code>.</p>" .
        takeaways(['Opportunities track deal values through a pipeline', 'Sales auto-reduce inventory stock', 'Webhooks allow external service integration'])
    ],
],

// ===================== SECTION 11 =====================
'Inventory Management' => [
    ['title' => 'Managing Products & Stock', 'content' =>
        objectives(['Add products to inventory', 'Track stock quantities', 'Understand inventory-sales integration']) .
        nav('/erp/inventory') .
        "<h2>Inventory Tracking</h2>
        <p>Manage your products, services, or physical goods with SKU tracking and stock management.</p>
        <h3>Adding an Item</h3>" .
        steps(['Go to <strong>/erp/inventory</strong> → <strong>\"Add Item\"</strong>',
            'Enter <strong>SKU</strong> — a unique identifier (e.g. PROD-001)',
            'Enter <strong>Name</strong> — product or service name',
            'Set <strong>Unit Price</strong>', 'Set <strong>Stock Quantity</strong>',
            'Click <strong>Save</strong>']) .
        "<h3>Automatic Stock Updates</h3>
        <p>When you record a <strong>sale through CRM</strong>, the stock quantity for that item is <strong>automatically reduced</strong>. No manual updating needed!</p>
        <h3>Edit & Delete</h3>
        <p>Update pricing, SKUs, or stock levels anytime. Delete items you no longer carry.</p>" .
        exercise('Add 3 inventory items (e.g. \"Web Development Package\", \"Logo Design\", \"Hosting Plan\") with prices and stock quantities.') .
        takeaways(['Every product needs an SKU for identification', 'Stock is auto-decremented when sales are recorded', 'Keep inventory quantities accurate for reliable reporting'])
    ],
],

// ===================== SECTION 12 =====================
'Project Management' => [
    ['title' => 'Projects, Tasks & Kanban', 'content' =>
        objectives(['Create projects linked to clients', 'Manage tasks with assignments and priorities', 'Use the Kanban board for visual management']) .
        "<h2>Creating Projects</h2>" . nav('/erp/projects') .
        steps(['Click <strong>\"New Project\"</strong>', 'Enter <strong>Name</strong> and <strong>Description</strong>',
            'Link to a <strong>Client</strong> (CRM customer) — optional',
            'Save']) .
        "<h2>Task Management</h2>" . nav('/erp/tasks') .
        steps(['Click <strong>\"New Task\"</strong>', 'Select a <strong>Project</strong>',
            'Enter <strong>Title</strong> and <strong>Description</strong>',
            'Assign to an <strong>Employee</strong>',
            'Set <strong>Priority</strong> (low/medium/high/urgent) and <strong>Due Date</strong>',
            'Save']) .
        "<p><strong>Visibility:</strong> Admins see all tasks. Regular staff see only their assigned tasks.</p>
        <h2>Kanban Board</h2>" . nav('/erp/projects/kanban') .
        "<p>A visual board with 4 columns: <strong>To Do → In Progress → Review → Done</strong>. Move tasks between columns to update status. Filter by project.</p>" .
        tip('The Kanban board is perfect for daily standups. Pull it up in team meetings to review work in progress.') .
        takeaways(['Projects can be linked to CRM clients', 'Tasks have priorities, assignees, and due dates', 'The Kanban board provides visual project tracking'])
    ],
    ['title' => 'Calendar & Timesheets', 'content' =>
        objectives(['Use the calendar for task planning', 'Log time against projects', 'Track team productivity']) .
        "<h2>Calendar View</h2>" . nav('/erp/calendar') .
        "<p>A monthly calendar showing tasks by their due date. Navigate between months and see who's working on what. Great for <strong>resource planning</strong>.</p>
        <h2>Timesheets</h2>" . nav('/erp/projects/timesheets') .
        steps(['Click <strong>\"Log Time\"</strong>',
            'Select a <strong>Project</strong> and optionally a <strong>Task</strong>',
            'Select the <strong>Employee</strong>',
            'Enter what was worked on (<strong>Description</strong>)',
            'Set <strong>Start Time</strong> and <strong>End Time</strong>',
            'Save — duration is auto-calculated in minutes']) .
        exercise('Log time entries for a project: record 2 hours of work on a task. Check the timesheet log to verify the duration was correctly calculated.') .
        takeaways(['The calendar gives a monthly view of task due dates', 'Timesheets log hours against projects and tasks', 'Duration is auto-calculated from start and end times'])
    ],
],

// ===================== SECTION 13 =====================
'Scheduler, Client Portal & Self-Service' => [
    ['title' => 'Setting Up the Scheduler', 'content' =>
        objectives(['Configure your scheduling profile', 'Set availability and duration', 'Connect Google Calendar']) .
        nav('/erp/scheduler/settings') .
        "<h2>Your Own Booking Page — Like Calendly! 📅</h2>
        <p>The Scheduler lets you create a <strong>public booking page</strong> where anyone can schedule a meeting with you.</p>
        <h3>Setup Steps</h3>" .
        steps(['Go to <strong>/erp/scheduler/settings</strong>',
            'Set a <strong>Title</strong> (e.g. \"30-Minute Consultation\")',
            'Add a <strong>Description</strong>',
            'Set your <strong>Slug</strong> (URL identifier) — auto-generated from your name',
            'Choose <strong>Duration</strong> (meeting length in minutes)',
            'Set your <strong>Timezone</strong>',
            'Configure <strong>Availability</strong> — which days/hours you\'re free',
            'Toggle <strong>Active</strong> to enable', 'Click <strong>Save</strong>']) .
        "<h3>Google Calendar Integration</h3>
        <p>Click <strong>\"Connect Google Calendar\"</strong> to enable:</p>
        <ul><li>🔍 Auto-check your calendar for conflicts</li>
        <li>📅 Auto-create calendar events for bookings</li>
        <li>📹 Auto-generate <strong>Google Meet links</strong></li></ul>" .
        tip('Your public booking page is at <strong>/schedule/your-slug</strong>. Share this link on your website, email signature, or social media!') .
        takeaways(['The Scheduler creates Calendly-like booking pages', 'Google Calendar integration prevents double-bookings', 'Meet links are auto-generated for video calls'])
    ],
    ['title' => 'Public Booking & Auto-CRM', 'content' =>
        objectives(['Understand the booking flow for guests', 'Know what happens after a booking', 'Manage bookings as admin']) .
        "<h2>What Guests Experience</h2>
        <p>When someone visits your booking page:</p>" .
        steps(['They see a <strong>calendar</strong> with available dates',
            'They select a date and see <strong>available time slots</strong>',
            'They fill in their <strong>name, email, phone, and notes</strong>',
            'They click <strong>Book</strong>']) .
        "<h3>What Happens Automatically</h3>
        <ul><li>✅ Booking is confirmed and saved</li>
        <li>📧 <strong>Confirmation email</strong> sent with meeting details</li>
        <li>📅 <strong>\"Add to Google Calendar\"</strong> link included</li>
        <li>📹 <strong>Google Meet link</strong> included (if connected)</li>
        <li>🔗 <strong>Cancel link</strong> for the guest</li>
        <li>👤 <strong>CRM lead auto-created</strong> with source \"Scheduler\"</li></ul>" .
        warn('Every booking automatically creates a CRM lead! This means your scheduling page doubles as a lead generation tool.') .
        "<h3>Managing Bookings</h3>" . nav('/erp/scheduler/bookings') .
        "<p>View all bookings with filters (All, Upcoming, Cancelled). Cancel bookings from the admin panel.</p>" .
        takeaways(['Guests book without needing an account', 'Confirmation emails include Meet links and calendar invites', 'Every booking auto-creates a CRM lead'])
    ],
    ['title' => 'Client Portal & Employee Self-Service', 'content' =>
        objectives(['Understand the Client Portal', 'Use the Employee Self-Service Portal', 'Know what each portal provides']) .
        "<h2>Client Portal</h2>" . nav('/erp/client/dashboard') .
        "<p>Clients with portal logins see a dedicated dashboard with:</p>
        <ul><li>📊 <strong>Active project count</strong> and <strong>unpaid invoice count</strong></li>
        <li>📋 <strong>Their projects</strong> — with task details</li>
        <li>💰 <strong>Their invoices</strong> — view and payment status</li></ul>
        <h2>Employee Self-Service Portal</h2>" . nav('/erp/my-portal') .
        "<p>Employees managing their own HR from one page:</p>
        <ul><li>⏰ <strong>Check In / Check Out</strong> — one-click attendance</li>
        <li>📅 <strong>Leave Balance</strong> — 20 days/year minus approved leave</li>
        <li>💵 <strong>Recent Payslips</strong> — last 5 payroll records</li>
        <li>👤 <strong>Profile</strong> — view personal details</li>
        <li>🏖️ <strong>Request Leave</strong> — submit leave requests</li></ul>" .
        tip('Both portals reduce your admin workload by giving stakeholders self-service access to the information they need.') .
        "<h2>🎉 Congratulations!</h2>
        <p>You've completed the entire ERP Management Mastery course! You now know how to use every module in Casjoe BOS. <strong>Mark this lesson as complete to claim your certificate!</strong></p>" .
        takeaways(['The Client Portal shows projects and invoices', 'The Self-Service Portal lets employees manage attendance, leave, and payslips', 'Both portals reduce admin workload through self-service'])
    ],
],
]; // End of second half

// --- Insert Second Half ---
foreach ($curriculum2 as $sectionTitle => $lessons) {
    $insertSection->execute([$courseId, $sectionTitle, $sectionOrder]);
    $sectionId = $db->lastInsertId();
    echo "Section $sectionOrder: $sectionTitle<br>";

    $lessonOrder = 1;
    foreach ($lessons as $l) {
        $insertLesson->execute([$sectionId, $l['title'], $l['content'], $lessonOrder]);
        echo "  Lesson $lessonOrder: {$l['title']}<br>";
        $lessonOrder++;
    }
    $sectionOrder++;
}

// --- Create Final Quiz ---
echo "<br>Creating final quiz...<br>";
try {
    // Check/create quiz tables
    $db->exec("CREATE TABLE IF NOT EXISTS academy_quizzes (id INT AUTO_INCREMENT PRIMARY KEY, course_id INT NOT NULL, title VARCHAR(255), passing_score INT DEFAULT 70, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    $db->exec("CREATE TABLE IF NOT EXISTS academy_quiz_questions (id INT AUTO_INCREMENT PRIMARY KEY, quiz_id INT NOT NULL, question_text TEXT, options JSON, correct_option_index INT DEFAULT 0)");
    $db->exec("CREATE TABLE IF NOT EXISTS academy_quiz_attempts (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, quiz_id INT, score INT, passed TINYINT DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

    // Delete old quiz for this course
    $db->prepare("DELETE FROM academy_quizzes WHERE course_id = ?")->execute([$courseId]);

    $db->prepare("INSERT INTO academy_quizzes (course_id, title, passing_score) VALUES (?, ?, 70)")
       ->execute([$courseId, "ERP Management — Final Assessment"]);
    $quizId = $db->lastInsertId();

    $questions = [
        ["What does the ERP dashboard's Business Health Score measure?", ["Only revenue","Only staff count","Revenue, efficiency, staff, and financial health combined","Customer satisfaction"], 2],
        ["How many active employees are allowed on the free plan?", ["1","3","5","Unlimited"], 1],
        ["What happens when you record a sale in CRM?", ["Nothing changes","Inventory stock is auto-decremented","An email is sent to the CEO","The lead is deleted"], 1],
        ["What is the public invoice URL format?", ["/erp/invoice/{id}","/invoice/{uuid}","/pay/{code}","/billing/{id}"], 1],
        ["What can clients see in the Client Portal?", ["All company data","Only chat messages","Their projects and invoices","Employee salaries"], 2],
        ["Which role has full access to all ERP modules?", ["User","HR","Client","Admin"], 3],
        ["What does the Scheduler auto-create in CRM when someone books?", ["A customer record","A lead","An invoice","A project"], 1],
        ["How is Net Pay calculated in payroll?", ["Base + Bonus","Base - Tax","Gross - Tax - Deductions","Salary × 12"], 2],
        ["What are the Kanban board columns for tasks?", ["New, Old, Done","To Do, In Progress, Review, Done","Open, Closed","Draft, Active, Complete"], 1],
        ["What happens when a client accepts an estimate?", ["It's automatically paid","It can be converted to an invoice","The lead is deleted","Nothing happens"], 1],
        ["How many leave days do employees get per year by default?", ["10","15","20","30"], 2],
        ["What does the AI Manager Weekly Roast do?", ["Sends a joke email","Provides brutally honest business feedback","Deletes underperforming data","Creates new goals automatically"], 1],
        ["Where can employees check in/out themselves?", ["/erp/attendance","/erp/my-portal","/erp/hr","/erp/clock"], 1],
        ["What weight does Revenue have in the health score?", ["20%","25%","30%","50%"], 2],
        ["What gets auto-sent when you add a new customer with portal login?", ["An invoice","A welcome email with login credentials","A calendar invite","A project brief"], 1],
    ];

    $qStmt = $db->prepare("INSERT INTO academy_quiz_questions (quiz_id, question_text, options, correct_option_index) VALUES (?, ?, ?, ?)");
    foreach ($questions as $q) {
        $qStmt->execute([$quizId, $q[0], json_encode($q[1]), $q[2]]);
    }
    echo "Quiz created with " . count($questions) . " questions<br>";

} catch (Exception $e) {
    echo "Quiz creation note: " . $e->getMessage() . "<br>";
}

// --- Ensure completion/certificate tables exist ---
try {
    $db->exec("CREATE TABLE IF NOT EXISTS academy_lesson_completions (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, lesson_id INT, course_id INT, completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY uniq_completion (user_id, lesson_id))");
    $db->exec("CREATE TABLE IF NOT EXISTS academy_certificates (id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT NOT NULL, user_id INT NOT NULL, course_id INT NOT NULL, certificate_code VARCHAR(100) UNIQUE, issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
} catch (Exception $e) {
    // Tables likely already exist
}

// Count totals
$totalSections = $sectionOrder - 1;
$stmtCount = $db->prepare("SELECT COUNT(*) FROM academy_lessons l JOIN academy_sections s ON l.section_id = s.id WHERE s.course_id = ?");
$stmtCount->execute([$courseId]);
$totalLessons = $stmtCount->fetchColumn();

echo "<br>✅ <strong>Done!</strong> ERP course populated successfully.<br>";
echo "📚 Course ID: $courseId<br>";
echo "📂 Sections: $totalSections<br>";
echo "📝 Lessons: $totalLessons<br>";
echo "📋 Quiz: 15 questions (70% to pass)<br>";
echo "🎓 Certificate: Awarded on 100% completion<br>";
