<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    // Validate dữ liệu
    $errors = [];
    if (empty($name)) $errors[] = "Vui lòng nhập họ tên";
    if (empty($email)) $errors[] = "Vui lòng nhập email";
    if (empty($subject)) $errors[] = "Vui lòng nhập chủ đề";
    if (empty($message)) $errors[] = "Vui lòng nhập nội dung tin nhắn";

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }

    if (empty($errors)) {
        // Gửi email thông báo (bạn cần cấu hình SMTP hoặc mail server)
        $to = "your-email@example.com";
        $email_subject = "Liên hệ mới từ: " . $name;
        $email_body = "Bạn nhận được tin nhắn mới từ form liên hệ.\n\n".
            "Họ tên: $name\n".
            "Email: $email\n".
            "Số điện thoại: $phone\n".
            "Chủ đề: $subject\n".
            "Nội dung:\n$message";

        $headers = "From: $email";

        if(mail($to, $email_subject, $email_body, $headers)) {
            $_SESSION['success'] = "Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau!";
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}

header("Location: contact.php");
exit();
?> 