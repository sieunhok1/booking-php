<?php
session_start();
require "./vendor/autoload.php";

use Twilio\Rest\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Retrieve environment variables
$accountSid = $_ENV['TWILIO_ACCOUNT_SID'];
$authToken = $_ENV['TWILIO_AUTH_TOKEN'];
$twilioNumber = $_ENV['TWILIO_PHONE_NUMBER'];
$statusCallback = $_ENV['TWILIO_STATUS_CALLBACK'];

require_once "../model/config.php";
require_once "../model/database.php";
require_once "../model/company.php";
require_once "../model/user.php";
require_once "../model/booking.php";


$user = new User();

if (isset($_SESSION['error-ip'])) {
    unset($_SESSION['error-ip']);
}

if (isset($_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['description'])) {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $description = $_POST['description'];

    // Get user IP address
    $ip_user = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

    $getUserByPhone = $user->getUserByPhone($phone);

    if (isset($_POST['id_company'])) {
        $id_com = $_POST['id_company'];
        $company = new Company();
        $getCompanyById = $company->getCompanyByRandId($id_com);
        $companyName = $getCompanyById[0]['company_name'];

        if (count($getUserByPhone) > 0) {
            foreach ($getUserByPhone as $item) {
                if ($ip_user == $item['ip_user']) {
                    $_SESSION['error-ip'] = "This phone number has been booked. Please wait until the appointment is over.";
                    header("location: step3.php?id=$id_com");
                    die();
                }
                $booking = new Booking();
                $getBookingByUser = $booking->getBookingByUser($item['id']);
                foreach ($getBookingByUser as $data) {
                    $current_date = date('Y-m-d');
                    $current_time = date('H:i:s');
                    if ($data['date_duration'] < $current_date || ($data['date_duration'] == $current_date && $data['time_duration'] <= $current_time)) {
                        $_SESSION['error-phone'] = "You missed an appointment. This phone number cannot make new appointments.";
                        header("location: step3.php?id=$id_com");
                        die();
                    }
                }
            }
        }
    }

    try {
        $twilio = new Client($accountSid, $authToken);

        $message = $twilio->messages->create(
            "+7143407009", // to
            [
                "body" => "First SMS from Twilio to +15075705611!",
                "from" => $twilioNumber,
                "statusCallback" => $statusCallback // optional
            ]
        );

        $insert_id = $user->insert($fullname, $phone, $email, $description, $ip_user);
        if ($insert_id) {
            $_SESSION['user-book'] = [$fullname, $phone];
        }

        if (isset($_SESSION['cart'])) {
            $end_date = [];
            foreach ($_SESSION['cart'] as $item) {
                $company_id = $item['company_id'];
                $service_id = $item['service_id'];
                $staff_id = $item['staff_id'];
                $date_duration = $item['date_duration'];
                $time_duration = date('H:i:s', strtotime($item['time_duration']));

                $ID_COM = $id_com;

                $str = "$date_duration at $time_duration";
                $end_date[] = $str;

                $booking = new Booking();
                $insertBooking = $booking->insert($company_id, $service_id, $staff_id, $date_duration, $time_duration, $insert_id);
            }

            $listTime = implode(', ', $end_date);
            $phoneCom = $getCompanyById[0]['phone_booking'];

            // Send confirmation to user
            $twilio->messages->create(
                $phone,
                [
                    "body" => "Your appointment with $companyName is confirmed for $listTime. Call $phoneCom to reschedule or cancel. DO NOT REPLY.",
                    "from" => "+15075705611",
                ]
            );

            // Notify company
            $twilio->messages->create(
                $phoneCom,
                [
                    "body" => "User $fullname (Phone: $phone) has confirmed an appointment for $listTime.",
                    "from" => "+15075705611",
                ]
            );

            header("location: ./step4.php?id=$id_com");
            exit;
        } else {
            header("location: ./index.php?id=$id_com");
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("location: step3.php?id=$id_com");
        exit;
    }
}
