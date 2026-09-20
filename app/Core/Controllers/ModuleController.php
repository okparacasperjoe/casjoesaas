<?php

namespace App\Core\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\TenantContext;

class ModuleController
{
    private $db;
    private $tenantId;
    private $log;

    public function __construct()
    {
        // $logFile = 'C:/Users/UK USER/.gemini/antigravity/scratch/php-saas/toggle_debug.log';
        $log = function($msg) {
            // file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n", FILE_APPEND);
            error_log("ModuleController: " . $msg);
        };
        $this->log = $log;

        $log("ModuleController hit. Session ID: " . session_id());
        if (!isset($_SESSION['user_id'])) {
            $log("ModuleController: Unauthorized access attempt.");
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();
        $log("ModuleController: User " . $_SESSION['user_id'] . " on Tenant " . $this->tenantId);
    }

    public function toggle()
    {
        header('Content-Type: application/json');
        $log = $this->log;
        
        $input = json_decode(file_get_contents('php://input'), true);
        $log("ModuleController: Toggle payload: " . json_encode($input));
        $slug = $input['slug'] ?? '';
        $active = $input['active'] ?? false; // boolean

        if (!$slug) {
            echo json_encode(['error' => 'Invalid module slug']);
            exit;
        }

        $newStatus = $active ? 'enabled' : 'disabled';

        // Get Module ID or auto-create module entry
        $stmt = $this->db->prepare("SELECT id, name FROM modules WHERE slug = ?");
        $stmt->execute([$slug]);
        $mod = $stmt->fetch();
        $modId = $mod['id'] ?? null;
        $modName = $mod['name'] ?? ucwords(str_replace('-', ' ', $slug));

        if (!$modId) {
            $insertMod = $this->db->prepare("INSERT INTO modules (name, slug, description, created_at) VALUES (?, ?, ?, NOW())");
            $insertMod->execute([$modName, $slug, 'Casjoe Ecosystem Module']);
            $modId = $this->db->lastInsertId();
        }

        $stmt = $this->db->prepare("INSERT INTO tenant_modules (tenant_id, module_id, status, expires_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE status = ?, expires_at = CASE WHEN ? = 'enabled' THEN DATE_ADD(NOW(), INTERVAL 30 DAY) ELSE expires_at END");
        $stmt->execute([$this->tenantId, $modId, $newStatus, $newStatus, $newStatus]);
        
        // Send Notification Email ONLY when module is activated/turned ON
        if ($active) {
            $user = \App\Core\Auth::user();
            if ($user && !empty($user['email'])) {
                try {
                    $moduleCapabilities = [
                        'casjoe-bos' => "<ul>
                            <li><b>Core ERP & Inventory Management:</b> Track multi-warehouse stock levels, stock transfers, low-stock alerts, and product variants seamlessly.</li>
                            <li><b>Staff & HR Management:</b> Onboard employees, track attendance, manage leave requests, maintain digital records, and run automated payroll with payslips.</li>
                            <li><b>Projects & Task Management:</b> Create projects, assign tasks on interactive Kanban boards, track billable hours with timesheets, and manage calendars.</li>
                            <li><b>CRM & Client Management:</b> Organize leads through drag-and-drop sales pipelines, log calls/notes, and schedule meetings via the Booking Scheduler.</li>
                            <li><b>Finance & Accounting:</b> Generate professional estimates and tax-compliant invoices, track business expenses, and monitor multi-currency cash flow.</li>
                            <li><b>AI Office & Cori AI Assistant:</b> Automate routine administrative tasks, get intelligent insights, and delegate workflows to autonomous AI employees.</li>
                        </ul>",
                        'casjoe-erp' => "<ul>
                            <li><b>Core ERP & Inventory Management:</b> Track multi-warehouse stock levels, stock transfers, low-stock alerts, and product variants seamlessly.</li>
                            <li><b>Staff & HR Management:</b> Onboard employees, track attendance, manage leave requests, maintain digital records, and run automated payroll with payslips.</li>
                            <li><b>Projects & Task Management:</b> Create projects, assign tasks on interactive Kanban boards, track billable hours with timesheets, and manage calendars.</li>
                            <li><b>CRM & Client Management:</b> Organize leads through drag-and-drop sales pipelines, log calls/notes, and schedule meetings via the Booking Scheduler.</li>
                            <li><b>Finance & Accounting:</b> Generate professional estimates and tax-compliant invoices, track business expenses, and monitor multi-currency cash flow.</li>
                            <li><b>AI Office & Cori AI Assistant:</b> Automate routine administrative tasks, get intelligent insights, and delegate workflows to autonomous AI employees.</li>
                        </ul>",
                        'casjoe-mart' => "<ul>
                            <li><b>Online Storefront:</b> Launch your own high-converting, custom-branded e-commerce store with responsive checkout and mobile-friendly design.</li>
                            <li><b>Multi-Vendor Marketplace:</b> Turn your store into a digital mall where third-party vendors can list products, fulfill orders, and earn automated payouts.</li>
                            <li><b>Order & Inventory Tracking:</b> Process orders from pending to delivered, print packing slips, and automatically synchronize stock levels in real time.</li>
                            <li><b>Discounts & Coupons:</b> Run promotional campaigns, percentage/fixed discounts, and limited-time flash sales to boost average order values.</li>
                        </ul>",
                        'casjoe-shop' => "<ul>
                            <li><b>Online Storefront:</b> Launch your own high-converting, custom-branded e-commerce store with responsive checkout and mobile-friendly design.</li>
                            <li><b>Multi-Vendor Marketplace:</b> Turn your store into a digital mall where third-party vendors can list products, fulfill orders, and earn automated payouts.</li>
                            <li><b>Order & Inventory Tracking:</b> Process orders from pending to delivered, print packing slips, and automatically synchronize stock levels in real time.</li>
                            <li><b>Discounts & Coupons:</b> Run promotional campaigns, percentage/fixed discounts, and limited-time flash sales to boost average order values.</li>
                        </ul>",
                        'casjoe-pay' => "<ul>
                            <li><b>Multi-Currency Wallets:</b> Hold, convert, send, and receive funds in NGN, USD, GBP, EUR, GHS, KES, and more with instant settlement.</li>
                            <li><b>Virtual & Physical Cards:</b> Issue branded corporate spending cards for your team members, set spending limits, and pay for online subscriptions worldwide.</li>
                            <li><b>Instant Payment Links & Checkout:</b> Create one-click payment links and embeddable checkout buttons to get paid by clients across social media or email.</li>
                            <li><b>Automated Payouts & Transfers:</b> Execute bulk salary disbursements to bank accounts and vendor disbursements with automated receipts.</li>
                        </ul>",
                        'casjoe-cloud' => "<ul>
                            <li><b>Secure Document Vault:</b> Store company contracts, corporate assets, sensitive files, and intellectual property with enterprise-grade encryption.</li>
                            <li><b>Team File Sharing & Collaboration:</b> Share folders and files with internal departments or external clients using granular role-based permissions.</li>
                            <li><b>Public & Private Share Links:</b> Generate password-protected public download links with optional expiration timers for secure distribution.</li>
                            <li><b>Automated Cloud Backups:</b> Safeguard your mission-critical business data with scheduled backups and instant restoration capabilities.</li>
                        </ul>",
                        'casjoe-academy' => "<ul>
                            <li><b>Course Builder & Curriculum Design:</b> Create rich, structured online courses complete with high-definition video lessons, downloadable files, and quizzes.</li>
                            <li><b>Staff & Corporate Training:</b> Deploy internal compliance courses and onboarding modules to upskill your workforce and track completion rates.</li>
                            <li><b>Monetization & Student Portal:</b> Sell masterclasses to global students, accept payments via Casjoe Pay, and automatically issue digital certificates upon completion.</li>
                        </ul>",
                        'casjoe-links' => "<ul>
                            <li><b>Short URL & Link Shortener:</b> Create custom branded short links, set link expiration dates, enable password protection, and track click analytics across geographies and devices.</li>
                            <li><b>Link-In-Bio Pages:</b> Build beautiful, highly customizable bio pages for Instagram, TikTok, and Twitter with social links, embedded videos, and call-to-action buttons.</li>
                            <li><b>QR Code Generator:</b> Generate high-resolution, branded QR codes with custom colors, embedded logos, frames, and vCard/WiFi/URL routing.</li>
                            <li><b>Sales Funnels & Lead Capture:</b> Build conversion-optimized sales funnels, product checkout funnels, and lead capture sequences to turn clicks into paying customers.</li>
                            <li><b>Static Website & Landing Pages:</b> Create full multi-section static websites and high-converting marketing landing pages without writing any code.</li>
                        </ul>",
                        'casjoe-smart-forms' => "<ul>
                            <li><b>Drag & Drop Form Builder:</b> Create multi-step surveys, customer onboarding forms, job application portals, and feedback questionnaires effortlessly.</li>
                            <li><b>Conditional Logic & Smart Rules:</b> Show or hide questions dynamically based on previous user answers for a tailored respondent experience.</li>
                            <li><b>Integrated Form Payments:</b> Collect registration fees, event tickets, or product orders directly inside your forms using integrated payment gateways.</li>
                            <li><b>Real-Time Responses & Exports:</b> View visual analytics of submissions instantly, export responses to CSV/Excel, or trigger webhooks to third-party apps.</li>
                        </ul>",
                        'casjoe-mail' => "<ul>
                            <li><b>Newsletter Broadcasts:</b> Design high-impact, mobile-responsive email newsletters using rich HTML templates and send them to thousands of subscribers.</li>
                            <li><b>Automated Drip Sequences & Autoresponders:</b> Build automated onboarding flows, abandoned cart reminders, and lead nurturing sequences that run 24/7 on autopilot.</li>
                            <li><b>Audience & Contact Management:</b> Segment your contacts into targeted tags and lists based on customer behaviors, purchase history, or demographics.</li>
                            <li><b>Delivery & Open Rate Analytics:</b> Track real-time open rates, click-through rates (CTR), bounce rates, and unsubscribe statistics to maximize campaign ROI.</li>
                        </ul>",
                        'casjoe-support' => "<ul>
                            <li><b>Ticketing & Helpdesk:</b> Convert customer inquiries into structured support tickets with priority levels, SLA tracking, and internal team assignments.</li>
                            <li><b>Live Chat Widget:</b> Embed a custom live chat widget on your website to assist visitors in real time and convert questions into sales.</li>
                            <li><b>Knowledge Base & FAQ Portal:</b> Build a self-service help center where customers and staff can search articles, tutorials, and troubleshooting guides.</li>
                        </ul>",
                        'casjoe-ads' => "<ul>
                            <li><b>Campaign Optimization:</b> Run and track high-converting ads across Meta (Facebook/Instagram) and Google Ads directly from one dashboard.</li>
                            <li><b>AI Marketing Insights:</b> Get AI-powered suggestions to optimize ad spend and lower your customer acquisition cost.</li>
                        </ul>",
                        'casjoe-crm' => "<ul>
                            <li><b>Sales Pipeline:</b> Organize customer deals through drag-and-drop pipeline stages from initial lead to closed-won.</li>
                            <li><b>Customer Interaction Logging:</b> Keep track of calls, emails, and meetings for every client in unified profiles.</li>
                        </ul>",
                        'casjoe-hrm' => "<ul>
                            <li><b>Staff Onboarding & Records:</b> Maintain complete digital files for all your employees and contractors.</li>
                            <li><b>Payroll & Taxes:</b> Automate salary computations, deductions, and payslip generation.</li>
                        </ul>"
                    ];

                    $featuresHtml = $moduleCapabilities[$slug] ?? "<ul><li><b>Full Module Access:</b> Unlock dedicated operational tools and automated workflows.</li><li><b>Executive Command Center:</b> Monitor real-time analytics and data for {$modName} directly on your dashboard.</li></ul>";

                    $subject = "🎉 Module Activated: " . $modName . " is now live!";
                    $body = "
                        <div style='font-family: Inter, sans-serif; max-width: 600px; margin: 0 auto; color: #1e293b;'>
                            <h2 style='color: #000066; margin-bottom: 12px;'>Welcome to " . htmlspecialchars($modName) . "! ⚡</h2>
                            <p style='font-size: 15px; line-height: 1.6;'>Hello <b>" . htmlspecialchars($user['name']) . "</b>,</p>
                            <p style='font-size: 15px; line-height: 1.6;'>Congratulations! You have successfully activated the <b>" . htmlspecialchars($modName) . "</b> module for your organization on Casjoe BOS.</p>
                            
                            <div style='background: #f8fafc; border-left: 4px solid #000066; padding: 18px; border-radius: 8px; margin: 24px 0;'>
                                <h3 style='margin-top: 0; color: #0f172a; font-size: 16px;'>Here is what you can do right now with " . htmlspecialchars($modName) . ":</h3>
                                " . $featuresHtml . "
                            </div>
                            
                            <p style='font-size: 15px; line-height: 1.6;'>You can launch this module anytime from your Global Dashboard or manage your ecosystem settings from the executive command center.</p>
                            <br>
                            <p style='font-size: 14px; color: #64748b;'>Best regards,<br><b>The Casjoe Team & Cori AI</b></p>
                        </div>
                    ";

                    \App\Core\Mailer::send($user['email'], $subject, $body);
                    $log("ModuleController: Welcome email sent for " . $modName . " to " . $user['email']);
                } catch (\Exception $e) {
                    $log("ModuleController: Email notification failed: " . $e->getMessage());
                }
            }
        } else {
            $log("ModuleController: Module " . $modName . " deactivated. No email sent.");
        }

        echo json_encode([
            'success' => true,
            'slug' => $slug,
            'status' => ($newStatus === 'enabled' ? 'active' : 'inactive'),
            'module_name' => $modName
        ]);
        exit;
    }
}
