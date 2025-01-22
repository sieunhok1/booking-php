<?php
session_start();
require "../vendor/autoload.php";

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

require_once "../../model/config.php";
require_once "../../model/database.php";
require_once "../../model/company.php";
require_once "../../model/user.php";
require_once "../../model/booking.php";

// Kiểm tra nếu có id booking được truyền vào
if(isset($_GET['id'])){
    $id_book = $_GET['id'];

    $booking = new Booking();
    $getBookingById = $booking->getBookingById($id_book);

    if ($getBookingById) {
        $phoneUser = $getBookingById[0]['phone'];
        $companyHotLine = $getBookingById[0]['hotline'];

        try {
            // Lấy thông tin từ biến môi trường thay vì hardcode
            $accountSid = getenv('TWILIO_ACCOUNT_SID');
            $authToken = getenv('TWILIO_AUTH_TOKEN');
            $twilioNumber = getenv('TWILIO_PHONE_NUMBER');

            // Khởi tạo Twilio Client
            $twilio = new Client($accountSid, $authToken);

            try {
                // Gửi tin nhắn SMS
                $messFirst = $twilio->messages->create(
                    $phoneUser, // Số điện thoại người nhận
                    [
                        "body" => "Sorry we're busy at the time you request the appointment, please make another appointment or call $companyHotLine. DO NOT REPLY TO TEXT, thank you!",
                        "from" => $twilioNumber, // Số điện thoại gửi
                    ]
                );

                $_SESSION['success'] = "Send SMS message successful!";
                header("location: ../list_booking.php");
                exit;

            } catch (Exception $err) {
                $_SESSION['error'] = "Phone number is wrong or invalid. Please check the phone number and try again.";
                header("location: ../list_booking.php");
                exit;
            }

        } catch (TwilioException $err) {
            $_SESSION['error'] = "Something went wrong with Twilio account. Please check the account and try again.";
            header("location: ../list_booking.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Booking not found or invalid. Please try again.";
        header("location: ../list_booking.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Error! No booking ID provided. Please try again later.";
    header("location: ../list_booking.php");
    exit;
}
