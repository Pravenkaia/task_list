<?php


namespace models;


class Mailer
{
    private $to_admin = MAIL_ADMIN_BCC;
    private $from = MAIL_FROM;


    /**
     * @param $to
     * @param $mailBody
     * @param string $mailSubj
     * @return bool
     */
    public function sendEmail($to, $mailBody, $mailSubj = '')
    {
        if (!$to || $mailBody) return false;
        $mailSubj = $mailSubj ?? "Confirmation of email sending";

        $headers = "Content-type: text;charset=utf-8 \r\n";
        $headers .= "From: "  . $this->from . "\r\n";
        $headers .= "Bcc: " .  $this->to_admin . "\r\n"; // blind carbon copy

        $success = mail($to, $mailSubj, $mailBody, $headers);

        if ($success) return true;
        return false;
    }

}