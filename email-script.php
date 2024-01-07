<?php
session_start();

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (isset($_POST['sendMailBtn'])) {
    $host = 'sxxm';
    $dbName = 'xxx';
    $dbUser = 'xxxxxx';
    $dbPassword = 'xxxxx';
    $port = 3306;

    $dsn = "mysql:host=$host;dbname=$dbName;port=$port";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);

        $stmt = $pdo->prepare("INSERT INTO warranty_registration (iso_number, model_name, customer_name, customer_email, 
            customer_mobile, customer_address, customer_city, customer_state, customer_pincode, 
            product_serial, purchase_date, invoice_attachment, warranty_attachment)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Initialize attachment variables
        $emailAttachment1 = null;
        $emailAttachment2 = null;

        // Handle attachments
        handleAttachment_db('invoice', $emailAttachment1);
        handleAttachment_db('warranty', $emailAttachment2);

        $stmt->execute([
            $_POST['iso_number'],
            $_POST['model_name'],
            $_POST['customer_name'],
            $_POST['customer_email'],
            $_POST['customer_mobile'],
            $_POST['customer_address'],
            $_POST['customer_city'],
            $_POST['customer_state'],
            $_POST['customer_pincode'],
            $_POST['product_serial'],
            $_POST['purchase_date'],
            $emailAttachment1,
            $emailAttachment2,
        ]);

   $_SESSION['success_message'] = "Email Message and Form Data have been saved successfully";

        // Display success message
        echo '';


        // Reset form fields using JavaScript
        echo "<script>
                document.getElementById('yourFormId').reset(); // Replace 'yourFormId' with the actual ID of your form
              </script>";

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Form data could not be saved. Error: " . $e->getMessage();
    }

    handleAttachment('invoice', $emailAttachment1);
    handleAttachment('warranty', $emailAttachment2);

    $to = $_POST['customer_email'];
    $subject = $_POST['iso_number']." ".$_POST['customer_name'];
    $message = "Installation Service Order No: " . $_POST['iso_number'] . "\n";
    $message .= "Model Name: " . $_POST['model_name'] . "\n";
    $message .= "Customer Name: " . $_POST['customer_name'] . "\n";
    $message .= "Customer Email: " . $_POST['customer_email'] . "\n";
    $message .= "Customer Mobile: " . $_POST['customer_mobile'] . "\n";
    $message .= "Customer Address: " . $_POST['customer_address'] . "\n";
    $message .= "Customer City: " . $_POST['customer_city'] . "\n";
    $message .= "Customer State: " . $_POST['customer_state'] . "\n";
    $message .= "Customer Pincode: " . $_POST['customer_pincode'] . "\n";
    $message .= "Product Serial Number: " . $_POST['product_serial'] . "\n";
    $message .= "Purchase Date: " . $_POST['purchase_date'] . "\n";

    sendEmail("hr@unbundl.com", $subject, $message, array($emailAttachment1, $emailAttachment2));
    @unlink($emailAttachment1);
    @unlink($emailAttachment2);
}

/**
 * Handles file attachment
 * @param string $fileInputName
 * @param string|null $attachmentVariable
 */
function handleAttachment_db($fileInputName, &$attachmentVariable)
{
    if (isset($_FILES[$fileInputName]['tmp_name'])) {
        $attachmentVariable = file_get_contents($_FILES[$fileInputName]['tmp_name']);

        // Add file type validation for PDF only
        $fileExtension = pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION);

        if (strtolower($fileExtension) !== 'pdf') {
            $_SESSION['error_message'] = "Invalid file type: Only PDF files are allowed";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
}

function handleAttachment($fileInputName, &$attachmentVariable)
{
    if (isset($_FILES[$fileInputName]['tmp_name'])) {
        // Add file type validation for PDF only
        $fileExtension = pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION);

        if (strtolower($fileExtension) !== 'pdf') {
            $_SESSION['error_message'] = "Invalid file type: Only PDF files are allowed";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $attachmentDir = 'uploads/'; // Choose a suitable directory
        $attachmentVariable = $attachmentDir . $_FILES[$fileInputName]['name'];

        if (!file_exists($attachmentDir)) {
            mkdir($attachmentDir, 0777, true);
        }

        move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $attachmentVariable);
    }
}



/**
 * Sends email with attachment
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param array $attachments
 */
function sendEmail($to, $subject, $message, $attachments = array())
{
    try {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = "php.form.submitted@gmail.com";
        $mail->Password = "xxxxxx";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom("php.form.submitted@gmail.com", $subject);
        $mail->addAddress($to, 'Joe User');

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // $mail->SMTPDebug = 2; 

        foreach ($attachments as $attachment) {
            if ($attachment) {
                $mail->addAttachment($attachment);
            }
        }

        $mail->send();
   $_SESSION['success_message'] = "Email Message and Form Data have been saved successfully";

        // Display success message
        echo '
        <div class="card bg-success text-white">
        <div class="demo"> Query Successfully Submitted </div>
    <div class="card-body">
        <h5 class="card-title">Thank You!</h5>
        <p class="card-text">
            Thank you for sharing the documents with us. Our team will verify the details and get back to you within 7 working days. FFIPL reserves the right to reject the warranty application if the registration terms & conditions are not met. Please refer to the product’s user manual for detailed warranty terms & conditions.
        </p>
        <button class="btn btn-light" onclick="redirectToIndex()">OK</button>
        </div>
    </div>';

        // Reset form fields using JavaScript
        echo "<script>
                document.getElementById('yourFormId').reset(); // Replace 'yourFormId' with the actual ID of your form
              </script>";

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Form data could not be saved. Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Add this CSS to your stylesheet or in a <style> tag in your HTML file */

       /* Add this CSS to your stylesheet or in a <style> tag in your HTML file */

body {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
}

.card {
    width: 500px; /* Adjust the width as needed */
    margin: 20px; /* Adjust the margin as needed */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Box shadow for the card */
}

.bg-success {
    background-color: #28a745; /* Green background color */
}

.text-white {
    color: #fff; /* White text color */
}

.card-title {
    font-size: 1.5rem; /* Adjust the font size as needed */
    margin-bottom: 10px; /* Adjust the margin as needed */
}

.card-text {
    font-size: 1rem; /* Adjust the font size as needed */
    margin-bottom: 10px; /* Adjust the margin as needed */
}

.btn-light {
    background-color: #fff; /* White background color for the button */
    color: #28a745; /* Green text color for the button */
    border: 1px solid #28a745; /* Green border for the button */
    padding: 8px 16px; /* Adjust the padding as needed */
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Box shadow for the button */
}

.btn-light:hover {
    background-color: #28a745; /* Green background color on hover */
    color: #fff; /* White text color on hover */
}

    </style>
</head>
<body>
    
</body>
</html>