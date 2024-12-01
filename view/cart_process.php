<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../log/login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

if (isset($_POST['add_to_cart'])) {
    $item = [
        'idoto' => $_POST['idoto'],
        'tenxe' => $_POST['tenxe'],
        'giaxe' => $_POST['giaxe'],
        'img' => $_POST['img'],
        'soluong' => 1
    ];
    
    try {
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $stmt = $conn->prepare("SELECT soluong FROM giohang WHERE idkh = ? AND idoto = ?");
        $stmt->execute([$user_id, $item['idoto']]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Cập nhật số lượng
            $stmt = $conn->prepare("UPDATE giohang SET soluong = soluong + 1 WHERE idkh = ? AND idoto = ?");
            $stmt->execute([$user_id, $item['idoto']]);
        } else {
            // Thêm mới vào giỏ hàng
            $stmt = $conn->prepare("INSERT INTO giohang (idkh, idoto, soluong) VALUES (?, ?, 1)");
            $stmt->execute([$user_id, $item['idoto']]);
        }

        // Cập nhật session
        if (!isset($_SESSION['carts'][$user_id])) {
            $_SESSION['carts'][$user_id] = [];
        }
        $_SESSION['carts'][$user_id][] = $item;

    } catch(PDOException $e) {
        error_log($e->getMessage());
    }
    
    header('Location: cart.php');
    exit();
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['remove'])) {
    $idoto = $_GET['remove'];
    
    try {
        // Xóa sản phẩm khỏi database
        $stmt = $conn->prepare("DELETE FROM giohang WHERE idkh = ? AND idoto = ?");
        $stmt->execute([$user_id, $idoto]);
        
        // Xóa sản phẩm khỏi session
        if (isset($_SESSION['carts'][$user_id])) {
            foreach ($_SESSION['carts'][$user_id] as $key => $item) {
                if ($item['idoto'] == $idoto) {
                    unset($_SESSION['carts'][$user_id][$key]);
                    break;
                }
            }
            // Reindex array sau khi xóa
            $_SESSION['carts'][$user_id] = array_values($_SESSION['carts'][$user_id]);
        }
        
    } catch(PDOException $e) {
        error_log($e->getMessage());
    }
    
    header('Location: cart.php');
    exit();
} 