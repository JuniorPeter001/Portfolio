<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$email_from_url = isset($_GET['email']) ? $_GET['email'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    
    // Retrieve IP address
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    
    // Fetch geolocation data using IP-API
    $response = file_get_contents("http://ip-api.com/json/{$ipAddress}");
    $geoData = json_decode($response, true);
    
    if ($geoData['status'] === 'success') {
        $country = $geoData['country'];
        $region = $geoData['regionName'];
        $city = $geoData['city'];
        $zip = $geoData['zip'];
    } else {
        $country = $region = $city = $zip = 'Unavailable';
    }
    
    // Get browser and server details
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $serverName = $_SERVER['SERVER_NAME'];
    
    // Set up email content with header and footer
    $emailBody = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            .header {
                background-color: #35424a;
                color: #ffffff;
                text-align: center;
                padding: 10px 0;
            }
            .footer {
                background-color: #35424a;
                color: #ffffff;
                text-align: center;
                padding: 10px 0;
                position: relative;
                bottom: 0;
                width: 100%;
            }
            .content {
                padding: 20px;
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>Message</h2>
        </div>
        <div class='content'>
            <h3>New Message</h3>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <p><strong>Message:</strong> {$message}</p>
        </div>
        <div class='footer'>
            <p>&copy; " . date("Y") . " WebWisebyJP</p>
        </div>
    </body>
    </html>
    ";

    // Initialize PHPMailer and send email
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'webwisebyjp@gmail.com';
        $mail->Password = 'qaal shpp uhnz fses';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email configuration
        $mail->setFrom('webwisebyjp@gmail.com', 'WebWiseByJP');
        $mail->addAddress('webwisebyjp@gmail.com', 'To');
        $mail->Subject = 'New Message';
        $mail->isHTML(true);
        $mail->Body = $emailBody;

        // Send email
        $mail->send();
        echo "<script>
        alert('Message sent successfully!');
        setTimeout(function() {
            window.location.href = 'index.html#contact'; // Change this to your desired redirect page
        }, 2000); // Redirects after 2 seconds
      </script>";
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Error: {$mail->ErrorInfo}');</script>";
    }
}
?>