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
        $username = $getBookingById[0]['fullname'];
        $company = $getBookingById[0]['company_name'];
        $companyHotLine = $getBookingById[0]['hotline'];
        $phoneUser = $getBookingById[0]['phone'];
        $dateBegin = $getBookingById[0]['date_duration'];

        // Lấy giờ và phút
        $hour_start = substr($getBookingById[0]['time_duration'], 0, 2);
        $minute_start = substr($getBookingById[0]['time_duration'], 3, 2);

        // Xử lý AM/PM
        $period_start = $hour_start >= 12 ? 'PM' : 'AM';
        $hour_start = $hour_start > 12 ? $hour_start - 12 : $hour_start;
        $hour_start = $hour_start < 10 && strlen($hour_start) < 2 ? '0' . $hour_start : $hour_start;

        $timeBegin = $hour_start . ':' . $minute_start . ' ' . $period_start;
        
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
                        "body" => "Hi $username, don't forget your appt at $company on $dateBegin at $timeBegin. To Reschedule or Cancel, please call $companyHotLine. DO NOT REPLY TO TEXT",
                        "from" => $twilioNumber, // Số điện thoại gửi
                    ]
                );

                $_SESSION['success'] = "Send SMS message successful!";
                header("location: ../list_booking.php");
                exit;

            } catch (Exception $err) {
                $_SESSION['error'] = "Phone number is incorrect or invalid. Please check the phone number and try again.";
                header("location: ../list_booking.php");
                exit;
            }

        } catch (TwilioException $err) {
            $_SESSION['error'] = "Something went wrong with the Twilio account. Please check the account and try again.";
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
