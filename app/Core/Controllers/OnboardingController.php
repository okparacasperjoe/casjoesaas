<?php

namespace App\Core\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\TenantContext;

class OnboardingController
{
    private $db;
    private $tenantId;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();
    }

    private function render($view, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../Views/onboarding/' . $view . '.php';
        // Need $viewPath variable inside layout.php
        $step = $this->getCurrentStep(); 
        require __DIR__ . '/../Views/onboarding/layout.php';
    }

    private function getCurrentStep()
    {
        $stmt = $this->db->query("SELECT onboarding_step FROM tenants WHERE id = ?", [$this->tenantId]);
        return (int) $stmt->fetchColumn();
    }

    private function updateStep($step)
    {
        $this->db->query("UPDATE tenants SET onboarding_step = ? WHERE id = ?", [$step, $this->tenantId]);
    }

    public function index()
    {
        $step = $this->getCurrentStep();
        if ($step >= 8) {
            header('Location: /dashboard');
            exit;
        }
        $method = 'step' . $step;
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            // Fallback
            $this->step1();
        }
    }

    // STEP 1: WELCOME
    public function step1()
    {
        $this->render('step1');
    }

    public function postStep1()
    {
        // Just advance
        $this->updateStep(2);
        header('Location: /onboarding');
        exit;
    }

    // STEP 2: IDENTITY & LOCALIZATION
    public function step2()
    {
        $this->ensureOnboardingColumns();
        $stmt = $this->db->query("SELECT * FROM tenants WHERE id = ?", [$this->tenantId]);
        $tenant = $stmt->fetch();
        $this->render('step2', ['tenant' => $tenant]);
    }

    private function ensureOnboardingColumns()
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $pdo = $this->db->getConnection();
        try { $pdo->exec("ALTER TABLE tenants ADD COLUMN logo VARCHAR(255) NULL"); } catch (\Exception $e) {}
        try { $pdo->exec("ALTER TABLE tenants ADD COLUMN country VARCHAR(100) DEFAULT 'Nigeria'"); } catch (\Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN country VARCHAR(100) DEFAULT 'Nigeria'"); } catch (\Exception $e) {}
    }

    public function postStep2()
    {
        $this->ensureOnboardingColumns();

        $name = trim($_POST['business_name'] ?? '');
        $type = $_POST['business_type'] ?? 'sme';
        $country = trim($_POST['country'] ?? 'Nigeria');
        $currency = trim($_POST['currency'] ?? 'NGN');

        // Handle Business Logo Upload
        $logoPath = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../../public/uploads/logos/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                $filename = 'logo_' . $this->tenantId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $filename)) {
                    $logoPath = '/uploads/logos/' . $filename;
                }
            }
        }

        // Update Tenant
        if ($logoPath) {
            $this->db->query("UPDATE tenants SET name = ?, business_type = ?, country = ?, currency = ?, logo = ?, onboarding_step = 3 WHERE id = ?", 
                [$name, $type, $country, $currency, $logoPath, $this->tenantId]);
        } else {
            $this->db->query("UPDATE tenants SET name = ?, business_type = ?, country = ?, currency = ?, onboarding_step = 3 WHERE id = ?", 
                [$name, $type, $country, $currency, $this->tenantId]);
        }
        
        // Update User Profile & Session
        $userId = $_SESSION['user_id'];
        $this->db->query("UPDATE users SET business_name = ?, country = ?, currency = ? WHERE id = ?", [$name, $country, $currency, $userId]);
        $_SESSION['currency'] = $currency;
        $_SESSION['country'] = $country;

        header('Location: /onboarding');
        exit;
    }

    // STEP 3: INTENT
    public function step3()
    {
        $this->render('step3');
    }

    public function postStep3()
    {
        $intents = $_POST['intent'] ?? [];
        $json = json_encode($intents);
        
        $this->db->query("UPDATE tenants SET onboarding_intent = ?, onboarding_step = 4 WHERE id = ?", 
            [$json, $this->tenantId]);
        
        header('Location: /onboarding');
        exit;
    }

    // STEP 4: WALLET (Casjoe Pay)
    public function step4()
    {
        $stmt = $this->db->query("SELECT * FROM tenants WHERE id = ?", [$this->tenantId]);
        $tenant = $stmt->fetch();
        $this->render('step4', ['tenant' => $tenant]);
    }

    public function postStep4()
    {
        // Auto-create wallets (NGN and USD)
        $currencies = ['NGN', 'USD'];
        foreach ($currencies as $curr) {
            $stmt = $this->db->prepare("SELECT id FROM cp_wallets WHERE tenant_id = ? AND currency = ?");
            $stmt->execute([$this->tenantId, $curr]);
            if (!$stmt->fetch()) {
                $this->db->prepare("INSERT INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0)")
                         ->execute([$this->tenantId, $_SESSION['user_id'], $curr]);
            }
        }

        // Save Payout Beneficiary if provided
        $method = $_POST['payout_method'] ?? '';
        if ($method === 'bank' && !empty($_POST['account_number']) && !empty($_POST['bank_name'])) {
            try {
                $stmt = $this->db->prepare("INSERT INTO cp_beneficiaries (user_id, account_name, account_number, bank_name, type, currency) VALUES (?, ?, ?, ?, 'bank_account', 'NGN')");
                $stmt->execute([
                    $_SESSION['user_id'],
                    trim($_POST['account_name'] ?? 'My Bank Account'),
                    trim($_POST['account_number']),
                    trim($_POST['bank_name'])
                ]);
            } catch (\Exception $e) {}
        } elseif ($method === 'crypto' && !empty($_POST['crypto_address'])) {
            try {
                $coin = trim($_POST['crypto_coin'] ?? 'USDT');
                $network = trim($_POST['crypto_network'] ?? 'TRC20');
                $stmt = $this->db->prepare("INSERT INTO cp_beneficiaries (user_id, account_name, account_number, bank_name, type, currency) VALUES (?, ?, ?, ?, 'crypto_wallet', 'USD')");
                $stmt->execute([
                    $_SESSION['user_id'],
                    "{$coin} Wallet ({$network})",
                    trim($_POST['crypto_address']),
                    "{$coin} - {$network}"
                ]);
            } catch (\Exception $e) {}
        }
        
        $this->updateStep(5);
        header('Location: /onboarding');
        exit;
    }

    // STEP 5: MODULES
    public function step5()
    {
        // Fetch all system modules
        $stmt = $this->db->query("SELECT * FROM modules");
        $allModules = $stmt->fetchAll();

        // Enrich modules with clear titles and descriptions as requested by user
        $enrichedModules = [];
        $addedNames = [];
        
        foreach ($allModules as $mod) {
            if ($mod['slug'] === 'casjoe-support' || stripos($mod['name'], 'All Access') !== false || stripos($mod['slug'], 'all-access') !== false) {
                continue;
            }

            $desc = $mod['description'];

            switch ($mod['slug']) {
                case 'casjoe-erp':
                case 'casjoe-bos':
                    $mod['name'] = 'Casjoe BOS (Business Operating System)';
                    $desc = 'The complete ERP to run your daily operations. Manage inventory, staff payroll, project tracking, tasks, and customer relations all in one unified platform.';
                    break;
                case 'casjoe-academy':
                    $mod['name'] = 'Casjoe Business School';
                    $desc = 'Create, sell, and host online courses or train your staff with built-in learning modules, marketing masterclasses, and student evaluation dashboards.';
                    break;
                case 'casjoe-smart-forms':
                    $mod['name'] = 'Casjoe Smart Forms & Payments';
                    $desc = 'Build intelligent multi-step forms, surveys, and order forms that capture customer data and process instant online payments.';
                    break;
                case 'casjoe-pay':
                    $mod['name'] = 'Casjoe Pay (Wallets & Financials)';
                    $desc = 'Your comprehensive financial hub. Accept payments across Africa, issue cards, and manage company wallets effortlessly.';
                    break;
                case 'casjoe-shop':
                case 'casjoe-mart':
                    $mod['name'] = 'Casjoe Mart & E-Commerce';
                    $desc = 'Launch your beautiful e-commerce store or marketplace. Sell physical or digital goods with integrated checkout and automated inventory tracking.';
                    break;
                case 'casjoe-mail':
                    $mod['name'] = 'Casjoe Mail (Marketing & Automation)';
                    $desc = 'Engage and convert your customers with high-converting email marketing campaigns, automated drips, and audience analytics.';
                    break;
                case 'casjoe-cloud':
                    $mod['name'] = 'Casjoe Cloud Storage';
                    $desc = 'Keep company documents, contracts, and media assets securely stored, encrypted, and accessible to authorized staff anywhere.';
                    break;
                case 'casjoe-links':
                    $mod['name'] = 'Casjoe Links & Bio Pages';
                    $desc = 'Create stunning bio pages for your social media, generate branded QR codes, and track customer engagement with smart links.';
                    break;
            }

            $mod['sub_features'] = '';
            $mod['description'] = $desc;
            
            // Prevent duplicate modules from rendering if aliases exist in DB
            if (isset($addedNames[$mod['name']])) {
                continue;
            }
            $addedNames[$mod['name']] = true;
            
            $enrichedModules[] = $mod;
        }

        $this->render('step5', ['modules' => $enrichedModules]);
    }

    public function postStep5()
    {
        $selectedSlugs = $_POST['modules'] ?? [];

        // Fetch all system modules to update status
        $stmt = $this->db->query("SELECT id, slug FROM modules");
        $allSystemModules = $stmt->fetchAll();

        foreach ($allSystemModules as $mod) {
            $slug = $mod['slug'];
            $modId = $mod['id'];
            
            // Determine status based strictly on what user selected
            $status = in_array($slug, $selectedSlugs) ? 'enabled' : 'disabled';

            // Insert or Update
            $stmt = $this->db->prepare("INSERT INTO tenant_modules (tenant_id, module_id, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = ?");
            $stmt->execute([$this->tenantId, $modId, $status, $status]);
        }

        // Skip Step 6 (Create Invoice) completely and go straight to Step 8 (Live Celebration)
        $this->updateStep(8);
        header('Location: /onboarding/complete');
        exit;
    }

    // STEP 6: FIRST ACTION (BYPASSED / REMOVED AS REQUESTED)
    public function step6()
    {
        $this->updateStep(8);
        header('Location: /onboarding/complete');
        exit;
    }

    public function skipAction()
    {
        $this->updateStep(8);
        header('Location: /onboarding/complete');
        exit;
    }

    public function action($action)
    {
        switch ($action) {
            case 'add-product':
                $stmt = $this->db->prepare("SELECT id FROM shop_vendors WHERE tenant_id = ?");
                $stmt->execute([$this->tenantId]);
                if ($stmt->fetch()) {
                    header('Location: /shop/vendor/products/create');
                } else {
                    header('Location: /shop/vendor/register');
                }
                break;
            case 'upload-cloud':
                header('Location: /cloud');
                break;
            case 'create-course':
                header('Location: /academy/instructor/courses/create');
                break;
            case 'configure-pay':
                header('Location: /pay/settings');
                break;
            default:
                header('Location: /dashboard');
        }
        $this->updateStep(8);
        exit;
    }

    // STEP 7: TOUR (Can be bypassed or kept if directly linked)
    public function step7()
    {
        $this->updateStep(8);
        header('Location: /onboarding/complete');
        exit;
    }

    // STEP 8: COMPLETE
    public function complete()
    {
        $this->updateStep(8);
        
        $stmt = $this->db->query("SELECT m.name FROM tenant_modules tm JOIN modules m ON tm.module_id = m.id WHERE tm.tenant_id = ? AND tm.status = 'enabled'", [$this->tenantId]);
        $activeNames = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $this->render('complete', ['active_names' => $activeNames]);
    }

    public function finish()
    {
        $this->updateStep(9); // Done
        header('Location: /dashboard');
        exit;
    }
}
