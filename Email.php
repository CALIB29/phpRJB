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
        $mail->Username = 'raphbutial29@gmail.com';  // Your email
        $mail->Password = 'ksiqjsdpnzwpxwlq';      // Your app password if 2FA is enabled
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient settings
        $mail->setFrom('raphbutial29@gmail.com', 'Raph Justine B. Butial');
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
?>



<?php
// Ensure you include the file that defines sendMail


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
        /* Add your styles here */
        body {
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
        }

        .email-container {
            margin: 50px auto;
            width: 50%;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #ccc;
            border-radius: 4px;
            padding: 12px;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #218838;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 28px;
            color: #333;
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
            <p class="<?= $response == 'success' ? 'text-success' : 'text-danger'; ?>">
                <?= $response == 'success' ? 'Email sent successfully.' : $response; ?>
            </p>
        <?php endif; ?>
    </form>
    <a href="Home.php">Back</a>
</div>

</body>
</html>
