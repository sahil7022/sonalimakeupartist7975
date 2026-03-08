<?php
// Configure your SMS provider here (example uses Twilio).
// 1. Create a Twilio account (https://www.twilio.com/)
// 2. Get your Account SID, Auth Token, and a Twilio phone number.
// 3. Fill in the values below.

$sms_enabled    = false; // set to true after adding real credentials
$sms_account_sid = '';   // e.g. 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
$sms_auth_token  = '';   // e.g. 'your_auth_token'
$sms_from_number = '';   // e.g. '+1xxxxxxxxxx' (Twilio number)

// Destination number for notifications (owner/admin)
// Use full international format, e.g. '+916362781833'
$sms_owner_number = '+916362781833';

