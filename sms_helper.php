<?php
require_once __DIR__ . '/sms_config.php';

function sendSmsNotification(string $message): void
{
    global $sms_enabled, $sms_account_sid, $sms_auth_token, $sms_from_number, $sms_owner_number;

    if (empty($sms_enabled) || !$sms_enabled) {
        return; // SMS disabled, do nothing
    }

    if (!$sms_account_sid || !$sms_auth_token || !$sms_from_number || !$sms_owner_number) {
        return; // Missing config
    }

    $url = "https://api.twilio.com/2010-04-01/Accounts/{$sms_account_sid}/Messages.json";

    $data = http_build_query([
        'To'   => $sms_owner_number,
        'From' => $sms_from_number,
        'Body' => $message,
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => $sms_account_sid . ':' . $sms_auth_token,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    curl_exec($ch);
    curl_close($ch);
}

