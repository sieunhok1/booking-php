<?php
require "./vendor/autoload.php";

use Twilio\Rest\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Retrieve environment variables
$accountSid = getenv('TWILIO_ACCOUNT_SID');
$authToken = getenv('TWILIO_AUTH_TOKEN');
$twilioNumber = getenv('TWILIO_PHONE_NUMBER');
$statusCallback = getenv('TWILIO_STATUS_CALLBACK');

$twilio = new Client($accountSid, $authToken);

try {
    $message = $twilio->messages->create(
        "+7143407009", // to
        [
            "body" => "First SMS from Twilio to +15075705611!",
            "from" => $twilioNumber,
            "statusCallback" => $statusCallback // optional
        ]
    );
    echo "Message sent!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
