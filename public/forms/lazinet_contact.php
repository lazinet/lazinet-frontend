<?php
  // Địa chỉ email mà bạn muốn nhận thông tin từ form
  $receiving_email_address = 'your_email@example.com';

  // Kiểm tra nếu form được gửi
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Kiểm tra tính hợp lệ của dữ liệu
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
      // Cấu trúc email
      $to = $receiving_email_address;
      $headers = "From: $email\r\n";
      $headers .= "Reply-To: $email\r\n";
      $email_subject = "New Contact Message: $subject";
      $email_body = "You have received a new message from $name.\n\n".
                    "Here are the details:\n\n".
                    "Name: $name\n\n".
                    "Email: $email\n\n".
                    "Message:\n\n$message";

      // Gửi email
      if (mail($to, $email_subject, $email_body, $headers)) {
        echo "Your message has been sent. Thank you!";
      } else {
        echo "There was a problem sending the email.";
      }
    } else {
      echo "Please fill in all fields.";
    }
  }
?>
