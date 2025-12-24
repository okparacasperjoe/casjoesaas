<?php

namespace App\Modules\CasjoeMail\Classes;

class SMTPClient
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $socket;

    public function __construct($host, $port, $username, $password, $encryption = 'tls')
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->encryption = $encryption;
    }

    public function send($to, $fromEmail, $fromName, $subject, $body)
    {
        try {
            $this->connect();
            $this->auth();

            $this->sendCommand("MAIL FROM: <$fromEmail>");
            $this->sendCommand("RCPT TO: <$to>");
            $this->sendCommand("DATA");

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "From: $fromName <$fromEmail>\r\n";
            $headers .= "Subject: $subject\r\n";

            $this->sendCommand($headers . "\r\n" . $body . "\r\n.");
            $this->sendCommand("QUIT");

            fclose($this->socket);
            return true;
        } catch (\Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            return false;
        }
    }

    private function connect()
    {
        $protocol = ($this->encryption === 'ssl') ? 'ssl://' : '';
        $this->socket = fsockopen($protocol . $this->host, $this->port, $errno, $errstr, 15);

        if (!$this->socket) {
            throw new \Exception("Could not connect to SMTP host: $errstr ($errno)");
        }

        $this->getResponse();
        $this->sendCommand("EHLO " . $_SERVER['SERVER_NAME']);

        if ($this->encryption === 'tls') {
            $this->sendCommand("STARTTLS");
            stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->sendCommand("EHLO " . $_SERVER['SERVER_NAME']);
        }
    }

    private function auth()
    {
        $this->sendCommand("AUTH LOGIN");
        $this->sendCommand(base64_encode($this->username));
        $this->sendCommand(base64_encode($this->password));
    }

    private function sendCommand($cmd)
    {
        fputs($this->socket, $cmd . "\r\n");
        return $this->getResponse();
    }

    private function getResponse()
    {
        $response = "";
        while ($str = fgets($this->socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") {
                break;
            }
        }
        return $response;
    }
}
