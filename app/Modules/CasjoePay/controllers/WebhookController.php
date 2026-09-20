<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\Services\NotificationService;
use PDO;

class WebhookController
{
    private $logFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../../../../storage/logs/strowallet_webhook.log';
        if (!is_dir(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    // ── Entry point for ALL Strowallet & ZiiroPay webhooks ──────────
    // Handles: /pay/webhook/strowallet  AND  /api/strowallet/webhook
    //          /api/ziiropay/webhook    AND  /pay/webhook/ziiropay
    //          /webhook/safeheaven      AND  /webhook/paga  AND  /webhook/amucha
    public function strowallet()
    {
        // Support GET healthcheck from webhook pingers
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'ok', 'message' => 'Casjoe Pay ZiiroPay/StroWallet webhook endpoint active']);
            exit;
        }

        // Detect which provider URL was called
        $uri      = $_SERVER['REQUEST_URI'] ?? '/webhook/unknown';
        $provider = basename(strtok($uri, '?')); // e.g. "paga", "safeheaven", "amucha", "ziiropay", "strowallet"

        $payloadRaw = file_get_contents('php://input');
        $this->log("RECEIVED [$provider]", $payloadRaw);

        header('Content-Type: application/json');

        $data = json_decode($payloadRaw, true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
            exit;
        }

        // Detect event type
        $event = strtolower(trim($data['event'] ?? $data['type'] ?? ''));

        // Virtual bank deposit events
        $depositEvents = [
            'virtual_account.credit',
            'virtualaccount.credit',
            'collection',
            'bank.transfer.received',
            'deposit',
            'transfer',
            'credit',
        ];

        $isCardEvent = (
            strpos($event, 'virtualcard.') === 0 ||
            strpos($event, 'card.') === 0 ||
            strpos($event, 'otp.') === 0 ||
            strpos($event, 'usdt.') === 0 ||
            isset($data['cardId']) ||
            isset($data['card_id']) ||
            isset($data['data']['cardId']) ||
            isset($data['data']['card_id'])
        );

        if ($isCardEvent && !$this->looksLikeDeposit($data)) {
            $this->handleCardEvent($data, $payloadRaw);
        } elseif (in_array($event, $depositEvents) || $this->looksLikeDeposit($data)) {
            $this->handleDeposit($data, $payloadRaw, $provider);
        } else {
            // Fall back to card update logic
            $this->handleCardEvent($data, $payloadRaw);
        }

        exit;
    }

    /**
     * POST /webhook/naira-card
     * Handles Naira card authorization, transaction, refund, and balance webhooks
     */
    public function nairaCard()
    {
        $payloadRaw = file_get_contents('php://input');
        $this->log('NAIRA_CARD_WEBHOOK', $payloadRaw);

        header('Content-Type: application/json');

        $data = json_decode($payloadRaw, true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // ── Authorization Request (POS/ATM/Online) ──
            if (isset($data['authorization.request'])) {
                $cardId = $data['card_Id'] ?? '';
                $amount = (float)($data['merchantAmount'] ?? 0);
                $fee = (float)($data['fee'] ?? 0);
                $totalDebit = $amount + $fee;

                $this->log('NAIRA_AUTH_REQ', "Card: $cardId | Amount: $amount | Fee: $fee");

                // Find card owner
                $stmt = $db->prepare("SELECT nc.user_id, w.balance FROM cp_naira_cards nc LEFT JOIN cp_wallets w ON nc.user_id = w.user_id AND w.currency = 'NGN' WHERE nc.card_id = ? LIMIT 1");
                $stmt->execute([$cardId]);
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$row) {
                    $this->log('NAIRA_AUTH_NO_CARD', "Card not found: $cardId");
                    echo json_encode(['APPROVE' => 'NO', 'reason' => 'Card not registered']);
                    exit;
                }

                $balance = (float)($row['balance'] ?? 0);

                if ($balance >= $totalDebit) {
                    // Deduct from wallet
                    $db->prepare("UPDATE cp_wallets SET balance = balance - ? WHERE user_id = ? AND currency = 'NGN'")
                       ->execute([$totalDebit, $row['user_id']]);

                    // Log pending transaction
                    $ref = 'NAIRA_ATM_' . ($data['authorization.request'] ?? uniqid());
                    $desc = 'ATM/POS: ' . ($data['merchant']['name'] ?? 'Unknown') . ' - ' . ucfirst($data['channel'] ?? 'pos');
                    $meta = json_encode($data);
                    $db->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta, created_at) VALUES ((SELECT tenant_id FROM users WHERE id = ?), ?, ?, 'debit', ?, 'NGN', 'pending', ?, ?, NOW())")
                       ->execute([$row['user_id'], $row['user_id'], $ref, $totalDebit, $desc, $meta]);

                    $this->log('NAIRA_AUTH_APPROVED', "User {$row['user_id']} | Amount: $totalDebit");
                    echo json_encode(['APPROVE' => 'YES']);
                } else {
                    $this->log('NAIRA_AUTH_DECLINED', "User {$row['user_id']} | Balance: $balance < Required: $totalDebit");
                    echo json_encode(['APPROVE' => 'NO', 'reason' => 'Insufficient balance']);
                }
                exit;
            }

            // ── Transaction Created (successful) ──
            if (isset($data['transaction.created'])) {
                $txnId = $data['transaction.created'];
                $cardId = $data['card_Id'] ?? '';
                $ref = 'NAIRA_ATM_' . $txnId;

                // Update pending transaction to successful
                $db->prepare("UPDATE cp_transactions SET status = 'successful' WHERE reference = ?")
                   ->execute([$ref]);

                $this->log('NAIRA_TXN_OK', "Ref: $ref confirmed");
                echo json_encode(['status' => 'success']);
                exit;
            }

            // ── Transaction Refund ──
            if (isset($data['transaction.refund'])) {
                $txnId = $data['transaction.refund'];
                $cardId = $data['card_Id'] ?? '';
                $amount = (float)($data['merchantAmount'] ?? 0);

                // Find user
                $stmt = $db->prepare("SELECT user_id FROM cp_naira_cards WHERE card_id = ? LIMIT 1");
                $stmt->execute([$cardId]);
                $userId = $stmt->fetchColumn();

                if ($userId) {
                    $db->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = 'NGN'")
                       ->execute([$amount, $userId]);

                    $ref = 'NAIRA_REFUND_' . $txnId;
                    $db->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta, created_at) VALUES ((SELECT tenant_id FROM users WHERE id = ?), ?, ?, 'credit', ?, 'NGN', 'successful', 'ATM/POS Refund', ?, NOW())")
                       ->execute([$userId, $userId, $ref, $amount, json_encode($data)]);

                    $this->log('NAIRA_REFUND_OK', "Refunded $amount to user $userId");
                }

                echo json_encode(['status' => 'success']);
                exit;
            }

            // ── Balance Check ──
            if (isset($data['card.balance'])) {
                $cardId = $data['card_Id'] ?? '';

                $stmt = $db->prepare("SELECT w.balance FROM cp_naira_cards nc JOIN cp_wallets w ON nc.user_id = w.user_id AND w.currency = 'NGN' WHERE nc.card_id = ? LIMIT 1");
                $stmt->execute([$cardId]);
                $balance = (float)($stmt->fetchColumn() ?: 0);

                $this->log('NAIRA_BALANCE_CHECK', "Card: $cardId | Balance: $balance");
                echo json_encode(['balance' => $balance]);
                exit;
            }

            // Unknown event
            $this->log('NAIRA_UNKNOWN_EVENT', substr($payloadRaw, 0, 500));
            echo json_encode(['status' => 'ignored']);

        } catch (\Exception $e) {
            $this->log('NAIRA_WEBHOOK_ERROR', $e->getMessage());
            http_response_code(200); // Must return 200 per StroWallet docs
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Deposit Handler ──────────────────────────────────────────────
    private function handleDeposit(array $data, string $raw, string $provider = 'unknown')
    {
        // Normalise — Strowallet may nest under data/response/account
        $payload = $data['data'] ?? $data;

        // Support both snake_case and camelCase field names
        // PAGA sends: accountNumber, transactionAmount, sessionId, sourceAccountName, tranRemarks
        $accountNumber = $payload['account_number']
                      ?? $payload['accountNumber']
                      ?? $payload['virtual_account_number']
                      ?? $payload['destination_account']
                      ?? null;

        $amount = (float)($payload['amount']
               ?? $payload['settled_amount']
               ?? $payload['settledAmount']
               ?? $payload['transaction_amount']
               ?? $payload['transactionAmount']   // ← PAGA key
               ?? 0);

        $reference = $payload['reference']
                  ?? $payload['transaction_reference']
                  ?? $payload['session_id']
                  ?? $payload['sessionId']          // ← PAGA key
                  ?? $payload['settlementId']        // ← PAGA fallback
                  ?? $payload['transactionId']
                  ?? ('VB-' . uniqid());

        $currency   = strtoupper($payload['currency'] ?? 'NGN');

        $senderName = $payload['sender_name']
                   ?? $payload['originator_name']
                   ?? $payload['sourceAccountName']  // ← PAGA key
                   ?? $payload['payer_name']
                   ?? 'Unknown Sender';

        $narration  = $payload['narration']
                   ?? $payload['tranRemarks']        // ← PAGA key
                   ?? $payload['description']
                   ?? $payload['remark']
                   ?? "Deposit via " . strtoupper($provider);

        $this->log("DEPOSIT [$provider]", "Account: $accountNumber | Amount: $amount $currency | Ref: $reference");


        if (!$accountNumber || $amount <= 0) {
            $this->log("DEPOSIT_SKIP", "Missing account_number or zero amount. Raw: " . substr($raw, 0, 500));
            echo json_encode(['status' => 'ignored', 'message' => 'Missing account number or amount']);
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // 1. Find which user owns this virtual account
            $stmt = $db->prepare("SELECT va.user_id, u.email, u.tenant_id 
                                   FROM virtual_accounts va 
                                   JOIN users u ON va.user_id = u.id
                                   WHERE va.account_number = ? LIMIT 1");
            $stmt->execute([$accountNumber]);
            $owner = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$owner) {
                $this->log("DEPOSIT_NO_USER", "No user found for account_number: $accountNumber");
                echo json_encode(['status' => 'ignored', 'message' => 'Account not found']);
                return;
            }

            $userId   = $owner['user_id'];
            $email    = $owner['email'];
            $tenantId = $owner['tenant_id'];

            // 2. Idempotency — skip if already processed
            $stmt = $db->prepare("SELECT id FROM cp_transactions WHERE reference = ? LIMIT 1");
            $stmt->execute([$reference]);
            if ($stmt->fetchColumn()) {
                $this->log("DEPOSIT_DUPLICATE", "Ref $reference already processed");
                echo json_encode(['status' => 'duplicate', 'message' => 'Already processed']);
                return;
            }

            // 3. Credit cp_wallets (NGN wallet)
            // Ensure wallet exists
            $db->prepare("INSERT IGNORE INTO cp_wallets (tenant_id, user_id, currency, balance) VALUES (?, ?, ?, 0.00)")
               ->execute([$tenantId, $userId, $currency]);

            $db->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?")
               ->execute([$amount, $userId, $currency]);

            // 4. Record transaction
            $desc = "Virtual Bank Deposit — $senderName" . ($narration ? ": $narration" : '');
            $meta = json_encode([
                'event'        => $data['event'] ?? 'deposit',
                'sender_name'  => $senderName,
                'account_no'   => $accountNumber,
                'narration'    => $narration,
                'raw_payload'  => substr($raw, 0, 800),
            ]);

            $db->prepare("INSERT INTO cp_transactions 
                          (tenant_id, user_id, reference, type, amount, currency, status, description, meta, created_at)
                          VALUES (?, ?, ?, 'credit', ?, ?, 'successful', ?, ?, NOW())")
               ->execute([$tenantId, $userId, $reference, $amount, $currency, $desc, $meta]);

            $this->log("DEPOSIT_OK", "Credited $amount $currency to user $userId (ref: $reference)");

            // 5. Send email credit alert
            if ($email) {
                $subject = "Credit Alert: {$currency} " . number_format($amount, 2) . " Received";
                $title   = "💰 Money Received";
                $content = "
                    <p>A bank transfer has been credited to your Casjoe Pay wallet.</p>
                    <table style='width:100%; border-collapse:collapse; margin-top:15px;'>
                        <tr><td style='padding:8px 0; border-bottom:1px solid #eee;'>Amount</td>
                            <td style='padding:8px 0; border-bottom:1px solid #eee; text-align:right; font-weight:bold; color:#000066;'>{$currency} " . number_format($amount, 2) . "</td></tr>
                        <tr><td style='padding:8px 0; border-bottom:1px solid #eee;'>From</td>
                            <td style='padding:8px 0; border-bottom:1px solid #eee; text-align:right;'>" . htmlspecialchars($senderName) . "</td></tr>
                        <tr><td style='padding:8px 0; border-bottom:1px solid #eee;'>Narration</td>
                            <td style='padding:8px 0; border-bottom:1px solid #eee; text-align:right;'>" . htmlspecialchars($narration) . "</td></tr>
                        <tr><td style='padding:8px 0; border-bottom:1px solid #eee;'>Reference</td>
                            <td style='padding:8px 0; border-bottom:1px solid #eee; text-align:right;'>{$reference}</td></tr>
                    </table>
                    <p style='margin-top:20px; color:#666;'>Log in to your dashboard to see your updated balance.</p>
                ";
                try {
                    NotificationService::sendTransactional($email, $subject, $title, $content);
                } catch (\Exception $e) {
                    $this->log("EMAIL_ERR", $e->getMessage());
                }
            }

            echo json_encode(['status' => 'success', 'message' => "Credited {$amount} {$currency} to user {$userId}"]);

        } catch (\Exception $e) {
            $this->log("DEPOSIT_ERROR", $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ── Card Event Handler (Supports ZiiroPay camelCase & legacy snake_case) ──
    private function handleCardEvent(array $data, string $raw)
    {
        $cardData = $data['data'] ?? $data;
        $event    = strtolower(trim($data['event'] ?? $data['type'] ?? ''));

        // Normalize card ID (support camelCase, snake_case, and nested variants)
        $cardId = $cardData['cardId']
               ?? $cardData['card_id']
               ?? $cardData['_id']
               ?? $data['cardId']
               ?? $data['card_id']
               ?? null;

        $amount    = (float)($cardData['amount'] ?? $data['amount'] ?? 0);
        $reference = $cardData['reference'] ?? $data['reference'] ?? ('CARD_EVT_' . uniqid());

        $this->log("CARD_EVENT", "Event: $event | cardId: " . ($cardId ?? 'null') . " | Ref: $reference");

        try {
            $db = Database::getInstance();

            // 1. Event: virtualcard.created.complete
            if ($event === 'virtualcard.created.complete') {
                $lastFour = $cardData['lastFour'] ?? $cardData['last4'] ?? $data['lastFour'] ?? $data['last4'] ?? null;
                $brand    = strtoupper($cardData['cardBrand'] ?? $data['cardBrand'] ?? 'VISA');
                $status   = strtolower($cardData['status'] ?? $data['status'] ?? 'active');

                $sql = "UPDATE cp_virtual_cards SET status = ?, brand = ?, card_type = ?";
                $params = [$status, $brand, $brand];

                if ($lastFour) {
                    $sql .= ", masked_pan = ?";
                    $params[] = '**** **** **** ' . $lastFour;
                }
                $sql .= " WHERE card_id = ?";
                $params[] = $cardId;

                $db->query($sql, $params);
                $this->log("CARD_CREATED_COMPLETE", "cardId: $cardId | brand: $brand | last4: $lastFour");
                echo json_encode(['status' => 'success', 'message' => 'Card activated successfully']);
                return;
            }

            // 2. Event: virtualcard.created.failed
            if ($event === 'virtualcard.created.failed') {
                $reason = $cardData['failureReason'] ?? $data['failureReason'] ?? 'Card creation failed by issuer';
                $db->query("UPDATE cp_virtual_cards SET status = 'failed' WHERE card_id = ?", [$cardId]);

                // Notify card owner
                $stmt = $db->query("SELECT user_id, tenant_id FROM cp_virtual_cards WHERE card_id = ? LIMIT 1", [$cardId]);
                $card = $stmt->fetch();
                if ($card) {
                    $userStmt = $db->query("SELECT email, name FROM users WHERE id = ? LIMIT 1", [$card['user_id']]);
                    $owner = $userStmt->fetch();
                    if ($owner && !empty($owner['email'])) {
                        try {
                            NotificationService::sendTransactional(
                                $owner['email'],
                                "Virtual Card Creation Update",
                                "⚠️ Card Issuance Notice",
                                "<p>Hello " . htmlspecialchars($owner['name'] ?? 'User') . ",</p><p>We were unable to provision your virtual card due to the following reason from the card issuer: <strong>" . htmlspecialchars($reason) . "</strong>.</p><p>Please contact Casjoe Pay support or try issuing an Instant Lite Card.</p>"
                            );
                        } catch (\Exception $e) {
                            $this->log("NOTIF_ERR", $e->getMessage());
                        }
                    }
                }

                $this->log("CARD_CREATED_FAILED", "cardId: $cardId | reason: $reason");
                echo json_encode(['status' => 'success', 'message' => 'Card failure handled']);
                return;
            }

            // 3. Event: virtualcard.transaction.authorization
            if ($event === 'virtualcard.transaction.authorization') {
                $merchant  = $cardData['merchant'] ?? $data['merchant'] ?? 'Card Merchant';
                $narrative = $cardData['narrative'] ?? $data['narrative'] ?? 'Online Purchase';
                $currency  = strtoupper($cardData['currency'] ?? $data['currency'] ?? 'USD');

                // Idempotency check
                $stmtChk = $db->query("SELECT id FROM cp_transactions WHERE reference = ? LIMIT 1", [$reference]);
                if ($stmtChk->fetch()) {
                    $this->log("CARD_AUTH_DUPLICATE", "Ref $reference already processed");
                    echo json_encode(['status' => 'duplicate', 'message' => 'Already processed']);
                    return;
                }

                $stmtCard = $db->query("SELECT user_id, tenant_id, balance FROM cp_virtual_cards WHERE card_id = ? LIMIT 1", [$cardId]);
                $card = $stmtCard->fetch();

                if ($card) {
                    $userId   = $card['user_id'];
                    $tenantId = $card['tenant_id'];

                    // Deduct from card balance
                    $db->query("UPDATE cp_virtual_cards SET balance = GREATEST(0, balance - ?) WHERE card_id = ?", [$amount, $cardId]);

                    $desc = "Card Purchase: " . $merchant . ($narrative ? " ($narrative)" : "");
                    $meta = json_encode([
                        'event'     => $event,
                        'card_id'   => $cardId,
                        'merchant'  => $merchant,
                        'narrative' => $narrative,
                        'currency'  => $currency,
                        'raw'       => substr($raw, 0, 800)
                    ]);

                    $db->query(
                        "INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta, created_at) VALUES (?, ?, ?, 'debit', ?, ?, 'successful', ?, ?, NOW())",
                        [$tenantId, $userId, $reference, $amount, $currency, $desc, $meta]
                    );

                    $this->log("CARD_AUTH_PROCESSED", "cardId: $cardId | Amount: $amount $currency | Merchant: $merchant | Ref: $reference");

                    $userStmt = $db->query("SELECT email, name FROM users WHERE id = ? LIMIT 1", [$userId]);
                    $owner = $userStmt->fetch();
                    if ($owner && !empty($owner['email'])) {
                        try {
                            $subject = "Card Debit Alert: {$currency} " . number_format($amount, 2);
                            $title   = "💳 Virtual Card Charged";
                            $content = "
                                <p>Your Casjoe Pay Virtual Card has been debited for an online transaction.</p>
                                <table style='width:100%; border-collapse:collapse; margin-top:15px;'>
                                    <tr><td style='padding:8px 0; border-bottom:1px solid #eee;'>Amount</td>
                                        <td style='padding:8px 0; border-bottom:1px solid #eee; text-align:right; font-weight:bold; color:#cc0000;'>{$currency} " . number_format($amount, 2) . "</td></tr>
                                    <tr><td style='padding:8px 0; border-bottom:1px solid #eee;'>Merchant</td>
                                        <td style='padding:8px 0; border-bottom:1px solid #eee; text-align:right;'>" . htmlspecialchars($merchant) . "</td></tr>
                                    <tr><td style='padding:8px 0; border-bottom:1px solid #eee;'>Description</td>
                                        <td style='padding:8px 0; border-bottom:1px solid #eee; text-align:right;'>" . htmlspecialchars($narrative) . "</td></tr>
                                    <tr><td style='padding:8px 0; border-bottom:1px solid #eee;'>Reference</td>
                                        <td style='padding:8px 0; border-bottom:1px solid #eee; text-align:right;'>{$reference}</td></tr>
                                </table>
                            ";
                            NotificationService::sendTransactional($owner['email'], $subject, $title, $content);
                        } catch (\Exception $e) {
                            $this->log("NOTIF_ERR", $e->getMessage());
                        }
                    }
                } else {
                    $this->log("CARD_AUTH_NO_LOCAL", "Card $cardId not found in local cp_virtual_cards");
                }

                echo json_encode(['status' => 'success', 'message' => 'Authorization processed']);
                return;
            }

            // 4. Event: virtualcard.transaction.declined
            if ($event === 'virtualcard.transaction.declined') {
                $reason    = $cardData['reason'] ?? $data['reason'] ?? 'Declined';
                $narrative = $cardData['narrative'] ?? $data['narrative'] ?? 'Transaction Declined';

                $stmtCard = $db->query("SELECT user_id, tenant_id FROM cp_virtual_cards WHERE card_id = ? LIMIT 1", [$cardId]);
                $card = $stmtCard->fetch();

                if ($card) {
                    $desc = "Card Declined: " . $reason . ($narrative ? " ($narrative)" : "");
                    $meta = json_encode([
                        'event'     => $event,
                        'card_id'   => $cardId,
                        'reason'    => $reason,
                        'narrative' => $narrative,
                        'raw'       => substr($raw, 0, 800)
                    ]);

                    $db->query(
                        "INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta, created_at) VALUES (?, ?, ?, 'debit', ?, 'USD', 'failed', ?, ?, NOW())",
                        [$card['tenant_id'], $card['user_id'], $reference, $amount, $desc, $meta]
                    );

                    $userStmt = $db->query("SELECT email, name FROM users WHERE id = ? LIMIT 1", [$card['user_id']]);
                    $owner = $userStmt->fetch();
                    if ($owner && !empty($owner['email'])) {
                        try {
                            $subject = "Virtual Card Transaction Declined";
                            $title   = "⚠️ Card Transaction Declined";
                            $content = "<p>A transaction attempt of <strong>$" . number_format($amount, 2) . " USD</strong> on your virtual card was declined.</p><p><strong>Reason:</strong> " . htmlspecialchars($reason) . "</p><p><strong>Details:</strong> " . htmlspecialchars($narrative) . "</p>";
                            NotificationService::sendTransactional($owner['email'], $subject, $title, $content);
                        } catch (\Exception $e) {
                            $this->log("NOTIF_ERR", $e->getMessage());
                        }
                    }
                }

                $this->log("CARD_DECLINED", "cardId: $cardId | reason: $reason | narrative: $narrative");
                echo json_encode(['status' => 'success', 'message' => 'Decline event logged']);
                return;
            }

            // 5. Event: virtualcard.transaction.declined.terminated
            if ($event === 'virtualcard.transaction.declined.terminated') {
                $db->query("UPDATE cp_virtual_cards SET status = 'terminated' WHERE card_id = ?", [$cardId]);
                $this->log("CARD_TERMINATED", "cardId: $cardId terminated");
                echo json_encode(['status' => 'success', 'message' => 'Card terminated']);
                return;
            }

            // 6. Event: virtualcard.withdrawal.success
            if ($event === 'virtualcard.withdrawal.success') {
                $stmtCard = $db->query("SELECT user_id, tenant_id FROM cp_virtual_cards WHERE card_id = ? LIMIT 1", [$cardId]);
                $card = $stmtCard->fetch();

                if ($card) {
                    $db->query("UPDATE cp_virtual_cards SET balance = GREATEST(0, balance - ?) WHERE card_id = ?", [$amount, $cardId]);
                    $db->query(
                        "INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta, created_at) VALUES (?, ?, ?, 'debit', ?, 'USD', 'successful', 'Virtual Card Withdrawal', ?, NOW())",
                        [$card['tenant_id'], $card['user_id'], $reference, $amount, json_encode($data)]
                    );
                }
                $this->log("CARD_WITHDRAWAL_SUCCESS", "cardId: $cardId | amount: $amount");
                echo json_encode(['status' => 'success', 'message' => 'Withdrawal recorded']);
                return;
            }

            // 7. Event: virtualcard.topup.complete
            if ($event === 'virtualcard.topup.complete') {
                $stmtCard = $db->query("SELECT user_id, tenant_id FROM cp_virtual_cards WHERE card_id = ? LIMIT 1", [$cardId]);
                $card = $stmtCard->fetch();

                if ($card) {
                    $stmtChk = $db->query("SELECT id FROM cp_transactions WHERE reference = ? LIMIT 1", [$reference]);
                    if (!$stmtChk->fetch()) {
                        $db->query("UPDATE cp_virtual_cards SET balance = balance + ? WHERE card_id = ?", [$amount, $cardId]);
                        $db->query(
                            "INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta, created_at) VALUES (?, ?, ?, 'credit', ?, 'USD', 'successful', 'Virtual Card Top-up Completed', ?, NOW())",
                            [$card['tenant_id'], $card['user_id'], $reference, $amount, json_encode($data)]
                        );
                    }
                }
                $this->log("CARD_TOPUP_COMPLETE", "cardId: $cardId | amount: $amount");
                echo json_encode(['status' => 'success', 'message' => 'Top-up completed']);
                return;
            }

            // 8. Event: virtualcard.topup.failed
            if ($event === 'virtualcard.topup.failed') {
                $this->log("CARD_TOPUP_FAILED", "cardId: $cardId | Ref: $reference | Raw: " . substr($raw, 0, 300));
                echo json_encode(['status' => 'success', 'message' => 'Top-up failure recorded']);
                return;
            }

            // 9. Event: otp.code
            if ($event === 'otp.code') {
                $otp   = $cardData['authorizationCode'] ?? $data['authorizationCode'] ?? '';
                $last4 = $cardData['last4'] ?? $data['last4'] ?? '';

                $stmtCard = $db->query("SELECT user_id FROM cp_virtual_cards WHERE card_id = ? LIMIT 1", [$cardId]);
                $card = $stmtCard->fetch();

                if ($card && $otp) {
                    $userStmt = $db->query("SELECT email, name FROM users WHERE id = ? LIMIT 1", [$card['user_id']]);
                    $owner = $userStmt->fetch();
                    if ($owner && !empty($owner['email'])) {
                        try {
                            $subject = "Your Virtual Card One-Time Passcode (OTP): $otp";
                            $title   = "🔑 Virtual Card OTP Code";
                            $content = "
                                <p>Hello " . htmlspecialchars($owner['name'] ?? 'User') . ",</p>
                                <p>Use the following verification code to authenticate your online card transaction (Card ending in <strong>" . htmlspecialchars($last4) . "</strong>):</p>
                                <div style='background:#f4f6f8; padding:15px; text-align:center; font-size:26px; font-weight:bold; letter-spacing:4px; margin:20px 0; color:#000066; border-radius:8px;'>
                                    " . htmlspecialchars($otp) . "
                                </div>
                                <p style='color:#666;'>If you did not initiate this transaction, please freeze your card immediately from your Casjoe Pay dashboard.</p>
                            ";
                            NotificationService::sendTransactional($owner['email'], $subject, $title, $content);
                        } catch (\Exception $e) {
                            $this->log("OTP_EMAIL_ERR", $e->getMessage());
                        }
                    }
                }
                $this->log("CARD_OTP_PROCESSED", "cardId: $cardId | OTP sent");
                echo json_encode(['status' => 'success', 'message' => 'OTP dispatched']);
                return;
            }

            // 10. Legacy Fallback (snake_case card_id + pan)
            $pan = $cardData['card_number'] ?? $cardData['pan'] ?? null;
            if ($cardId && $pan) {
                $cvv       = $cardData['cvv'] ?? $cardData['cvc'] ?? '000';
                $status    = $cardData['card_status'] ?? 'active';
                $maskedPan = substr($pan, 0, 4) . '****' . substr($pan, -4);

                $expiryStr = $cardData['expiration'] ?? $cardData['expiry_date'] ?? null;
                $expM = $expY = null;
                if ($expiryStr) {
                    $parts = explode('/', $expiryStr);
                    $expM  = (int)($parts[0] ?? 0);
                    $expY  = (int)($parts[1] ?? 0);
                    if ($expY < 100) $expY += 2000;
                }

                $sql    = "UPDATE cp_virtual_cards SET pan = ?, masked_pan = ?, cvv = ?, status = ?";
                $params = [$pan, $maskedPan, $cvv, $status];

                if ($expM && $expY) {
                    $sql     .= ", start_month = ?, start_year = ?";
                    $params[] = $expM;
                    $params[] = $expY;
                }

                $sql     .= " WHERE card_id = ?";
                $params[] = $cardId;

                $db->query($sql, $params);
                $this->log("CARD_UPDATED", "card_id: $cardId (Legacy)");

                echo json_encode(['status' => 'success', 'message' => 'Card updated']);
                return;
            }

            if ($cardId) {
                $this->log("CARD_GENERIC_EVENT", "cardId: $cardId | Event: $event | Raw: " . substr($raw, 0, 300));
                echo json_encode(['status' => 'success', 'message' => 'Card event logged']);
                return;
            }

            $this->log("UNHANDLED", "Event: " . ($data['event'] ?? 'unknown') . " | Raw: " . substr($raw, 0, 300));
            echo json_encode(['status' => 'ignored', 'message' => 'Unrecognised event']);

        } catch (\Exception $e) {
            $this->log("CARD_ERROR", $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ── Heuristic: detect deposit payload even without explicit event field ──
    // Covers snake_case (SafeHaven/Amucha) AND camelCase (PAGA) field names
    private function looksLikeDeposit(array $data): bool
    {
        $payload = $data['data'] ?? $data;

        $hasAccount = isset($payload['account_number'])
                   || isset($payload['accountNumber'])
                   || isset($payload['virtual_account_number']);

        $hasAmount  = isset($payload['amount'])
                   || isset($payload['transactionAmount'])
                   || isset($payload['transaction_amount'])
                   || isset($payload['settledAmount'])
                   || isset($payload['settled_amount']);

        return $hasAccount && $hasAmount;
    }

    private function log(string $tag, string $msg)
    {
        $line = "[" . date('Y-m-d H:i:s') . "] [$tag] $msg\n";
        @file_put_contents($this->logFile, $line, FILE_APPEND);
    }
}
