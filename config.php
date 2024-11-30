<?php
$servername = "localhost"; // Tên server database
$username = "root";     // Tên đăng nhập database 
$password = "";         // Mật khẩu database
$dbname = "dbxeoto"; // Tên database của bạn

try {
    // Tạo kết nối
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Thiết lập chế độ báo lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    echo "Kết nối thất bại: " . $e->getMessage();
}
?>
