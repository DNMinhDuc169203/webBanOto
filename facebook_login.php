<?php
session_start();
require_once 'config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!empty($data['facebook_id'])) {
        // Kiểm tra xem tài khoản Facebook đã tồn tại chưa
        $stmt = $conn->prepare("SELECT tk.*, kh.hovaten, kh.idkh 
                               FROM taikhoan tk 
                               JOIN khachhang kh ON tk.idkh = kh.idkh 
                               WHERE tk.oauth_provider = 'facebook' 
                               AND tk.oauth_id = ?");
        $stmt->execute([$data['facebook_id']]);
        $user = $stmt->fetch();

        if (!$user) {
            // Tạo khách hàng mới
            $stmt = $conn->prepare("INSERT INTO khachhang (hovaten, ngaysinh, diachi) VALUES (?, CURDATE(), '')");
            $stmt->execute([$data['name']]);
            $idkh = $conn->lastInsertId();

            // Tạo tài khoản mới
            $stmt = $conn->prepare("INSERT INTO taikhoan (tendangnhap, matkhau, idkh, oauth_provider, oauth_id, email) 
                                  VALUES (?, '', ?, 'facebook', ?, ?)");
            $stmt->execute([$data['email'], $idkh, $data['facebook_id'], $data['email']]);

            // Lấy thông tin user mới tạo
            $stmt = $conn->prepare("SELECT tk.*, kh.hovaten, kh.idkh 
                                  FROM taikhoan tk 
                                  JOIN khachhang kh ON tk.idkh = kh.idkh 
                                  WHERE tk.idkh = ?");
            $stmt->execute([$idkh]);
            $user = $stmt->fetch();
        }

        // Tạo session
        $_SESSION['user'] = [
            'id' => $user['idkh'],
            'name' => $user['hovaten'],
            'email' => $user['email']
        ];

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid Facebook data']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 