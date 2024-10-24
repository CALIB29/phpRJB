<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Include PHPMailer classes
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendMail($email, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;
        $mail->Username = 'calibutial20@gmail.com';  // Your email
        $mail->Password = 'ugoduoybbaalydjd';      // Your app password if 2FA is enabled
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient settings
        $mail->setFrom('calibutial20@gmail.com', 'Raph Justine B. Butial');
        $mail->addAddress($email); 

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();
        return "success";
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        $response = "All fields are required";
    } else {
        // Call sendMail function and pass the form data
        $response = sendMail($_POST['email'], $_POST['subject'], $_POST['message']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Notification Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* General styling */
        body {
            background-color: #f7f7f7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .email-container {
            margin: 50px auto;
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .email-container:hover {
            transform: scale(1.01); /* Scale effect on hover */
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #ccc;
            border-radius: 4px;
            padding: 12px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #007BFF; /* Focus border color */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Focus shadow */
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-submit:hover {
            background-color: #218838;
            transform: translateY(-2px); /* Slight lift on hover */
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 28px;
            color: #333;
        }

        .response-message {
            text-align: center;
            margin-top: 20px;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        @media screen and (max-width: 768px) {
            .email-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="email-container">
    <div class="form-header">
        <h2>Send Email Notification</h2>
    </div>

    <form action="" method="post">
        <div class="form-group">
            <label for="email">Recipient Email:</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter recipient email" required>
        </div>

        <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" class="form-control" placeholder="Enter email subject" required>
        </div>

        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" name="message" class="form-control" rows="5" placeholder="Enter your message" required></textarea>
        </div>

        <div class="form-group text-center">
            <button type="submit" name="submit" class="btn-submit">Send Email</button>
        </div>

        <?php if (isset($response)) : ?>
            <div class="response-message">
                <p class="<?= $response == 'success' ? 'text-success' : 'text-danger'; ?>">
                    <?= $response == 'success' ? 'Email sent successfully.' : $response; ?>
                </p>
            </div>
        <?php endif; ?>
    </form>
    <div class="text-center">
        <a href="Home.php" class="btn btn-secondary">Back</a>
    </div>
</div>

</body>
</html>
