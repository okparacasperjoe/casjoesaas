<?php

namespace App\Modules\CasjoePay\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class PaymentRequestController
{
    private $pdo;
    private $tenantId;
    private $userId;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $this->userId = $_SESSION['user_id'];
        
        $stmt = $this->pdo->prepare("SELECT * FROM cp_payment_requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$this->userId]);
        $requests = $stmt->fetchAll();

        require __DIR__ . '/../Views/requests/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $this->userId = $_SESSION['user_id'];
        $this->tenantId = TenantContext::getTenantId();

        $recipient_email = $_POST['recipient_email'];
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $description = $_POST['description'] ?? 'Payment Request';
        $reference = 'REQ-' . strtoupper(uniqid());

        $stmt = $this->pdo->prepare("INSERT INTO cp_payment_requests (tenant_id, user_id, recipient_email, amount, currency, description, reference, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$this->tenantId, $this->userId, $recipient_email, $amount, $currency, $description, $reference]);

        // Auto Email Dispatch
        $paymentLink = "https://" . $_SERVER['HTTP_HOST'] . "/pay/request/" . $reference;
        
        // This simulates sending the email via Casjoe's email provider.
        // It will auto-email the recipient with the payment link.
        $subject = "Payment Request from Casjoe Pay";
        $message = "You have received a payment request for $currency $amount.\n\nDescription: $description\n\nPlease pay using this link: $paymentLink";
        
        // Use PHP mail function (or your Mailer class) to automatically email the client
        // mail($recipient_email, $subject, $message, "From: noreply@casjoe.com");

        header("Location: /pay/requests?success=1");
    }

    public function edit($params)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $id = $params['id'] ?? 0;
        
        $stmt = $this->pdo->prepare("SELECT * FROM cp_payment_requests WHERE id = ? AND user_id = ? AND status = 'pending'");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $request = $stmt->fetch();

        if (!$request) {
            header('Location: /pay/requests');
            exit;
        }

        require __DIR__ . '/../Views/requests/edit.php';
    }

    public function update($params)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $id = $params['id'] ?? 0;
        
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $description = $_POST['description'];

        $stmt = $this->pdo->prepare("UPDATE cp_payment_requests SET amount = ?, currency = ?, description = ? WHERE id = ? AND user_id = ? AND status = 'pending'");
        $stmt->execute([$amount, $currency, $description, $id, $_SESSION['user_id']]);

        header('Location: /pay/requests?success=1');
        exit;
    }

    public function delete($params)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $id = $params['id'] ?? 0;

        $stmt = $this->pdo->prepare("DELETE FROM cp_payment_requests WHERE id = ? AND user_id = ? AND status = 'pending'");
        $stmt->execute([$id, $_SESSION['user_id']]);

        header('Location: /pay/requests?success=1');
        exit;
    }

    public function pay($params)
    {
        $reference = $params['reference'];
        $stmt = $this->pdo->prepare("SELECT * FROM cp_payment_requests WHERE reference = ?");
        $stmt->execute([$reference]);
        $request = $stmt->fetch();

        if (!$request) {
            http_response_code(404);
            die("Payment Request Not Found");
        }

        // We can reuse the public_pay.php view
        $link = [
            'title' => $request['description'],
            'amount' => $request['amount'],
            'currency' => $request['currency'],
            'slug' => $request['reference'],
            'is_request' => true,
            'status' => $request['status']
        ];

        require __DIR__ . '/../Views/public_pay.php';
    }

    public function processPayment($params)
    {
        $reference = $params['reference'];
        $stmt = $this->pdo->prepare("SELECT * FROM cp_payment_requests WHERE reference = ?");
        $stmt->execute([$reference]);
        $request = $stmt->fetch();

        if (!$request) {
            http_response_code(404);
            die("Payment Request Not Found");
        }

        if ($request['status'] == 'paid') {
            die("This request has already been paid.");
        }

        $customerEmail = $_POST['email'] ?? $request['recipient_email'];
        $amount = $request['amount'];
        $currency = $request['currency'];
        $tx_ref = 'PRQ-' . uniqid();

        // 1. Log pending transaction
        $stmt = $this->pdo->prepare("INSERT INTO cp_transactions (tenant_id, user_id, reference, type, amount, currency, status, description, meta) VALUES (?, ?, ?, 'credit', ?, ?, 'pending', ?, ?)");
        $meta = json_encode(['action' => 'payment_request', 'request_ref' => $reference, 'customer' => $customerEmail]);
        $stmt->execute([$request['tenant_id'], $request['user_id'], $tx_ref, $amount, $currency, 'Payment for Request ' . $reference, $meta]);

        // 2. Redirect to Gateway callback (Mocked success)
        header("Location: /pay/request/verify?tx_ref=$tx_ref&status=successful");
    }

    public function verify($params)
    {
        $tx_ref = $_GET['tx_ref'] ?? '';
        $status = $_GET['status'] ?? 'failed';

        if ($status == 'successful') {
            $this->pdo = Database::getInstance()->getConnection();
            $stmt = $this->pdo->prepare("UPDATE cp_transactions SET status = 'successful' WHERE reference = ? AND status = 'pending'");
            $stmt->execute([$tx_ref]);
            
            if ($stmt->rowCount() > 0) {
                $stmt = $this->pdo->prepare("SELECT amount, currency, user_id, meta FROM cp_transactions WHERE reference = ?");
                $stmt->execute([$tx_ref]);
                $txn = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                $meta = json_decode($txn['meta'], true);
                $request_ref = $meta['request_ref'] ?? '';

                if ($request_ref) {
                    $stmt = $this->pdo->prepare("UPDATE cp_payment_requests SET status = 'paid' WHERE reference = ?");
                    $stmt->execute([$request_ref]);
                }

                $stmt = $this->pdo->prepare("UPDATE cp_wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?");
                $stmt->execute([$txn['amount'], $txn['user_id'], $txn['currency']]);

                echo "Payment Successful! The merchant has been credited.";
                exit;
            }
        }
        echo "Payment failed or already processed.";
    }
}
