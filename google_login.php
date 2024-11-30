<?php
session_start();
require_once 'config.php';
require_once 'vendor/autoload.php';

// Đảm bảo header là JSON
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Log function
function logError($message) {
    error_log(date('Y-m-d H:i:s') . ": " . $message . "\n", 3, __DIR__ . '/google_login_error.log');
}

try {
    logError("Starting Google login process");
    
    $input = json_decode(file_get_contents('php://input'), true);
    logError("Received input: " . print_r($input, true));
    
    if (!isset($input['token'])) {
        throw new Exception('Token không hợp lệ');
    }

    $token = $input['token'];
    
    // Decode JWT token manually first
    $tokenParts = explode('.', $token);
    $payload = json_decode(base64_decode($tokenParts[1]), true);
    
    logError("Decoded payload: " . print_r($payload, true));

    if ($payload) {
        $email = $payload['email'];
        $name = $payload['name'] ?? ($payload['given_name'] . ' ' . $payload['family_name']);
        $google_id = $payload['sub'];

        logError("User info - Email: $email, Name: $name, Google ID: $google_id");

        // Kiểm tra kết nối database
        if (!$conn) {
            throw new Exception('Không thể kết nối database');
        }

        // Kiểm tra email trong database
        $stmt = $conn->prepare("SELECT tk.*, kh.hovaten, kh.idkh 
                               FROM taikhoan tk 
                               JOIN khachhang kh ON tk.idkh = kh.idkh 
                               WHERE tk.email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        logError("Database query result: " . print_r($user, true));

        if ($user) {
            // Người dùng đã tồn tại
            $_SESSION['user'] = [
                'id' => $user['idkh'],
                'name' => $user['hovaten'],
                'email' => $user['email']
            ];
            logError("Existing user logged in: " . print_r($_SESSION['user'], true));
        } else {
            // Tạo tài khoản mới
            $conn->beginTransaction();
            try {
                logError("Creating new user account");
                
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
                logError("New user created: " . print_r($_SESSION['user'], true));
            } catch (Exception $e) {
                $conn->rollBack();
                logError("Error creating new user: " . $e->getMessage());
                throw $e;
            }
        }

        // Load giỏ hàng
        try {
            $stmt = $conn->prepare("
                SELECT g.*, o.tenxe, o.giaxe, o.img 
                FROM giohang g
                JOIN oto o ON g.idoto = o.idoto
                WHERE g.idkh = ?
            ");
            $stmt->execute([$_SESSION['user']['id']]);
            $_SESSION['carts'][$_SESSION['user']['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            logError("Cart loaded successfully");
        } catch(PDOException $e) {
            logError("Error loading cart: " . $e->getMessage());
        }

        $response = [
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'user' => [
                'name' => $_SESSION['user']['name'],
                'email' => $_SESSION['user']['email']
            ]
        ];
        logError("Sending success response: " . print_r($response, true));
        echo json_encode($response);
    } else {
        throw new Exception('Không thể xác thực token');
    }
} catch(Exception $e) {
    logError("Fatal error: " . $e->getMessage());
    logError("Stack trace: " . $e->getTraceAsString());
    
    $errorResponse = [
        'success' => false,
        'error' => $e->getMessage()
    ];
    echo json_encode($errorResponse);
}