<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class FunnelPublicController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Show public funnel
     */
    public function show($params)
    {
        // The route is /f/{slug} but the actual value passed is the Funnel ID
        $funnelId = is_array($params) ? ($params['slug'] ?? $params['id'] ?? 0) : $params;
        
        // Get funnel details
        $stmt = $this->db->query(
            "SELECT * FROM sales_funnels WHERE id = ?",
            [$funnelId]
        );
        $funnel = $stmt->fetch();

        if (!$funnel) {
            http_response_code(404);
            echo "Funnel not found";
            return;
        }

        // Check if funnel is active
        if ($funnel['status'] !== 'active') {
            http_response_code(403);
            require __DIR__ . '/../Views/public/funnel_unavailable.php';
            return;
        }

        // Get funnel steps
        $stmt = $this->db->query(
            "SELECT * FROM funnel_steps WHERE funnel_id = ? ORDER BY step_order ASC",
            [$funnelId]
        );
        $steps = $stmt->fetchAll();

        if (empty($steps)) {
            echo "This funnel has no steps configured yet.";
            return;
        }

        // Create or get session
        $sessionToken = $_COOKIE['funnel_session_' . $funnelId] ?? null;
        
        if (!$sessionToken) {
            $sessionToken = bin2hex(random_bytes(32));
            setcookie('funnel_session_' . $funnelId, $sessionToken, time() + 86400, '/');
            
            // Create session record
            $stmt = $this->db->prepare(
                "INSERT INTO funnel_sessions (funnel_id, session_token, entry_url, ip_address, user_agent) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $funnelId,
                $sessionToken,
                $_SERVER['REQUEST_URI'] ?? '',
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $sessionId = $this->db->getConnection()->lastInsertId();
            
            // Log view event
            $this->trackEvent($funnelId, $sessionId, 'view', ['step_id' => $steps[0]['id']]);
            
            // Log in ERP activity feed
            require_once __DIR__ . '/../Services/ActivityLogger.php';
            $activityLogger = new \App\Modules\CasjoeLinks\Services\ActivityLogger();
            $activityLogger->logFunnelView($funnel['tenant_id'], $funnelId, $funnel['name'], $sessionId);
        }

        // Get current step (default to first)
        $currentStepIndex = $_GET['step'] ?? 0;
        $currentStep = $steps[$currentStepIndex] ?? $steps[0];

        // Render step based on type
        require __DIR__ . '/../Views/public/funnel_render.php';
    }

    /**
     * Process funnel step submission
     */
    public function processStep($params)
    {
        $funnelId = is_array($params) ? ($params['slug'] ?? $params['id'] ?? 0) : $params;
        $stepIndex = $_POST['step_index'] ?? 0;
        
        // Get funnel and steps
        $stmt = $this->db->query("SELECT * FROM sales_funnels WHERE id = ?", [$funnelId]);
        $funnel = $stmt->fetch();
        
        $stmt = $this->db->query(
            "SELECT * FROM funnel_steps WHERE funnel_id = ? ORDER BY step_order ASC",
            [$funnelId]
        );
        $steps = $stmt->fetchAll();
        
        $currentStep = $steps[$stepIndex] ?? null;
        
        if (!$currentStep) {
            header('Location: /f/' . $funnelId);
            exit;
        }

        // Get session
        $sessionToken = $_COOKIE['funnel_session_' . $funnelId] ?? null;
        $sessionId = null;
        if ($sessionToken) {
            $stmt = $this->db->query(
                "SELECT id FROM funnel_sessions WHERE session_token = ? AND funnel_id = ?",
                [$sessionToken, $funnelId]
            );
            $session = $stmt->fetch();
            $sessionId = $session['id'] ?? null;
        }

        // Process based on step type
        switch ($currentStep['step_type']) {
            case 'landing':
                $this->trackEvent($funnelId, $sessionId, 'landing_complete', ['step_id' => $currentStep['id']]);
                break;
                
            case 'form':
                // Create/update contact
                require_once __DIR__ . '/../Services/ContactMatcher.php';
                $matcher = new \App\Modules\CasjoeLinks\Services\ContactMatcher();
                
                $contactData = [
                    'name' => $_POST['name'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'company' => $_POST['company'] ?? ''
                ];
                
                $funnelMetadata = [
                    'funnel_id' => $funnelId,
                    'funnel_name' => $funnel['name'],
                    'funnel_type' => $funnel['type'],
                    'lead_source' => $funnel['lead_source'],
                    'entry_url' => $_SERVER['HTTP_REFERER'] ?? ''
                ];
                
                $contactId = $matcher->findOrCreateContact($funnel['tenant_id'], $contactData, $funnelMetadata);
                
                if ($sessionId) {
                    $this->db->query(
                        "UPDATE funnel_sessions SET contact_id = ? WHERE id = ?",
                        [$contactId, $sessionId]
                    );
                }
                
                // Auto-create deal
                require_once __DIR__ . '/../Services/DealAutomation.php';
                $dealAutomation = new \App\Modules\CasjoeLinks\Services\DealAutomation();
                
                $dealId = $dealAutomation->createDeal($funnel, $contactId, [
                    'form_data' => $_POST
                ]);
                
                // Update session with deal ID
                if ($sessionId) {
                    $this->db->query(
                        "UPDATE funnel_sessions SET deal_id = ? WHERE id = ?",
                        [$dealId, $sessionId]
                    );
                }
                
                $_SESSION['funnel_contact_id_' . $funnelId] = $contactId;
                $_SESSION['funnel_deal_id_' . $funnelId] = $dealId;
                $_SESSION['funnel_form_data_' . $funnelId] = $_POST;
                
                $this->trackEvent($funnelId, $sessionId, 'form_submit', [
                    'contact_id' => $contactId,
                    'deal_id' => $dealId
                ]);
                
                // Send form confirmation email
                if (!empty($contactData['email'])) {
                    require_once __DIR__ . '/../Services/MailTrigger.php';
                    $mailTrigger = new \App\Modules\CasjoeLinks\Services\MailTrigger();
                    $mailTrigger->sendFormConfirmation($funnelId, $contactData['email'], $contactData);
                }
                
                // Log in ERP activity feed
                require_once __DIR__ . '/../Services/ActivityLogger.php';
                $activityLogger = new \App\Modules\CasjoeLinks\Services\ActivityLogger();
                $activityLogger->logFormSubmission(
                    $funnel['tenant_id'],
                    $funnelId,
                    $funnel['name'],
                    $contactId,
                    $contactData['name'] ?? 'Unknown',
                    $dealId
                );
                
                break;
                
            case 'payment':
                // Create payment
                require_once __DIR__ . '/../Services/PaymentProcessor.php';
                $paymentProcessor = new \App\Modules\CasjoeLinks\Services\PaymentProcessor();
                
                $paymentConfig = json_decode($currentStep['config'] ?? '{}', true);
                
                // Handle order bump
                if (!empty($_POST['order_bump']) && !empty($paymentConfig['has_order_bump'])) {
                    $bumpAmount = $paymentConfig['bump_amount'] ?? 0;
                    $paymentConfig['amount'] = ($paymentConfig['amount'] ?? 0) + $bumpAmount;
                    $paymentConfig['description'] = ($paymentConfig['description'] ?? '') . ' + ' . ($paymentConfig['bump_title'] ?? 'Order Bump');
                    
                    $dealId = $_SESSION['funnel_deal_id_' . $funnelId] ?? null;
                    if ($dealId) {
                        try {
                            $this->db->query("UPDATE deals SET value = value + ? WHERE id = ?", [$bumpAmount, $dealId]);
                        } catch (\Exception $e) {
                            // Silently ignore DB error
                        }
                    }
                }

                $contactId = $_SESSION['funnel_contact_id_' . $funnelId] ?? null;
                $dealId = $_SESSION['funnel_deal_id_' . $funnelId] ?? null;
                
                $paymentDetails = $paymentProcessor->createPayment(
                    $funnelId,
                    $sessionId,
                    $paymentConfig,
                    $contactId
                );
                
                // Store payment ID in session
                $_SESSION['funnel_payment_id_' . $funnelId] = $paymentDetails['payment_id'];
                $_SESSION['funnel_deal_id_for_payment_' . $funnelId] = $dealId;
                
                // Redirect to payment gateway
                $callbackUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/f/' . $funnelId . '/payment/callback';
                $cancelUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/f/' . $funnelId . '?step=' . $stepIndex;
                
                // Redirect to Casjoe Pay checkout
                header('Location: /pay/checkout?amount=' . $paymentDetails['amount'] . 
                       '&currency=' . urlencode($paymentDetails['currency'] ?? 'NGN') .
                       '&description=' . urlencode($paymentDetails['description']) . 
                       '&callback=' . urlencode($callbackUrl) . 
                       '&cancel=' . urlencode($cancelUrl) . 
                       '&reference=funnel_' . $paymentDetails['payment_id']);
                exit;

            case 'upsell':
            case 'downsell':
                $action = $_POST['offer_action'] ?? 'accept';
                $config = json_decode($currentStep['config'] ?? '{}', true);
                $contactId = $_SESSION['funnel_contact_id_' . $funnelId] ?? null;
                $dealId = $_SESSION['funnel_deal_id_' . $funnelId] ?? null;
                $productName = $config['product_name'] ?? 'Offer';
                
                $nextStepIndex = $stepIndex + 1;

                if ($action === 'accept') {
                    $this->trackEvent($funnelId, $sessionId, $currentStep['step_type'] . '_accepted', ['step_id' => $currentStep['id']]);
                    
                    if ($contactId) {
                        try {
                            require_once __DIR__ . '/../../../Core/Services/LeadScoringService.php';
                            \App\Core\Services\LeadScoringService::addPoints($funnel['tenant_id'], (int)$contactId, 'Upsell accepted: ' . $productName, 15);
                        } catch (\Exception $e) {}
                    }
                    
                    $offerAmount = $config['amount'] ?? $config['price'] ?? 0;
                    if ($dealId && $offerAmount > 0) {
                        try {
                            $this->db->query("UPDATE deals SET value = value + ? WHERE id = ?", [$offerAmount, $dealId]);
                        } catch (\Exception $e) {}
                    }
                    
                    // Skip downsell if we accepted
                    if (isset($steps[$nextStepIndex]) && $steps[$nextStepIndex]['step_type'] === 'downsell') {
                        $nextStepIndex++;
                    }
                } else {
                    $this->trackEvent($funnelId, $sessionId, $currentStep['step_type'] . '_declined', ['step_id' => $currentStep['id']]);
                    
                    if ($currentStep['step_type'] === 'downsell') {
                        // Skip any remaining downsells
                        while (isset($steps[$nextStepIndex]) && $steps[$nextStepIndex]['step_type'] === 'downsell') {
                            $nextStepIndex++;
                        }
                    }
                }
                
                if (isset($steps[$nextStepIndex])) {
                    header('Location: /f/' . $funnelId . '?step=' . $nextStepIndex);
                } else {
                    if ($sessionId) {
                        $this->db->query("UPDATE funnel_sessions SET completed_at = NOW() WHERE id = ?", [$sessionId]);
                    }
                    header('Location: /f/' . $funnelId . '/complete');
                }
                exit;
        }

        // Advance to next step
        $nextStepIndex = $stepIndex + 1;
        
        if (isset($steps[$nextStepIndex])) {
            header('Location: /f/' . $funnelId . '?step=' . $nextStepIndex);
        } else {
            if ($sessionId) {
                $this->db->query(
                    "UPDATE funnel_sessions SET completed_at = NOW() WHERE id = ?",
                    [$sessionId]
                );
            }
            header('Location: /f/' . $funnelId . '/complete');
        }
        exit;
    }

    /**
     * Funnel completion page
     */
    public function complete($params)
    {
        $funnelId = is_array($params) ? ($params['slug'] ?? $params['id'] ?? 0) : $params;
        
        $stmt = $this->db->query("SELECT * FROM sales_funnels WHERE id = ?", [$funnelId]);
        $funnel = $stmt->fetch();
        
        // Get thank you step config
        $stmt = $this->db->query(
            "SELECT config FROM funnel_steps WHERE funnel_id = ? AND step_type = 'thankyou'",
            [$funnelId]
        );
        $thankYouStep = $stmt->fetch();
        $config = json_decode($thankYouStep['config'] ?? '{}', true);
        
        require __DIR__ . '/../Views/public/funnel_complete.php';
    }

    /**
     * Handle payment callback from Casjoe Pay
     */
    public function paymentCallback($params)
    {
        $funnelId = is_array($params) ? ($params['id'] ?? $params['slug'] ?? 0) : $params;
        $status = $_GET['status'] ?? 'failed';
        $reference = $_GET['reference'] ?? '';
        
        // Extract payment ID from reference (format: funnel_{payment_id})
        $paymentId = (int)str_replace('funnel_', '', $reference);
        
        if (!$paymentId) {
            header('Location: /f/' . $funnelId . '?error=invalid_payment');
            exit;
        }
        
        require_once __DIR__ . '/../Services/PaymentProcessor.php';
        $paymentProcessor = new \App\Modules\CasjoeLinks\Services\PaymentProcessor();
        
        if ($status === 'success') {
            // Get deal ID from session
            $dealId = $_SESSION['funnel_deal_id_for_payment_' . $funnelId] ?? null;
            
            // Process successful payment
            $paymentProcessor->handlePaymentSuccess($paymentId, $reference, $dealId);
            
            // Send payment confirmation email
            $payment = $paymentProcessor->getPayment($paymentId);
            if ($payment && !empty($_SESSION['funnel_form_data_' . $funnelId]['email'])) {
                require_once __DIR__ . '/../Services/MailTrigger.php';
                $mailTrigger = new \App\Modules\CasjoeLinks\Services\MailTrigger();
                $mailTrigger->sendPaymentConfirmation(
                    $funnelId,
                    $_SESSION['funnel_form_data_' . $funnelId]['email'],
                    $payment['amount'],
                    $reference
                );
            }
            
            // Get funnel steps to find next step
            $stmt = $this->db->query(
                "SELECT * FROM funnel_steps WHERE funnel_id = ? ORDER BY step_order ASC",
                [$funnelId]
            );
            $steps = $stmt->fetchAll();
            
            // Find current payment step and advance
            foreach ($steps as $index => $step) {
                if ($step['step_type'] === 'payment') {
                    $nextStepIndex = $index + 1;
                    if (isset($steps[$nextStepIndex])) {
                        header('Location: /f/' . $funnelId . '?step=' . $nextStepIndex . '&payment=success');
                    } else {
                        header('Location: /f/' . $funnelId . '/complete?payment=success');
                    }
                    exit;
                }
            }
        } else {
            // Handle failed payment
            $reason = $_GET['message'] ?? 'Payment declined';
            $paymentProcessor->handlePaymentFailed($paymentId, $reason);
            
            // Redirect back to payment step
            header('Location: /f/' . $funnelId . '?error=payment_failed&message=' . urlencode($reason));
            exit;
        }
    }

    /**
     * Track funnel event
     */
    private function trackEvent($funnelId, $sessionId, $eventType, $metadata = [])
    {
        $stmt = $this->db->prepare(
            "INSERT INTO funnel_analytics (funnel_id, session_id, event_type, metadata) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$funnelId, $sessionId, $eventType, json_encode($metadata)]);
    }
}
