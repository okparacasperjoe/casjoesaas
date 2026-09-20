<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use PDO;

class PayrollController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function index()
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, e.first_name, e.last_name 
            FROM erp_payroll p
            JOIN erp_employees e ON p.employee_id = e.id
            WHERE p.tenant_id = ?
            ORDER BY p.payment_date DESC
        ");
        $stmt->execute([$this->tenantId]);
        $payrolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/payroll/index.php';
    }

    public function create()
    {
        // Get employees
        $stmt = $this->pdo->prepare("SELECT id, first_name, last_name, salary FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/payroll/create.php';
    }

    public function store()
    {
        $employeeId = $_POST['employee_id'];
        $startDate = $_POST['pay_period_start'];
        $endDate = $_POST['pay_period_end'];
        $paymentDate = $_POST['payment_date'];
        
        $baseEarning = (float)$_POST['base_earning'];
        $bonus = (float)($_POST['bonus'] ?? 0);
        $tax = (float)($_POST['tax'] ?? 0);
        $deductions = (float)($_POST['other_deductions'] ?? 0);

        $gross = $baseEarning + $bonus;
        $net = $gross - $tax - $deductions;

        // Start Transaction
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO erp_payroll 
                (tenant_id, employee_id, pay_period_start, pay_period_end, payment_date, gross_pay, net_pay, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'paid')
            ");
            $stmt->execute([$this->tenantId, $employeeId, $startDate, $endDate, $paymentDate, $gross, $net]);
            $payrollId = $this->pdo->lastInsertId();

            // Insert Items
            $insertItem = $this->pdo->prepare("INSERT INTO erp_payroll_items (payroll_id, type, description, amount) VALUES (?, ?, ?, ?)");
            
            $insertItem->execute([$payrollId, 'earning', 'Base Salary', $baseEarning]);
            if ($bonus > 0) {
                $insertItem->execute([$payrollId, 'earning', 'Bonus', $bonus]);
            }
            if ($tax > 0) {
                $insertItem->execute([$payrollId, 'deduction', 'Tax', $tax]);
            }
            if ($deductions > 0) {
                $insertItem->execute([$payrollId, 'deduction', 'Other Deductions', $deductions]);
            }

            $this->pdo->commit();

            // Notify Employee
            $empStmt = $this->pdo->prepare("SELECT email, first_name, last_name FROM erp_employees WHERE id = ?");
            $empStmt->execute([$employeeId]);
            $emp = $empStmt->fetch(PDO::FETCH_ASSOC);

            if ($emp && !empty($emp['email'])) {
                $subject = "Payslip Generated";
                $message = "<p>Hello {$emp['first_name']},</p>";
                $message .= "<p>Your payslip for the period <strong>{$startDate}</strong> to <strong>{$endDate}</strong> has been generated.</p>";
                $message .= "<p><strong>Net Pay:</strong> " . number_format($net, 2) . "</p>";
                $message .= "<p>You can view the full details in your Employee Portal.</p>";
                try {
                    \App\Core\Mailer::send($emp['email'], $subject, $message, false);
                } catch (\Exception $e) {
                    error_log("Payslip email failed: " . $e->getMessage());
                }
            }

            header('Location: /erp/payroll');
            exit;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            echo "Error processing payroll: " . $e->getMessage();
        }
    }

    public function show()
    {
        $id = $_GET['id'];
        
        // Get Payroll
        $stmt = $this->pdo->prepare("
            SELECT p.*, e.first_name, e.last_name, e.job_title, e.email
            FROM erp_payroll p
            JOIN erp_employees e ON p.employee_id = e.id
            WHERE p.id = ? AND p.tenant_id = ?
        ");
        $stmt->execute([$id, $this->tenantId]);
        $payroll = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payroll) {
            die("Payslip not found");
        }

        // Get Items
        $stmt = $this->pdo->prepare("SELECT * FROM erp_payroll_items WHERE payroll_id = ?");
        $stmt->execute([$id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/hr/payroll/show.php';
    }


    public function delete() {
        $id = $_POST['id'];
        $stmt = $this->pdo->prepare("DELETE FROM erp_hr_payroll WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        header('Location: /erp/hr/payroll');
        exit;
    }

    public function run()
    {
        $month = $_POST['month'] ?? date('F');
        $year = (int)($_POST['year'] ?? date('Y'));

        // Run payroll for all active employees
        $stmt = $this->pdo->prepare("SELECT * FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
        $stmt->execute([$this->tenantId]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $startDate = date('Y-m-01', strtotime("{$month} {$year}"));
        $endDate = date('Y-m-t', strtotime("{$month} {$year}"));
        $paymentDate = date('Y-m-d');

        foreach ($employees as $emp) {
            $salary = (float)($emp['salary'] ?? 0);
            if ($salary <= 0) continue;
            $tax = round($salary * 0.05, 2);
            $net = $salary - $tax;

            $insert = $this->pdo->prepare("
                INSERT INTO erp_payroll (tenant_id, employee_id, pay_period_start, pay_period_end, payment_date, gross_pay, net_pay, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'paid')
            ");
            $insert->execute([$this->tenantId, $emp['id'], $startDate, $endDate, $paymentDate, $salary, $net]);
        }

        header('Location: /erp/payroll?success=Payroll generated');
        exit;
    }
}
