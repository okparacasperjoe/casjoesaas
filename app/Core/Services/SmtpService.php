<?php

namespace App\Core\Services;

class SmtpService
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $fromEmail;
    private $fromName;
    private $encryption;
    private $socket;

    public function __construct($settings)
    {
        $this->host = $settings['host'] ?? '';
        $this->port = $settings['port'] ?? 587;
        $this->username = $settings['username'] ?? '';
        $this->password = $settings['password'] ?? '';
        $this->fromEmail = $settings['from_email'] ?? '';
        $this->fromName = $settings['from_name'] ?? '';
        $this->encryption = $settings['encryption'] ?? 'tls';
    }

    public function send($to, $subject, $body)
    {
        if (empty($this->host)) {
            throw new \Exception("SMTP Host not configured");
        }

        $protocol = '';
        if ($this->encryption === 'ssl') {
            $protocol = 'ssl://';
        }

        $this->socket = fsockopen($protocol . $this->host, $this->port, $errno, $errstr, 30);
        if (!$this->socket) {
            throw new \Exception("Could not connect to SMTP host: $errstr ($errno)");
        }

        $this->read(); // Greeting

        $this->cmd('EHLO ' . gethostname());

        if ($this->encryption === 'tls') {
            $this->cmd('STARTTLS');
            stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->cmd('EHLO ' . gethostname());
        }

        if (!empty($this->username)) {
            $this->cmd('AUTH LOGIN');
            $this->cmd(base64_encode($this->username));
            $this->cmd(base64_encode($this->password));
        }

        $this->cmd("MAIL FROM: <{$this->fromEmail}>");
        $this->cmd("RCPT TO: <$to>");
        $this->cmd("DATA");

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: $subject\r\n";

        $this->cmd($headers . "\r\n" . $body . "\r\n.");
        $this->cmd("QUIT");

        fclose($this->socket);
        return true;
    }

    private function cmd($cmd)
    {
        fwrite($this->socket, $cmd . "\r\n");
        $response = $this->read();
        // 2xx, 3xx are usually ok. 4xx, 5xx are errors.
        if (substr($response, 0, 1) >= 4) {
            throw new \Exception("SMTP Error: $response");
        }
        return $response;
    }

    private function read()
    {
        $response = '';
        while ($str = fgets($this->socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") {
                break;
            }
        }
        return $response;
    }
}
