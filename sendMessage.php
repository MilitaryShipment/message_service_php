<?php

class SendMessage
{
    public $debug = true;

    public function __construct($to = array(), $from = '', $fromName = '', $replyTo = '', $cc = array(), $bcc = array(), $subject = '', $body = '', $attachments = array(),$isHTML = true)
    {
        $this->to = $to;
        $this->from = $from;
        $this->fromName = $fromName;
        $this->replyTo = $replyTo;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->subject = $subject;
        $this->body = $body;
        $this->attachments = $attachments;
        $this->isHTML = $isHTML;
        $this->send();
    }

    public function send()
    {
        define('PHP_MAILER', __DIR__ . '/class.phpmailer.php');
        define('EMAIL_HOST', "mail.allamericanmoving.com");
        define('MAX_EMAIL_SIZE', 12500000);
        require_once(PHP_MAILER);

        // Prepare email to be sent
        $email = new phpmailer();
        $email->CharSet = "UTF-8";
        $email->Host = EMAIL_HOST;
        $email->imaper = "smtp";

        // From
        $email->From = $this->from;
        $email->FromName = $this->from;
        if (isset($this->replyTo) && !empty($this->replyTo)) {
            $email->AddReplyTo($this->replyTo);
        }

        // To
        if (is_array($this->to)) {
            $to = array_unique($this->to);
            $to = array_values($this->to);
            foreach ($this->to as $t) {
                $email->AddAddress($t);
            }
        } else {
            $email->AddAddress($this->to);
        }

        // Carbon Copies
        if (isset($this->cc) && !empty($this->cc)) {
            if (is_array($this->cc)) {
                foreach ($this->cc as $c) {
                    $email->AddCC($c);
                }
            } else {
                $email->AddCC($this->cc);
            }
        }

        // Blind Carbon Copies
        if (isset($this->bcc) && !empty($this->bcc)) {
            if (is_array($this->bcc)) {
                foreach ($this->bcc as $b) {
                    $email->AddBCC($b);
                }
            } else {
                $email->AddBCC($this->bcc);
            }
        }

        // Subject and body
        $email->Subject = $this->subject;
        $email->isHTML($this->isHTML);
        $email->Body = $this->body;
        $email->SingleTo = true;

        // Add Attachments
        if (isset($this->attachments) && !empty($this->attachments)) {
            if (is_array($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $email->AddAttachment($attachment, basename($attachment), $encoding = 'base64', $type = 'application/pdf');
                }
            } else {
                $email->AddAttachment($this->attachments, basename($this->attachments), $encoding = 'base64', $type = 'application/pdf');
            }
        }

        // Send email
        $basename = basename(__FILE__);
        //$email->IsSendmail();

        if (!$email->Send()) {
            echo "Error sending: " . $email->ErrorInfo . "<br>";
        } else {
            //echo "E-mail sent" . "<br>";
        }

        // Clear up
        $email->ClearAddresses();
        $email->ClearAttachments();

        return $this;
    }

} // end class

