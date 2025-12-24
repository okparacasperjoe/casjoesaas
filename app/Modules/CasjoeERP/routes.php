<?php

use App\Core\Router;
use App\Modules\CasjoeERP\Controllers\ErpController;
use App\Modules\CasjoeERP\Controllers\EmployeeController;
use App\Modules\CasjoeERP\Controllers\FinanceController;
use App\Modules\CasjoeERP\Controllers\DepartmentController;
use App\Modules\CasjoeERP\Controllers\LeaveController;
use App\Modules\CasjoeERP\Controllers\PayrollController;
use App\Modules\CasjoeERP\Controllers\PerformanceController;
use App\Modules\CasjoeERP\Controllers\RecruitmentController;
use App\Modules\CasjoeERP\Controllers\TrainingController;
use App\Modules\CasjoeERP\Controllers\LifecycleController;
use App\Modules\CasjoeERP\Controllers\CrmController;
use App\Modules\CasjoeERP\Controllers\InventoryController;
use App\Modules\CasjoeERP\Controllers\ProjectController;
use App\Modules\CasjoeERP\Controllers\SelfServiceController;
use App\Modules\CasjoeERP\Controllers\SystemController;

/**
 * ERP Module Routes
 * Prefix: /erp
 */

// --- Dashboard ---
Router::get('/erp', [ErpController::class, 'dashboard']); // Default to dashboard
Router::get('/erp/dashboard', [ErpController::class, 'dashboard']);

// --- Human Resources (HR) ---
// Employee Management
Router::get('/erp/employees', [EmployeeController::class, 'index']);
Router::get('/erp/employees/create', [EmployeeController::class, 'create']);
Router::post('/erp/employees/store', [EmployeeController::class, 'store']);
Router::post('/erp/employees/delete', [EmployeeController::class, 'delete']);
Router::get('/erp/employees/profile', [EmployeeController::class, 'profile']);
Router::get('/erp/employees/family', [EmployeeController::class, 'family']);
Router::get('/erp/employees/bank', [EmployeeController::class, 'bank']);
Router::get('/erp/employees/documents', [EmployeeController::class, 'documents']);
Router::get('/erp/employees/assets', [EmployeeController::class, 'assets']);
Router::get('/erp/hr', [EmployeeController::class, 'index']); // Alias

// Attendance
Router::get('/erp/attendance', [EmployeeController::class, 'attendance']);
Router::post('/erp/attendance/checkin', [EmployeeController::class, 'checkIn']);
Router::post('/erp/attendance/checkout', [EmployeeController::class, 'checkOut']);

// Departments & Designations
Router::get('/erp/departments', [DepartmentController::class, 'index']);
Router::post('/erp/departments/store', [DepartmentController::class, 'store']);
Router::get('/erp/designations', [DepartmentController::class, 'designations']);

// Leave Management
Router::get('/erp/leave', [LeaveController::class, 'index']);
Router::get('/erp/leave/create', [LeaveController::class, 'create']);
Router::post('/erp/leave/store', [LeaveController::class, 'store']);
Router::post('/erp/leave/approve', [LeaveController::class, 'approve']);
Router::post('/erp/leave/reject', [LeaveController::class, 'reject']);

// Payroll
Router::get('/erp/payroll', [PayrollController::class, 'index']);
Router::get('/erp/payroll/create', [PayrollController::class, 'create']);
Router::post('/erp/payroll/store', [PayrollController::class, 'store']);
Router::get('/erp/payroll/show', [PayrollController::class, 'show']);

// Performance
Router::get('/erp/performance', [PerformanceController::class, 'index']);
Router::get('/erp/performance/create', [PerformanceController::class, 'create']);
Router::post('/erp/performance/store', [PerformanceController::class, 'store']);

// Recruitment
Router::get('/erp/recruitment', [RecruitmentController::class, 'index']);
Router::post('/erp/recruitment/store', [RecruitmentController::class, 'storeJob']);
Router::get('/erp/recruitment/applications', [RecruitmentController::class, 'applications']);
Router::post('/erp/recruitment/apply', [RecruitmentController::class, 'storeApplication']);

// Training
Router::get('/erp/training', [TrainingController::class, 'index']);
Router::post('/erp/training/store', [TrainingController::class, 'storeProgram']);

// Employee Lifecycle
Router::get('/erp/lifecycle', [LifecycleController::class, 'index']);
Router::get('/erp/lifecycle/create', [LifecycleController::class, 'create']);
Router::post('/erp/lifecycle/store', [LifecycleController::class, 'store']);
Router::get('/erp/promotion', [ErpController::class, 'promotion']);
Router::get('/erp/resignation', [ErpController::class, 'resignation']);
Router::get('/erp/termination', [ErpController::class, 'termination']);

// --- Finance ---
Router::get('/erp/finance', [FinanceController::class, 'index']);
Router::get('/erp/finance/dashboard', [FinanceController::class, 'dashboard']);
// Transactions
Router::get('/erp/finance/transactions', [FinanceController::class, 'transactions']);
Router::get('/erp/transactions', [FinanceController::class, 'transactions']); // Alias
Router::get('/erp/transactions/create', [FinanceController::class, 'createTransaction']);
Router::post('/erp/transactions/store', [FinanceController::class, 'storeTransaction']);

Router::get('/erp/finance/invoices', [FinanceController::class, 'invoices']);
Router::get('/erp/finance/invoices/create', [FinanceController::class, 'createInvoice']);
Router::post('/erp/finance/invoices/store', [FinanceController::class, 'storeInvoice']);
Router::get('/erp/finance/invoice/view', [FinanceController::class, 'showInvoice']); // Internal view
Router::get('/erp/finance/reports', [FinanceController::class, 'reports']);
Router::get('/erp/finance/settings', [FinanceController::class, 'settings']);

// Public Invoice Routes
Router::get('/invoice/{uuid}', [FinanceController::class, 'publicInvoice']);
Router::post('/invoice/{uuid}/pay', [FinanceController::class, 'processPayment']);

// Estimates
Router::get('/erp/finance/estimates', [FinanceController::class, 'estimates']);
Router::get('/erp/finance/estimates/create', [FinanceController::class, 'createEstimate']);
Router::post('/erp/finance/estimates/store', [FinanceController::class, 'storeEstimate']);
Router::get('/estimate/{uuid}', [FinanceController::class, 'publicEstimate']);
Router::post('/estimate/{uuid}/accept', [FinanceController::class, 'acceptEstimate']);
Router::post('/estimate/{uuid}/reject', [FinanceController::class, 'rejectEstimate']);
Router::post('/estimate/{uuid}/convert', [FinanceController::class, 'convertEstimate']);

// Expenses
Router::get('/erp/finance/expenses', [FinanceController::class, 'expenses']);
Router::get('/erp/finance/expenses/create', [FinanceController::class, 'createExpense']);
Router::post('/erp/finance/expenses/store', [FinanceController::class, 'storeExpense']);

// Vendors
Router::get('/erp/finance/vendors', [FinanceController::class, 'vendors']);
Router::post('/erp/finance/vendors/store', [FinanceController::class, 'storeVendor']);

// Inventory
Router::get('/erp/inventory', [InventoryController::class, 'index']);
Router::post('/erp/inventory/store', [InventoryController::class, 'store']);

Router::get('/erp/benefits', [ErpController::class, 'benefits']);

// --- CRM ---
Router::get('/erp/crm/pipeline', [CrmController::class, 'pipeline']);
Router::post('/erp/crm/pipeline/update', [CrmController::class, 'updateStage']);

// Default to customers
Router::get('/erp/crm', [CrmController::class, 'customers']);
Router::get('/erp/crm/customers', [CrmController::class, 'customers']);
Router::post('/erp/crm/customers/store', [CrmController::class, 'storeCustomer']);
Router::get('/erp/crm/leads', [CrmController::class, 'leads']);
Router::post('/erp/crm/leads/store', [CrmController::class, 'storeLead']);

// Opportunities & Sales
Router::get('/erp/crm/opportunities', [CrmController::class, 'opportunities']);
Router::post('/erp/crm/opportunities/store', [CrmController::class, 'storeOpportunity']);
Router::get('/erp/crm/sales', [CrmController::class, 'sales']);
Router::post('/erp/crm/sales/store', [CrmController::class, 'storeSale']);

// --- Project Management ---
Router::get('/erp/projects', [ProjectController::class, 'projects']);
Router::get('/erp/projects/create', [ProjectController::class, 'createProject']);
Router::post('/erp/projects/store', [ProjectController::class, 'storeProject']);
Router::get('/erp/calendar', [ProjectController::class, 'calendar']);
Router::get('/erp/tasks', [ProjectController::class, 'tasks']);
Router::get('/erp/tasks/create', [ProjectController::class, 'createTask']);
Router::post('/erp/tasks/store', [ProjectController::class, 'storeTask']);

Router::get('/erp/projects/timesheets', [ProjectController::class, 'timesheets']);
Router::post('/erp/projects/timesheets/log', [ProjectController::class, 'logTime']);
Router::get('/erp/my-portal', [SelfServiceController::class, 'dashboard']);

// --- Client Portal ---
use App\Modules\CasjoeERP\Controllers\ClientPortalController;
Router::get('/erp/client/dashboard', [ClientPortalController::class, 'dashboard']);
Router::get('/erp/client/projects', [ClientPortalController::class, 'projects']);
Router::get('/erp/client/projects/view', [ClientPortalController::class, 'projectDetails']);
Router::get('/erp/client/invoices', [ClientPortalController::class, 'invoices']);

// --- System & Settings ---
Router::get('/erp/settings', [SystemController::class, 'settings']);
Router::post('/erp/settings/update', [SystemController::class, 'updateSettings']);
Router::get('/erp/roles', [SystemController::class, 'roles']);
Router::get('/erp/permissions', [SystemController::class, 'permissions']);
Router::get('/erp/announcements', [SystemController::class, 'announcements']);
Router::get('/erp/announcements/create', [SystemController::class, 'createAnnouncement']);
Router::post('/erp/announcements/store', [SystemController::class, 'storeAnnouncement']);
Router::get('/erp/reports', [SystemController::class, 'reports']);
Router::get('/erp/audit_log', [SystemController::class, 'audit_log']);
Router::get('/erp/support', [SystemController::class, 'support']);
Router::get('/erp/company', [SystemController::class, 'company']);
Router::get('/erp/activity', [SystemController::class, 'activity']);

// --- General / Legacy / Utilities ---
Router::get('/erp/assets', [ErpController::class, 'assets']); 
Router::get('/erp/notifications', [ErpController::class, 'notifications']);
Router::get('/erp/users', [ErpController::class, 'users']);
Router::get('/erp/chat', [\App\Modules\CasjoeERP\Controllers\ChatController::class, 'index']);
Router::post('/erp/chat/send', [\App\Modules\CasjoeERP\Controllers\ChatController::class, 'send']);
Router::get('/erp/chat/poll', [\App\Modules\CasjoeERP\Controllers\ChatController::class, 'poll']);
Router::post('/erp/chat/subscribe', [\App\Modules\CasjoeERP\Controllers\ChatController::class, 'subscribe']);

Router::get('/erp/leads', [ErpController::class, 'leads']);
Router::get('/erp/opportunities', [ErpController::class, 'opportunities']);
Router::get('/erp/sales', [ErpController::class, 'sales']);
Router::get('/erp/tickets', [ErpController::class, 'tickets']);

// API
Router::get('/erp/api/stats', [ErpController::class, 'stats']);
