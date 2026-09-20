<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class ProjectController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function projects()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_projects WHERE tenant_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->tenantId]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/projects/index.php';
    }

    public function createProject()
    {
        $stmt = $this->pdo->prepare("SELECT id, name FROM erp_crm_customers WHERE tenant_id = ? ORDER BY name ASC");
        $stmt->execute([$this->tenantId]);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/projects/create.php';
    }

    public function storeProject()
    {
        $name = $_POST['name'];
        $desc = $_POST['description'];
        $clientId = !empty($_POST['client_id']) ? $_POST['client_id'] : null;
        
        $stmt = $this->pdo->prepare("INSERT INTO erp_projects (tenant_id, name, description, client_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $name, $desc, $clientId]);

        header('Location: /erp/projects');
        exit;
    }

    public function tasks()
    {
        $stmt = $this->pdo->prepare("
            SELECT t.*, p.name as project_name 
            FROM erp_tasks t 
            LEFT JOIN erp_projects p ON t.project_id = p.id 
            WHERE t.tenant_id = ? 
            ORDER BY t.due_date ASC
        ");
        $stmt->execute([$this->tenantId]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/projects/tasks.php';
    }

    public function createTask()
    {
        // Get projects for dropdown
        $stmt = $this->pdo->prepare("SELECT id, name FROM erp_projects WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get employees for assignment dropdown
        $stmt = $this->pdo->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM erp_employees WHERE tenant_id = ?");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/projects/create_task.php';
    }

    public function storeTask()
    {
        $projectId = $_POST['project_id'];
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $assignedTo = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;
        $priority = $_POST['priority'];
        $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        $stmt = $this->pdo->prepare("INSERT INTO erp_tasks (tenant_id, project_id, title, description, assigned_to, priority, due_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $projectId, $title, $desc, $assignedTo, $priority, $dueDate]);

        if ($assignedTo) {
            $empStmt = $this->pdo->prepare("SELECT email, first_name, last_name FROM erp_employees WHERE id = ?");
            $empStmt->execute([$assignedTo]);
            $emp = $empStmt->fetch(PDO::FETCH_ASSOC);

            if ($emp && !empty($emp['email'])) {
                $subject = "New Task Assigned: " . $title;
                $message = "<p>Hello {$emp['first_name']},</p>";
                $message .= "<p>A new task has been assigned to you:</p>";
                $message .= "<ul>";
                $message .= "<li><strong>Task:</strong> " . htmlspecialchars($title) . "</li>";
                $message .= "<li><strong>Priority:</strong> " . htmlspecialchars($priority) . "</li>";
                if ($dueDate) {
                    $message .= "<li><strong>Due Date:</strong> " . htmlspecialchars($dueDate) . "</li>";
                }
                $message .= "</ul>";
                $message .= "<p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($desc)) . "</p>";

                try {
                    \App\Core\Mailer::send($emp['email'], $subject, $message, false);
                } catch (\Exception $e) {
                    error_log("Task assignment email failed: " . $e->getMessage());
                }
            }
        }

        header('Location: /erp/tasks');
        exit;
    }
    public function calendar()
    {
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        // Validate
        if (!checkdate($month, 1, $year)) {
            $month = date('m');
            $year = date('Y');
        }

        // Calculation
        $firstDayTimestamp = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDayTimestamp);
        $startDayOfWeek = date('w', $firstDayTimestamp); // 0 (Sun) - 6 (Sat)
        $monthName = date('F', $firstDayTimestamp);

        // Fetch tasks for this month
        $startDate = "$year-$month-01";
        $endDate = "$year-$month-$daysInMonth";

        $stmt = $this->pdo->prepare("
            SELECT t.*, p.name as project_name 
            FROM erp_tasks t 
            LEFT JOIN erp_projects p ON t.project_id = p.id 
            WHERE t.tenant_id = ? 
            AND t.due_date BETWEEN ? AND ?
            ORDER BY t.due_date ASC
        ");
        $stmt->execute([$this->tenantId, $startDate, $endDate]);
        $tasksAll = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group tasks by day
        $tasksByDay = [];
        foreach ($tasksAll as $t) {
            $day = (int)date('d', strtotime($t['due_date']));
            $tasksByDay[$day][] = $t;
        }

        // Nav Links
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
        
        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

        require __DIR__ . '/../Views/projects/calendar.php';
    }
    public function timesheets()
    {
        // Get logs
        $stmt = $this->pdo->prepare("
            SELECT l.*, p.name as project_name, t.title as task_title, CONCAT(e.first_name, ' ', e.last_name) as employee_name 
            FROM erp_project_time_logs l
            LEFT JOIN erp_projects p ON l.project_id = p.id
            LEFT JOIN erp_tasks t ON l.task_id = t.id
            LEFT JOIN erp_employees e ON l.employee_id = e.id
            WHERE l.tenant_id = ? 
            ORDER BY l.start_time DESC
        ");
        $stmt->execute([$this->tenantId]);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Data for dropdowns
        $stmtProj = $this->pdo->prepare("SELECT id, name FROM erp_projects WHERE tenant_id = ?");
        $stmtProj->execute([$this->tenantId]);
        $projects = $stmtProj->fetchAll(PDO::FETCH_ASSOC);

        // Assume current logged in user (in a real app, from session. Here we just list all employees for demo)
        $stmtEmp = $this->pdo->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM erp_employees WHERE tenant_id = ?");
        $stmtEmp->execute([$this->tenantId]);
        $employees = $stmtEmp->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/projects/timesheets.php';
    }

    public function logTime()
    {
        $projectId = !empty($_POST['project_id']) ? $_POST['project_id'] : null;
        $taskId = !empty($_POST['task_id']) ? $_POST['task_id'] : null;
        $employeeId = $_POST['employee_id'];
        $desc = $_POST['description'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        
        // Calculate duration in minutes
        $start = new \DateTime($startTime);
        $end = new \DateTime($endTime);
        $diff = $start->diff($end);
        $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

        $stmt = $this->pdo->prepare("INSERT INTO erp_project_time_logs (tenant_id, project_id, task_id, employee_id, description, start_time, end_time, duration_minutes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$this->tenantId, $projectId, $taskId, $employeeId, $desc, $startTime, $endTime, $minutes]);

        header('Location: /erp/projects/timesheets');
        exit;
    }


    // ==========================================
    // PROJECTS (Edit/Update/Delete)
    // ==========================================
    public function updateProject() {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $client_id = $_POST['client_id'] ?: null;
        $status = $_POST['status'];
        $stmt = $this->pdo->prepare("UPDATE erp_projects SET name=?, description=?, client_id=?, status=? WHERE id=? AND tenant_id=?");
        $stmt->execute([$name, $description, $client_id, $status, $id, $this->tenantId]);
        header('Location: /erp/projects');
        exit;
    }

    public function deleteProject() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_projects WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/projects');
        exit;
    }

    // ==========================================
    // TASKS (Edit/Update/Delete)
    // ==========================================
    public function updateTask() {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $project_id = $_POST['project_id'] ?: null;
        $assigned_to = $_POST['assigned_to'] ?: null;
        $due_date = $_POST['due_date'] ?: null;
        $status = $_POST['status'];
        $priority = $_POST['priority'];
        $stmt = $this->pdo->prepare("UPDATE erp_tasks SET title=?, project_id=?, assigned_to=?, due_date=?, status=?, priority=? WHERE id=? AND tenant_id=?");
        $stmt->execute([$title, $project_id, $assigned_to, $due_date, $status, $priority, $id, $this->tenantId]);
        header('Location: /erp/tasks');
        exit;
    }

    public function deleteTask() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_tasks WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/tasks');
        exit;
    }

    public function updateStatus()
    {
        $taskId = (int)($_POST['task_id'] ?? ($_POST['id'] ?? 0));
        $status = $_POST['status'] ?? 'pending';

        if ($taskId > 0) {
            $stmt = $this->pdo->prepare("UPDATE erp_tasks SET status = ? WHERE id = ? AND tenant_id = ?");
            $stmt->execute([$status, $taskId, $this->tenantId]);
        }

        $redirect = $_SERVER['HTTP_REFERER'] ?? '/erp/tasks';
        header("Location: {$redirect}");
        exit;
    }
}
