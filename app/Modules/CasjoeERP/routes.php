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

use App\Modules\CasjoeERP\Controllers\IntegrationController;
use App\Modules\CasjoeERP\Controllers\SchedulerController;
use App\Modules\CasjoeERP\Controllers\GoalController;
use App\Modules\CasjoeERP\Controllers\AIManagerController;
use App\Modules\CasjoeERP\Controllers\WhatsAppController;
use App\Modules\CasjoeERP\Controllers\WalletController;
use App\Modules\CasjoeERP\Controllers\SopController;
use App\Modules\CasjoeERP\Controllers\LocationController;

/**
 * ERP Module Routes
 * Prefix: /erp
 */

// --- Dashboard ---
Router::get('/erp', [ErpController::class, 'dashboard']); // Default to dashboard
Router::get('/erp/dashboard', [ErpController::class, 'dashboard']);

// --- Goals ---
Router::get('/erp/goals', [GoalController::class, 'index']);
Router::get('/erp/goals/create', [GoalController::class, 'create']);
Router::post('/erp/goals/store', [GoalController::class, 'store']);
Router::get('/erp/goals/edit', [GoalController::class, 'edit']);
Router::post('/erp/goals/update', [GoalController::class, 'update']);
Router::post('/erp/goals/delete', [GoalController::class, 'delete']);

// --- AI Manager ---
Router::get('/erp/ai-manager', [AIManagerController::class, 'dashboard']);
Router::post('/erp/ai-manager/settings/save', [AIManagerController::class, 'saveSettings']);
Router::post('/erp/ai-manager/lead/research/{id}', [AIManagerController::class, 'researchLead']);
Router::post('/erp/ai-manager/email/generate/{id}', [AIManagerController::class, 'generateFollowUpEmail']);
Router::get('/erp/ai-manager/stress-test/analytics', [AIManagerController::class, 'stressTestAnalytics']);
Router::get('/erp/ai-manager/stress-test/staff', [AIManagerController::class, 'stressTestStaff']);
Router::get('/erp/ai-manager/stress-test/admin', [AIManagerController::class, 'stressTestAdmin']);
Router::get('/erp/ai-manager/stress-test/hr', [AIManagerController::class, 'stressTestHr']);
Router::get('/erp/ai-manager/insight/read/{id}', [AIManagerController::class, 'markInsightRead']);
Router::get('/erp/ai-manager/insights/dismiss', [AIManagerController::class, 'dismissAllInsights']);
Router::post('/erp/ai-manager/chat', [AIManagerController::class, 'chat']);
Router::post('/erp/ai-manager/execute-action', [AIManagerController::class, 'executeAction']);

// --- WhatsApp ---
Router::get('/erp/whatsapp', [WhatsAppController::class, 'index']);
Router::post('/erp/whatsapp/logout', [WhatsAppController::class, 'logout']);

// --- Wallet ---
Router::get('/erp/wallet', [WalletController::class, 'index']);
Router::post('/erp/wallet/withdraw', [WalletController::class, 'withdraw']);
Router::get('/erp/wallet/migrate', [WalletController::class, 'migrate']);

// --- Human Resources (HR) ---
// Employee Management
Router::get('/erp/employees', [EmployeeController::class, 'index']);
Router::get('/erp/employees/create', [EmployeeController::class, 'create']);
Router::post('/erp/employees/store', [EmployeeController::class, 'store']);
Router::get('/erp/employees/edit', [EmployeeController::class, 'edit']);
Router::post('/erp/employees/update', [EmployeeController::class, 'update']);
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
Router::get('/erp/departments/create', [DepartmentController::class, 'create']);
Router::post('/erp/departments/store', [DepartmentController::class, 'store']);
Router::post('/erp/departments/update', [DepartmentController::class, 'update']);
Router::post('/erp/departments/delete', [DepartmentController::class, 'delete']);
Router::get('/erp/designations', [DepartmentController::class, 'designations']);

// Leave Management
Router::get('/erp/leave', [LeaveController::class, 'index']);
Router::get('/erp/leave/create', [LeaveController::class, 'create']);
Router::post('/erp/leave/store', [LeaveController::class, 'store']);
Router::post('/erp/leave/approve', [LeaveController::class, 'approve']);
Router::post('/erp/leave/reject', [LeaveController::class, 'reject']);
Router::post('/erp/leave/delete', [LeaveController::class, 'delete']);

// Payroll
Router::get('/erp/payroll', [PayrollController::class, 'index']);
Router::get('/erp/payroll/create', [PayrollController::class, 'create']);
Router::post('/erp/payroll/store', [PayrollController::class, 'store']);
Router::post('/erp/payroll/delete', [PayrollController::class, 'delete']);
Router::get('/erp/payroll/show', [PayrollController::class, 'show']);

// Performance
Router::get('/erp/performance', [PerformanceController::class, 'index']);
Router::get('/erp/performance/create', [PerformanceController::class, 'create']);
Router::post('/erp/performance/store', [PerformanceController::class, 'store']);
Router::post('/erp/performance/delete', [PerformanceController::class, 'delete']);

// Recruitment
Router::get('/erp/recruitment', [RecruitmentController::class, 'index']);
Router::post('/erp/recruitment/store', [RecruitmentController::class, 'storeJob']);
Router::post('/erp/recruitment/delete', [RecruitmentController::class, 'deleteJob']);
Router::get('/erp/recruitment/applications', [RecruitmentController::class, 'applications']);
Router::post('/erp/recruitment/apply', [RecruitmentController::class, 'storeApplication']);
Router::post('/erp/recruitment/applications/delete', [RecruitmentController::class, 'deleteApplication']);

// Training
Router::get('/erp/training', [TrainingController::class, 'index']);
Router::post('/erp/training/store', [TrainingController::class, 'storeProgram']);
Router::post('/erp/training/delete', [TrainingController::class, 'deleteProgram']);

// Employee Lifecycle
Router::get('/erp/lifecycle', [LifecycleController::class, 'index']);
Router::get('/erp/lifecycle/promotion', [LifecycleController::class, 'index']);
Router::get('/erp/lifecycle/resignation', [LifecycleController::class, 'index']);
Router::get('/erp/lifecycle/termination', [LifecycleController::class, 'index']);
Router::get('/erp/lifecycle/create', [LifecycleController::class, 'create']);
Router::post('/erp/lifecycle/store', [LifecycleController::class, 'store']);
Router::post('/erp/lifecycle/delete', [LifecycleController::class, 'delete']);

// Integrations
Router::get('/erp/crm/integrations', [IntegrationController::class, 'index']);
Router::get('/erp/crm/integrations/create', [IntegrationController::class, 'create']);
Router::post('/erp/crm/integrations/store', [IntegrationController::class, 'store']);
Router::get('/erp/crm/integrations/edit', [IntegrationController::class, 'edit']);
Router::post('/erp/crm/integrations/update', [IntegrationController::class, 'update']);
Router::post('/erp/crm/integrations/delete', [IntegrationController::class, 'delete']);
Router::get('/erp/crm/integrations/embed', [IntegrationController::class, 'embed']);

// Scheduler (Admin)
Router::get('/erp/scheduler', [SchedulerController::class, 'index']);
Router::get('/erp/scheduler/settings', [SchedulerController::class, 'settings']);
Router::post('/erp/scheduler/settings/save', [SchedulerController::class, 'saveSettings']);
Router::get('/erp/scheduler/bookings', [SchedulerController::class, 'bookings']);
Router::post('/erp/scheduler/bookings/cancel', [SchedulerController::class, 'cancelBooking']);
Router::get('/erp/scheduler/google/auth', [SchedulerController::class, 'googleAuth']);
Router::get('/erp/scheduler/google/callback', [SchedulerController::class, 'googleCallback']);
Router::post('/erp/scheduler/google/disconnect', [SchedulerController::class, 'googleDisconnect']);

// Scheduler (Public)
Router::get('/book/{slug}', [SchedulerController::class, 'publicPage']);
Router::get('/book/{slug}/slots', [SchedulerController::class, 'getAvailableSlots']);
Router::post('/book/{slug}/submit', [SchedulerController::class, 'submitBooking']);
Router::post('/book/{slug}/book', [SchedulerController::class, 'submitBooking']);
Router::get('/book/{slug}/cancel', [SchedulerController::class, 'publicCancel']);
Router::post('/book/{slug}/cancel', [SchedulerController::class, 'publicCancel']);

// Scheduler Aliases (Guarantee zero 404 whether /book or /schedule is used)
Router::get('/schedule/{slug}', [SchedulerController::class, 'publicPage']);
Router::get('/schedule/{slug}/slots', [SchedulerController::class, 'getAvailableSlots']);
Router::post('/schedule/{slug}/submit', [SchedulerController::class, 'submitBooking']);
Router::post('/schedule/{slug}/book', [SchedulerController::class, 'submitBooking']);
Router::get('/schedule/{slug}/cancel', [SchedulerController::class, 'publicCancel']);
Router::post('/schedule/{slug}/cancel', [SchedulerController::class, 'publicCancel']);

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
Router::get('/erp/transactions/edit', [FinanceController::class, 'editTransaction']);
Router::post('/erp/transactions/update', [FinanceController::class, 'updateTransaction']);
Router::post('/erp/transactions/delete', [FinanceController::class, 'deleteTransaction']);

Router::get('/erp/finance/invoices', [FinanceController::class, 'invoices']);
Router::get('/erp/finance/invoices/create', [FinanceController::class, 'createInvoice']);
Router::post('/erp/finance/invoices/store', [FinanceController::class, 'storeInvoice']);
Router::get('/erp/finance/invoices/edit', [FinanceController::class, 'editInvoice']);
Router::post('/erp/finance/invoices/update', [FinanceController::class, 'updateInvoice']);
Router::post('/erp/finance/invoices/delete', [FinanceController::class, 'deleteInvoice']);
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
Router::get('/erp/finance/estimates/edit', [FinanceController::class, 'editEstimate']);
Router::post('/erp/finance/estimates/update', [FinanceController::class, 'updateEstimate']);
Router::post('/erp/finance/estimates/delete', [FinanceController::class, 'deleteEstimate']);
Router::get('/estimate/{uuid}', [FinanceController::class, 'publicEstimate']);
Router::post('/estimate/{uuid}/accept', [FinanceController::class, 'acceptEstimate']);
Router::post('/estimate/{uuid}/reject', [FinanceController::class, 'rejectEstimate']);
Router::post('/estimate/{uuid}/convert', [FinanceController::class, 'convertEstimate']);

// Expenses
Router::get('/erp/finance/expenses', [FinanceController::class, 'expenses']);
Router::get('/erp/finance/expenses/create', [FinanceController::class, 'createExpense']);
Router::post('/erp/finance/expenses/store', [FinanceController::class, 'storeExpense']);
Router::get('/erp/finance/expenses/edit', [FinanceController::class, 'editExpense']);
Router::post('/erp/finance/expenses/update', [FinanceController::class, 'updateExpense']);
Router::post('/erp/finance/expenses/delete', [FinanceController::class, 'deleteExpense']);
// Expenses Aliases (Guarantee zero 404 whether /erp/finance or /erp/accounting is accessed)
Router::get('/erp/accounting/expenses', [FinanceController::class, 'expenses']);
Router::get('/erp/accounting', [FinanceController::class, 'index']);

// Vendors
Router::get('/erp/finance/vendors', [FinanceController::class, 'vendors']);
Router::post('/erp/finance/vendors/store', [FinanceController::class, 'storeVendor']);
Router::get('/erp/finance/vendors/edit', [FinanceController::class, 'editVendor']);
Router::post('/erp/finance/vendors/update', [FinanceController::class, 'updateVendor']);
Router::post('/erp/finance/vendors/delete', [FinanceController::class, 'deleteVendor']);

// Inventory
Router::get('/erp/inventory', [InventoryController::class, 'index']);
Router::post('/erp/inventory/store', [InventoryController::class, 'store']);
Router::get('/erp/inventory/edit', [InventoryController::class, 'edit']);
Router::post('/erp/inventory/update', [InventoryController::class, 'update']);
Router::post('/erp/inventory/delete', [InventoryController::class, 'delete']);

Router::get('/erp/benefits', [ErpController::class, 'benefits']);

// --- CRM ---
Router::get('/erp/crm/pipeline', [CrmController::class, 'pipeline']);
Router::post('/erp/crm/pipeline/update', [CrmController::class, 'updateStage']);

// Default to customers
Router::get('/erp/crm', [CrmController::class, 'customers']);
Router::get('/erp/crm/customers', [CrmController::class, 'customers']);
Router::post('/erp/crm/customers/store', [CrmController::class, 'storeCustomer']);
Router::post('/erp/crm/customers/update', [CrmController::class, 'updateCustomer']);
Router::post('/erp/crm/customers/delete', [CrmController::class, 'deleteCustomer']);

Router::get('/erp/crm/leads', [CrmController::class, 'leads']);
Router::post('/erp/crm/leads/store', [CrmController::class, 'storeLead']);
Router::post('/erp/crm/leads/update', [CrmController::class, 'updateLead']);
Router::post('/erp/crm/leads/delete', [CrmController::class, 'deleteLead']);

// Opportunities & Sales
Router::get('/erp/crm/opportunities', [CrmController::class, 'opportunities']);
Router::post('/erp/crm/opportunities/store', [CrmController::class, 'storeOpportunity']);
Router::post('/erp/crm/opportunities/update', [CrmController::class, 'updateOpportunity']);
Router::post('/erp/crm/opportunities/delete', [CrmController::class, 'deleteOpportunity']);

Router::get('/erp/crm/sales', [CrmController::class, 'sales']);
Router::post('/erp/crm/sales/store', [CrmController::class, 'storeSale']);
Router::post('/erp/crm/sales/update', [CrmController::class, 'updateSale']);
Router::post('/erp/crm/sales/delete', [CrmController::class, 'deleteSale']);

// --- Project Management ---
Router::get('/erp/projects', [ProjectController::class, 'projects']);
Router::get('/erp/projects/create', [ProjectController::class, 'createProject']);
Router::post('/erp/projects/store', [ProjectController::class, 'storeProject']);
Router::post('/erp/projects/update', [ProjectController::class, 'updateProject']);
Router::post('/erp/projects/delete', [ProjectController::class, 'deleteProject']);
Router::get('/erp/calendar', [ProjectController::class, 'calendar']);
Router::get('/erp/tasks', [ProjectController::class, 'tasks']);
Router::get('/erp/tasks/create', [ProjectController::class, 'createTask']);
Router::post('/erp/tasks/store', [ProjectController::class, 'storeTask']);
Router::post('/erp/tasks/update', [ProjectController::class, 'updateTask']);
Router::post('/erp/tasks/delete', [ProjectController::class, 'deleteTask']);

Router::get('/erp/projects/timesheets', [ProjectController::class, 'timesheets']);
Router::post('/erp/projects/timesheets/log', [ProjectController::class, 'logTime']);

// --- Employee Self-Service Portal ---
Router::get('/erp/my-portal', [SelfServiceController::class, 'dashboard']);
Router::get('/erp/my-portal/checkin', [SelfServiceController::class, 'checkIn']);
Router::post('/erp/my-portal/checkin', [SelfServiceController::class, 'checkIn']);
Router::get('/erp/my-portal/checkout', [SelfServiceController::class, 'checkOut']);
Router::post('/erp/my-portal/checkout', [SelfServiceController::class, 'checkOut']);
Router::get('/erp/my-portal/request-leave', [SelfServiceController::class, 'requestLeave']);
Router::get('/erp/my-portal/profile', [SelfServiceController::class, 'profile']);
Router::post('/erp/my-portal/profile/update', [SelfServiceController::class, 'updateProfile']);

// --- Client Portal ---
use App\Modules\CasjoeERP\Controllers\ClientPortalController;
Router::get('/erp/client/dashboard', [ClientPortalController::class, 'dashboard']);
Router::get('/erp/client/projects', [ClientPortalController::class, 'projects']);
Router::get('/erp/client/projects/view', [ClientPortalController::class, 'projectDetails']);
Router::get('/erp/client/invoices', [ClientPortalController::class, 'invoices']);

// --- System & Settings ---
Router::get('/erp/settings', [SystemController::class, 'settings']);
Router::post('/erp/settings/update', [SystemController::class, 'updateSettings']);
Router::post('/erp/settings/api-key/generate', [SystemController::class, 'generateApiKey']);
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
Router::get('/erp/users', [SystemController::class, 'users']);
Router::post('/erp/users/invite', [SystemController::class, 'inviteUser']);
Router::post('/erp/users/update', [SystemController::class, 'updateUserRole']);
Router::post('/erp/users/delete', [SystemController::class, 'deleteUser']);
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

// --- SOPs ---
Router::get('/erp/sops', [SopController::class, 'index']);
Router::get('/erp/sops/create', [SopController::class, 'create']);
Router::post('/erp/sops/store', [SopController::class, 'store']);
Router::get('/erp/sops/edit', [SopController::class, 'edit']);
Router::post('/erp/sops/update', [SopController::class, 'update']);
Router::post('/erp/sops/delete', [SopController::class, 'delete']);
Router::get('/erp/sops/assign', [SopController::class, 'assign']);
Router::post('/erp/sops/assign/store', [SopController::class, 'storeAssignment']);
Router::get('/erp/sops/tracking', [SopController::class, 'tracking']);

// --- Locations ---
Router::get('/erp/locations', [LocationController::class, 'index']);
Router::get('/erp/locations/create', [LocationController::class, 'create']);
Router::post('/erp/locations/store', [LocationController::class, 'store']);
Router::get('/erp/locations/edit', [LocationController::class, 'edit']);
Router::post('/erp/locations/update', [LocationController::class, 'update']);
Router::post('/erp/locations/delete', [LocationController::class, 'delete']);
Router::post('/erp/locations/assign-staff', [LocationController::class, 'assignStaff']);

// --- AI Sales & CRM Automation Helpers ---
Router::post('/erp/crm/leads/ai-email/{id}', [\App\Modules\CasjoeERP\Controllers\AiEmailComposerController::class, 'generateEmail']);
Router::post('/erp/crm/leads/ai-proposal/{id}', [\App\Modules\CasjoeERP\Controllers\ProposalController::class, 'generateAiProposal']);
Router::post('/api/crm/webhook/{tenant_id}', [\App\Modules\CasjoeERP\Controllers\CrmWebhookController::class, 'captureLead']);

// --- AI External Agent API Sync ---
Router::get('/api/v1/erp/sync', [\App\Modules\CasjoeERP\Controllers\ApiSyncController::class, 'syncSummary']);
Router::get('/api/v1/erp/crm/leads', [\App\Modules\CasjoeERP\Controllers\ApiSyncController::class, 'getLeads']);
Router::get('/api/v1/erp/crm/customers', [\App\Modules\CasjoeERP\Controllers\ApiSyncController::class, 'getCustomers']);

// --- Finance Transactions CSV / Excel Bulk Import ---
Router::get('/erp/transactions/import', [FinanceController::class, 'importTransactionsView']);
Router::get('/erp/finance/transactions/import', [FinanceController::class, 'importTransactionsView']);
Router::post('/erp/transactions/import', [FinanceController::class, 'importTransactions']);
Router::post('/erp/finance/transactions/import', [FinanceController::class, 'importTransactions']);
Router::get('/erp/transactions/sample-csv', [FinanceController::class, 'downloadSampleTransactionsCsv']);
Router::post('/erp/transactions/clean-duplicates', [FinanceController::class, 'cleanDuplicateTransactions']);

// --- Office Fixed Assets Management ---
Router::get('/erp/assets/create', [FinanceController::class, 'createAsset']);
Router::post('/erp/assets/store', [FinanceController::class, 'storeAsset']);
Router::get('/erp/assets/edit', [FinanceController::class, 'editAsset']);
Router::post('/erp/assets/update', [FinanceController::class, 'updateAsset']);
Router::post('/erp/assets/delete', [FinanceController::class, 'deleteAsset']);

// --- Transactions Bulk Delete & Clear All ---
Router::post('/erp/transactions/delete-all', [FinanceController::class, 'deleteAllTransactions']);
Router::post('/erp/finance/transactions/delete-all', [FinanceController::class, 'deleteAllTransactions']);
Router::post('/erp/transactions/bulk-delete', [FinanceController::class, 'bulkDeleteTransactions']);
Router::post('/erp/finance/transactions/bulk-delete', [FinanceController::class, 'bulkDeleteTransactions']);

// --- E-Sign & Document Signing Module ---
Router::get('/erp/documents', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'index']);
Router::get('/erp/documents/create', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'create']);
Router::post('/erp/documents/store', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'store']);
Router::get('/erp/documents/show', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'show']);
Router::get('/erp/documents/edit', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'edit']);
Router::post('/erp/documents/update', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'update']);
Router::post('/erp/documents/delete', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'delete']);
Router::get('/erp/documents/print', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'printDocument']);

// Public E-Sign Routes
Router::get('/sign/{uuid}', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'publicSignView']);
Router::post('/sign/{uuid}/submit', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'publicSubmitSignature']);
Router::post('/sign/{uuid}/decline', [\App\Modules\CasjoeERP\Controllers\DocumentSigningController::class, 'publicDeclineSignature']);



// Workflow Automations
Router::get('/erp/crm/workflows', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'index']);
Router::get('/erp/crm/workflows/create', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'create']);
Router::post('/erp/crm/workflows/store', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'store']);
Router::get('/erp/crm/workflows/edit', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'edit']);
Router::post('/erp/crm/workflows/update', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'update']);
Router::post('/erp/crm/workflows/toggle', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'toggle']);
Router::post('/erp/crm/workflows/delete', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'delete']);
Router::get('/erp/crm/workflows/logs', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'logs']);

// Additional Workflow Routes
Router::post('/erp/crm/workflows/save', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'save']);
Router::get('/erp/crm/workflows/edit/{id}', [\App\Modules\CasjoeERP\Controllers\WorkflowController::class, 'editWithId']);

// Client Management Routes
Router::get('/erp/clients', [\App\Modules\CasjoeERP\Controllers\CrmController::class, 'clients']);
Router::get('/erp/clients/create', [\App\Modules\CasjoeERP\Controllers\CrmController::class, 'createClient']);
Router::post('/erp/clients/store', [\App\Modules\CasjoeERP\Controllers\CrmController::class, 'storeClient']);
Router::post('/erp/leads/store', [\App\Modules\CasjoeERP\Controllers\CrmController::class, 'storeLead']);

// Finance Reminders & Accounts & Journal
Router::get('/erp/finance/reminders', [\App\Modules\CasjoeERP\Controllers\FinanceController::class, 'reminders']);
Router::get('/erp/finance/reminders/create', [\App\Modules\CasjoeERP\Controllers\FinanceController::class, 'createReminder']);
Router::post('/erp/finance/reminders/store', [\App\Modules\CasjoeERP\Controllers\FinanceController::class, 'storeReminder']);
Router::get('/erp/finance/reminders/edit', [\App\Modules\CasjoeERP\Controllers\FinanceController::class, 'editReminder']);
Router::post('/erp/finance/reminders/update', [\App\Modules\CasjoeERP\Controllers\FinanceController::class, 'updateReminder']);
Router::post('/erp/finance/reminders/delete', [\App\Modules\CasjoeERP\Controllers\FinanceController::class, 'deleteReminder']);
Router::post('/erp/finance/transactions/update', [\App\Modules\CasjoeERP\Controllers\FinanceController::class, 'updateTransaction']);
Router::post('/erp/finance/account/store', [\App\Modules\CasjoeERP\Controllers\FinanceController::class, 'storeAccount']);
Router::post('/erp/finance/journal/store', [\App\Modules\CasjoeERP\Controllers\FinanceController::class, 'storeJournalEntry']);

// Attendance Report Routes
Router::get('/erp/attendance/report', [\App\Modules\CasjoeERP\Controllers\EmployeeController::class, 'attendanceReport']);
Router::post('/erp/attendance/report', [\App\Modules\CasjoeERP\Controllers\EmployeeController::class, 'attendanceReport']);

// Employee Self-Service SOPs
Router::get('/erp/my-portal/sops', [\App\Modules\CasjoeERP\Controllers\SelfServiceController::class, 'sops']);
Router::get('/erp/my-portal/sops/{id}/read', [\App\Modules\CasjoeERP\Controllers\SelfServiceController::class, 'readSop']);
Router::get('/erp/my-portal/sops/read', [\App\Modules\CasjoeERP\Controllers\SelfServiceController::class, 'readSop']);
Router::post('/erp/my-portal/sops/sign', [\App\Modules\CasjoeERP\Controllers\SelfServiceController::class, 'signSop']);

// HR Payroll Runs & Aliases
Router::post('/erp/hr/payroll/run', [\App\Modules\CasjoeERP\Controllers\PayrollController::class, 'run']);
Router::get('/erp/hr/payroll', [\App\Modules\CasjoeERP\Controllers\PayrollController::class, 'index']);
Router::get('/erp/hr/payroll/view', [\App\Modules\CasjoeERP\Controllers\PayrollController::class, 'show']);

// Project Kanban Task Status
Router::post('/erp/task/update-status', [\App\Modules\CasjoeERP\Controllers\ProjectController::class, 'updateStatus']);

// Scheduler Endpoints
Router::post('/erp/scheduler/cancel', [\App\Modules\CasjoeERP\Controllers\SchedulerController::class, 'cancelBooking']);
Router::get('/erp/scheduler/google/disconnect', [\App\Modules\CasjoeERP\Controllers\SchedulerController::class, 'googleDisconnect']);

// AI CRM Lead Intelligence
Router::post('/api/crm/lead/{id}/research', [\App\Modules\CasjoeERP\Controllers\AIManagerController::class, 'researchLead']);
Router::get('/api/crm/lead/{id}/generate-proposal', [\App\Modules\CasjoeERP\Controllers\AIManagerController::class, 'generateProposal']);
Router::post('/api/crm/lead/{id}/generate-email', [\App\Modules\CasjoeERP\Controllers\AIManagerController::class, 'generateLeadEmail']);

// Zero-404 Route Protection Aliases: CRM Clients
Router::get('/erp/crm/clients', [\App\Modules\CasjoeERP\Controllers\CrmController::class, 'clients']);
Router::get('/erp/crm/clients/create', [\App\Modules\CasjoeERP\Controllers\CrmController::class, 'createClient']);
Router::post('/erp/crm/clients/store', [\App\Modules\CasjoeERP\Controllers\CrmController::class, 'storeClient']);

// Zero-404 Route Protection Aliases: HR Employees
Router::get('/erp/hr/employees', [EmployeeController::class, 'index']);
Router::get('/erp/hr/employees/create', [EmployeeController::class, 'create']);
Router::post('/erp/hr/employees/store', [EmployeeController::class, 'store']);
Router::get('/erp/hr/employees/edit', [EmployeeController::class, 'edit']);
Router::post('/erp/hr/employees/update', [EmployeeController::class, 'update']);
Router::post('/erp/hr/employees/delete', [EmployeeController::class, 'delete']);
Router::get('/erp/hr/employees/profile', [EmployeeController::class, 'profile']);
Router::get('/erp/hr/employees/family', [EmployeeController::class, 'family']);
Router::get('/erp/hr/employees/bank', [EmployeeController::class, 'bank']);
Router::get('/erp/hr/employees/documents', [EmployeeController::class, 'documents']);
Router::get('/erp/hr/employees/assets', [EmployeeController::class, 'assets']);

// Zero-404 Route Protection Aliases: HR Departments & Designations
Router::get('/erp/hr/departments', [DepartmentController::class, 'index']);
Router::get('/erp/hr/departments/create', [DepartmentController::class, 'create']);
Router::get('/erp/hr/departments/designations', [DepartmentController::class, 'designations']);
Router::get('/erp/hr/designations', [DepartmentController::class, 'designations']);

// Zero-404 Route Protection Aliases: Self-Service SOPs
Router::get('/erp/hr/self_service/sops', [\App\Modules\CasjoeERP\Controllers\SelfServiceController::class, 'sops']);
Router::get('/erp/hr/self-service/sops', [\App\Modules\CasjoeERP\Controllers\SelfServiceController::class, 'sops']);
Router::get('/erp/self-service/sops', [\App\Modules\CasjoeERP\Controllers\SelfServiceController::class, 'sops']);
Router::get('/erp/self_service/sops', [\App\Modules\CasjoeERP\Controllers\SelfServiceController::class, 'sops']);

// Zero-404 Route Protection Aliases: Payroll Run
Router::get('/erp/hr/payroll/run', [\App\Modules\CasjoeERP\Controllers\PayrollController::class, 'run']);
Router::get('/erp/payroll/run', [\App\Modules\CasjoeERP\Controllers\PayrollController::class, 'run']);


// Moniepoint POS Integration
Router::get('/casjoe-erp/settings/moniepoint', [\App\Modules\CasjoeERP\Controllers\MoniepointIntegrationController::class, 'settings']);
Router::post('/casjoe-erp/settings/moniepoint', [\App\Modules\CasjoeERP\Controllers\MoniepointIntegrationController::class, 'saveSettings']);
Router::post('/api/erp/moniepoint/push', [\App\Modules\CasjoeERP\Controllers\MoniepointIntegrationController::class, 'pushPayment']);

