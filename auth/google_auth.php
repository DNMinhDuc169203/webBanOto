<?php
session_start();
require_once '../config.php';
require_once '../vendor/autoload.php';

// Khởi tạo Google Client
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    try {
        // Lấy token từ code
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token['access_token']);
        
        // Lấy thông tin người dùng
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        $email = $google_account_info->email;
        $name = $google_account_info->name;
        $google_id = $google_account_info->id;

        // Kiểm tra email trong database
        $stmt = $conn->prepare("SELECT tk.*, kh.hovaten, kh.idkh 
                               FROM taikhoan tk 
                               JOIN khachhang kh ON tk.idkh = kh.idkh 
                               WHERE tk.email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Người dùng đã tồn tại
            $_SESSION['user'] = [
                'id' => $user['idkh'],
                'name' => $user['hovaten'],
                'email' => $user['email']
            ];
        } else {
            // Tạo tài khoản mới
            $conn->beginTransaction();
            try {
                // Tạo khách hàng mới
                $stmt = $conn->prepare("INSERT INTO khachhang (hovaten, ngaysinh, diachi) VALUES (?, CURDATE(), '')");
                $stmt->execute([$name]);
                $idkh = $conn->lastInsertId();

                // Tạo tài khoản mới
                $stmt = $conn->prepare("INSERT INTO taikhoan (tendangnhap, matkhau, idkh, oauth_provider, oauth_id, email) 
                                      VALUES (?, '', ?, 'google', ?, ?)");
                $stmt->execute([$email, $idkh, $google_id, $email]);

                $_SESSION['user'] = [
                    'id' => $idkh,
                    'name' => $name,
                    'email' => $email
                ];

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }
        }

        // Load giỏ hàng
        $stmt = $conn->prepare("
            SELECT g.*, o.tenxe, o.giaxe, o.img 
            FROM giohang g
            JOIN oto o ON g.idoto = o.idoto
            WHERE g.idkh = ?
        ");
        $stmt->execute([$_SESSION['user']['id']]);
        $_SESSION['carts'][$_SESSION['user']['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Location: ../index.php');
        exit();
    } catch(Exception $e) {
        $_SESSION['error'] = 'Đăng nhập thất bại: ' . $e->getMessage();
        header('Location: ../log/login.php');
        exit();
    }
} 