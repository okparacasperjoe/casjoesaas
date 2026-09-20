<?php

namespace App\Modules\CasjoeSmartForms\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class FormController
{
    public function index()
    {
        // List all forms
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        
        $stmt = $db->query("SELECT * FROM smart_forms WHERE tenant_id = ? ORDER BY created_at DESC", [$tenantId]);
        $forms = $stmt->fetchAll();

        // Dashboard Analytics
        $totalForms = count($forms);
        $totalSubmissions = 0;
        $totalViews = 0;

        // Simple aggregation (in a real app, do this via SQL count)
        $formIds = array_column($forms, 'id');
        if (!empty($formIds)) {
            $ids = implode(',', $formIds);
            $stmt = $db->query("SELECT COUNT(*) as cnt FROM smart_form_submissions WHERE form_id IN ($ids)");
            $totalSubmissions = $stmt->fetch()['cnt'] ?? 0;

            $stmt = $db->query("SELECT COUNT(*) as cnt FROM smart_form_analytics WHERE form_id IN ($ids) AND event_type='view'");
            $totalViews = $stmt->fetch()['cnt'] ?? 0;
        }

        require_once __DIR__ . '/../Views/index.php';
    }

    public function templates()
    {
        // Define preset templates
        $templates = $this->getTemplates();
        require_once __DIR__ . '/../Views/templates.php';
    }

    public function useTemplate($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        $templates = $this->getTemplates();
        $template = null;
        foreach ($templates as $t) {
            if ($t['id'] === $id) {
                $template = $t;
                break;
            }
        }

        if (!$template) {
            header("Location: /smart-forms");
            exit;
        }

        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        $userId = $_SESSION['user_id'] ?? 1;

        $db->query("INSERT INTO smart_forms (tenant_id, user_id, title, description, structure, settings) VALUES (?, ?, ?, ?, ?, ?)", 
            [$tenantId, $userId, $template['title'], $template['description'], json_encode($template['structure']), json_encode($template['settings'])]);

        $formId = $db->lastInsertId();
        header("Location: /smart-forms/edit/$formId");
        exit;
    }

    private function getTemplates()
    {
        return [
            [
                'id' => 'contact_us',
                'title' => 'Contact Us',
                'description' => 'A simple form for users to get in touch with you.',
                'structure' => [
                    ['type' => 'text', 'name' => 'f_name', 'label' => 'Full Name', 'required' => true, 'placeholder' => 'John Doe'],
                    ['type' => 'email', 'name' => 'f_email', 'label' => 'Email Address', 'required' => true, 'placeholder' => 'john@example.com'],
                    ['type' => 'textarea', 'name' => 'f_msg', 'label' => 'Message', 'required' => true, 'placeholder' => 'How can we help?']
                ],
                'settings' => ['form_mode' => 'classic', 'submit_text' => 'Send Message']
            ],
            [
                'id' => 'event_rsvp',
                'title' => 'Event RSVP',
                'description' => 'Collect attendees for your upcoming event.',
                'structure' => [
                    ['type' => 'text', 'name' => 'f_name', 'label' => 'Your Name', 'required' => true],
                    ['type' => 'email', 'name' => 'f_email', 'label' => 'Email Address', 'required' => true],
                    ['type' => 'radio', 'name' => 'f_attend', 'label' => 'Will you attend?', 'required' => true, 'options' => 'Yes, No'],
                    ['type' => 'number', 'name' => 'f_guests', 'label' => 'Number of guests (+1s)', 'required' => false, 'placeholder' => '0']
                ],
                'settings' => ['form_mode' => 'conversational', 'submit_text' => 'Confirm RSVP']
            ],
            [
                'id' => 'job_application',
                'title' => 'Job Application',
                'description' => 'Gather resumes and cover letters from applicants.',
                'structure' => [
                    ['type' => 'text', 'name' => 'f_name', 'label' => 'Full Name', 'required' => true],
                    ['type' => 'email', 'name' => 'f_email', 'label' => 'Email Address', 'required' => true],
                    ['type' => 'phone', 'name' => 'f_phone', 'label' => 'Phone Number', 'required' => true],
                    ['type' => 'file', 'name' => 'f_resume', 'label' => 'Upload Resume (PDF/Doc)', 'required' => true],
                    ['type' => 'textarea', 'name' => 'f_cover', 'label' => 'Cover Letter', 'required' => false]
                ],
                'settings' => ['form_mode' => 'classic', 'submit_text' => 'Submit Application']
            ],
            [
                'id' => 'customer_feedback',
                'title' => 'Customer Feedback',
                'description' => 'Find out what your customers think of your product.',
                'structure' => [
                    ['type' => 'rating', 'name' => 'f_rate', 'label' => 'How would you rate your experience?', 'required' => true],
                    ['type' => 'textarea', 'name' => 'f_improve', 'label' => 'What could we improve?', 'required' => false],
                    ['type' => 'radio', 'name' => 'f_recommend', 'label' => 'Would you recommend us to a friend?', 'options' => 'Yes, No, Maybe']
                ],
                'settings' => ['form_mode' => 'conversational', 'submit_text' => 'Submit Feedback']
            ]
        ];
    }

    public function create()
    {
        // Initialize empty form for the view
        $form = [
            'title' => 'Untitled Form',
            'description' => '',
            'logo' => '',
            'structure' => '[]',
            'settings' => '{}',
            'status' => 'active'
        ];
        require_once __DIR__ . '/../Views/create.php';
    }

    public function store()
    {
        // Save new form
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        // Placeholder user ID (should get from session)
        $userId = $_SESSION['user_id'] ?? 1; 

        $title = $_POST['title'] ?? 'Untitled Form';
        $description = $_POST['description'] ?? '';
        $structure = $_POST['structure'] ?? '[]'; // JSON string
        $settings = $_POST['settings'] ?? '{}';
        
        // Handle logo upload - use CasjoeCloud storage
        $logo = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $cloudUploadDir = $_SERVER['DOCUMENT_ROOT'] . '/../storage/tenants/' . $tenantId . '/cloud';
            if (!is_dir($cloudUploadDir)) {
                mkdir($cloudUploadDir, 0777, true);
            }
            
            $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $filename = 'form_logo_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $targetPath = $cloudUploadDir . '/' . $filename;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                // Store in CasjoeCloud
                $publicUrl = '/cloud/asset/' . $tenantId . '/' . $filename;
                $stmt = $db->prepare("INSERT INTO cloud_files (tenant_id, folder_id, name, path, type, size_bytes) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$tenantId, null, $_FILES['logo']['name'], $publicUrl, $_FILES['logo']['type'], $_FILES['logo']['size']]);
                $logo = $publicUrl;
            }
        }

        $db->query("INSERT INTO smart_forms (tenant_id, user_id, title, description, logo, structure, settings) VALUES (?, ?, ?, ?, ?, ?, ?)", 
            [$tenantId, $userId, $title, $description, $logo, $structure, $settings]);

        $formId = $db->lastInsertId();
        header("Location: /smart-forms?saved=$formId");
        exit;
    }

    public function edit($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        // Show builder with existing data
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        $stmt = $db->query("SELECT * FROM smart_forms WHERE id = ? AND tenant_id = ?", [$id, $tenantId]);
        $form = $stmt->fetch();
        if (!$form) {
            header("Location: /smart-forms?error=FormNotFound");
            exit;
        }

        require_once __DIR__ . '/../Views/create.php';
    }

    public function update($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        
        $title = $_POST['title'];
        $description = $_POST['description'] ?? '';
        $structure = $_POST['structure'];
        $settings = $_POST['settings'] ?? '{}';
        
        // Handle logo upload - use CasjoeCloud storage
        $logo = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $cloudUploadDir = $_SERVER['DOCUMENT_ROOT'] . '/../storage/tenants/' . $tenantId . '/cloud';
            if (!is_dir($cloudUploadDir)) {
                mkdir($cloudUploadDir, 0777, true);
            }
            
            $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $filename = 'form_logo_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $targetPath = $cloudUploadDir . '/' . $filename;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                // Store in CasjoeCloud
                $publicUrl = '/cloud/asset/' . $tenantId . '/' . $filename;
                $stmt = $db->prepare("INSERT INTO cloud_files (tenant_id, folder_id, name, path, type, size_bytes) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$tenantId, null, $_FILES['logo']['name'], $publicUrl, $_FILES['logo']['type'], $_FILES['logo']['size']]);
                $logo = $publicUrl;
            }
        }
        
        // Update query with or without logo
        if ($logo) {
            $db->query("UPDATE smart_forms SET title = ?, description = ?, logo = ?, structure = ?, settings = ? WHERE id = ? AND tenant_id = ? AND user_id = ?", 
                [$title, $description, $logo, $structure, $settings, $id, $tenantId, $_SESSION['user_id'] ?? 0]);
        } else {
            $db->query("UPDATE smart_forms SET title = ?, description = ?, structure = ?, settings = ? WHERE id = ? AND tenant_id = ? AND user_id = ?", 
                [$title, $description, $structure, $settings, $id, $tenantId, $_SESSION['user_id'] ?? 0]);
        }
            
        header("Location: /smart-forms?saved=$id");
        exit;
    }
    
    public function delete($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        
        $db->query("DELETE FROM smart_forms WHERE id = ? AND tenant_id = ? AND user_id = ?", [$id, $tenantId, $_SESSION['user_id'] ?? 0]);
        
        header("Location: /smart-forms");
        exit;
    }
    
    public function toggle($id)
    {
        header('Content-Type: application/json');
        $id = is_array($id) ? $id['id'] : $id;
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $newStatus = $input['status'] ?? 'inactive';
        
        if (!in_array($newStatus, ['active', 'inactive'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            exit;
        }
        
        $db->query("UPDATE smart_forms SET status = ? WHERE id = ? AND tenant_id = ? AND user_id = ?", 
            [$newStatus, $id, $tenantId, $_SESSION['user_id'] ?? 0]);
        
        echo json_encode(['success' => true, 'status' => $newStatus]);
        exit;
    }

    public function stats($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // Get Form Info
        $stmt = $db->query("SELECT * FROM smart_forms WHERE id = ? AND tenant_id = ?", [$id, $tenantId]);
        $form = $stmt->fetch();
        if (!$form) die("Form not found");

        if (!$form) die("Form not found");

        $settings = json_decode($form['settings'] ?? '{}', true);

        // --- 1. Top KPI Strip ---
        // Views
        $stmt = $db->query("SELECT COUNT(*) as count FROM smart_form_analytics WHERE form_id = ? AND event_type = 'view'", [$id]);
        $views = $stmt->fetch()['count'];

        // Starts
        $stmt = $db->query("SELECT COUNT(*) as count FROM smart_form_analytics WHERE form_id = ? AND event_type = 'start'", [$id]);
        $starts = $stmt->fetch()['count'];

        // Submissions
        $stmt = $db->query("SELECT COUNT(*) as count FROM smart_form_submissions WHERE form_id = ?", [$id]);
        $submissions = $stmt->fetch()['count'];

        // Payments
        $stmt = $db->query("SELECT COUNT(*) as count FROM smart_form_submissions WHERE form_id = ? AND payment_status = 'paid'", [$id]);
        $payments = $stmt->fetch()['count'];

        $kpi = [
            'views' => $views,
            'starts' => $starts,
            'submissions' => $submissions,
            'payments' => $payments
        ];

        // --- 2. Conversion Funnel ---
        $funnel = [
            'views' => ['count' => $views, 'drop_off' => 0],
            'starts' => ['count' => $starts, 'drop_off' => $views > 0 ? round((($views - $starts) / $views) * 100) : 0],
            'submitted' => ['count' => $submissions, 'drop_off' => $starts > 0 ? round((($starts - $submissions) / $starts) * 100) : 0],
            'paid' => ['count' => $payments, 'drop_off' => $submissions > 0 ? round((($submissions - $payments) / $submissions) * 100) : 0]
        ];

        // --- 3. Biggest Problem Alert & Field Analysis ---
        // Basic Logic: Find where most people are stuck.
        $alert = "No major drop-off detected";
        if ($starts > 0 && $submissions == 0) {
            $alert = "Users are starting but not finishing. Check your form length.";
        }
        
        // Field interactions
        $stmt = $db->query("
            SELECT JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.field')) as field_name, COUNT(*) as count 
            FROM smart_form_analytics 
            WHERE form_id = ? AND event_type = 'interaction'
            GROUP BY field_name
            ORDER BY count DESC
        ", [$id]);
        $fieldInteractions = $stmt->fetchAll();
        
        if (count($fieldInteractions) > 0 && $submissions < $starts) {
            $mostDroppedField = $fieldInteractions[0]['field_name'];
            if ($mostDroppedField) {
                $alert = "High drop-off detected after interacting with field: <strong>" . htmlspecialchars($mostDroppedField) . "</strong>";
            }
        }
        
        // --- 4. Traffic Quality ---
        // Device
        $stmt = $db->query("SELECT device_type, COUNT(*) as count FROM smart_form_analytics WHERE form_id = ? AND event_type = 'view' GROUP BY device_type", [$id]);
        $deviceStats = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        $totalDevices = array_sum($deviceStats);
        $devices = [
            'mobile' => $totalDevices > 0 && isset($deviceStats['mobile']) ? round(($deviceStats['mobile'] / $totalDevices) * 100) : 0,
            'desktop' => $totalDevices > 0 && isset($deviceStats['desktop']) ? round(($deviceStats['desktop'] / $totalDevices) * 100) : 0
        ];

        // --- 5. Revenue Snapshot ---
        $revenue = 0;
        if (!empty($settings['payment_enabled'])) {
            $stmt = $db->query("SELECT SUM(amount) as total FROM smart_form_submissions WHERE form_id = ? AND payment_status = 'paid'", [$id]);
            $revenue = $stmt->fetch()['total'] ?? 0;
        }

        // --- 6. Recent Activity ---
        $stmt = $db->query("
            SELECT 'submission' as type, created_at, data as details FROM smart_form_submissions WHERE form_id = ? 
            UNION 
            SELECT 'view' as type, created_at, concat('IP: ', ip_address) as details FROM smart_form_analytics WHERE form_id = ? AND event_type='view'
            ORDER BY created_at DESC LIMIT 5
        ", [$id, $id]);
        $activity = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/stats.php';
    }

    public function export($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // 1. Verify Ownership
        $stmt = $db->query("SELECT title, structure, user_id FROM smart_forms WHERE id = ? AND tenant_id = ?", [$id, $tenantId]);
        $form = $stmt->fetch();
        if (!$form || $form['user_id'] != ($_SESSION['user_id'] ?? 0)) die("Form not found or access denied.");

        // 2. Fetch Submissions
        $stmt = $db->query("SELECT created_at, ip_address, data, payment_status, amount FROM smart_form_submissions WHERE form_id = ? ORDER BY created_at DESC", [$id]);
        $submissions = $stmt->fetchAll();

        if (empty($submissions)) die("No submissions to export.");

        // 3. Prepare CSV Headers
        $structure = json_decode($form['structure'], true);
        $headers = ['Date', 'IP Address', 'Payment Status', 'Amount'];
        
        $fieldMap = []; // map name => label
        foreach ($structure as $field) {
            if ($field['type'] === 'content') continue;
            $headers[] = $field['label'] ?: $field['name'];
            $fieldMap[$field['name']] = $field['label'] ?: $field['name'];
        }

        // 4. Output CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . preg_replace('/[^a-zA-Z0-9_-]/', '', $form['title']) . '_Responses.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers, ',', '"', '\\');

        foreach ($submissions as $sub) {
            $data = json_decode($sub['data'], true) ?? [];
            $row = [
                $sub['created_at'],
                $sub['ip_address'],
                $sub['payment_status'] ?? 'N/A',
                $sub['amount'] ?? 0
            ];

            // Add dynamic field data
            foreach ($fieldMap as $name => $label) {
                // Flatten arrays (like checkboxes)
                $val = $data[$name] ?? '';
                if (is_array($val)) $val = implode(', ', $val);
                $row[] = $val;
            }
            fputcsv($output, $row, ',', '"', '\\');
        }
        fclose($output);
        exit;
    }

    public function responses($id)
    {
        $id = is_array($id) ? $id['id'] : $id;
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // 1. Verify Ownership & Get Form
        $stmt = $db->query("SELECT * FROM smart_forms WHERE id = ? AND tenant_id = ?", [$id, $tenantId]);
        $form = $stmt->fetch();
        if (!$form) die("Form not found or access denied.");

        // 2. Fetch Submissions
        $stmt = $db->query("SELECT * FROM smart_form_submissions WHERE form_id = ? ORDER BY created_at DESC", [$id]);
        $submissions = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/responses.php';
    }
}

