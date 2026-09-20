<?php

namespace App\Core;

class Mailer
{
    public static function sendWithTemplate($to, $subject, $templateName, $data)
    {
        $message = \App\Core\EmailTemplate::render($templateName, $data);
        return self::send($to, $subject, $message, true);
    }

    public static function send($to, $subject, $message, $isRaw = false)
    {
        if (!$isRaw) {
            $message = \App\Core\EmailTemplate::render('generic', [
                'content' => $message,
                'title' => $subject
            ]);
        }

        $mailer = defined('MAIL_MAILER') ? MAIL_MAILER : 'mail';

        if ($mailer === 'smtp') {
            return self::sendSmtp($to, $subject, $message);
        }

        $headers = "From: " . (defined('MAIL_FROM_ADDRESS') ? MAIL_FROM_ADDRESS : "no-reply@" . $_SERVER['HTTP_HOST']) . "\r\n";
        $headers .= "Reply-To: support@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        return mail($to, $subject, $message, $headers);
    }

    private static function sendSmtp($to, $subject, $message)
    {
        $host = MAIL_HOST;
        $port = MAIL_PORT;
        $username = MAIL_USERNAME;
        $password = MAIL_PASSWORD;
        $from = MAIL_FROM_ADDRESS;
        $fromName = MAIL_FROM_NAME;

        if (defined('MAIL_ENCRYPTION') && MAIL_ENCRYPTION === 'ssl') {
            $host = 'ssl://' . $host;
        }

        try {
            $socket = fsockopen($host, $port, $errno, $errstr, 15);
            if (!$socket) {
                error_log("SMTP Connect Failed: $errstr ($errno)");
                return false;
            }
            stream_set_timeout($socket, 10);

            self::serverResponse($socket, "220");
            self::serverCmd($socket, "EHLO app.casjoe.com", "250");

            if (MAIL_ENCRYPTION === 'tls') {
                self::serverCmd($socket, "STARTTLS", "220");
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                self::serverCmd($socket, "EHLO app.casjoe.com", "250");
            }

            self::serverCmd($socket, "AUTH LOGIN", "334");
            self::serverCmd($socket, base64_encode($username), "334");
            self::serverCmd($socket, base64_encode($password), "235");

            self::serverCmd($socket, "MAIL FROM: <$from>", "250");
            self::serverCmd($socket, "RCPT TO: <$to>", "250");

            self::serverCmd($socket, "DATA", "354");

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Date: " . date("r") . "\r\n";
            $headers .= "From: $fromName <$from>\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "Subject: $subject\r\n";

            fwrite($socket, "$headers\r\n$message\r\n.\r\n");
            self::serverResponse($socket, "250");

            self::serverCmd($socket, "QUIT", "221");
            fclose($socket);

            return true;
        } catch (\Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            return false;
        }
    }

    private static function serverCmd($socket, $cmd, $expectedCode = null)
    {
        fwrite($socket, $cmd . "\r\n");
        if ($expectedCode) {
            self::serverResponse($socket, $expectedCode);
        }
    }

    private static function serverResponse($socket, $expectedCode)
    {
        $response = '';
        while (substr($response, 3, 1) != ' ') {
            if (!($line = fgets($socket, 256))) {
                throw new \Exception("No response from SMTP server");
            }
            $response = $line;
        }
        if (substr($response, 0, 3) != $expectedCode) {
            throw new \Exception("SMTP Error: $response");
        }
    }
}
