<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once 'layout/header.php';

$user_id = $_SESSION['user']['id'];

try {
    // Lấy giỏ hàng từ database
    $stmt = $conn->prepare("
        SELECT g.*, o.tenxe, o.giaxe, o.img 
        FROM giohang g
        JOIN oto o ON g.idoto = o.idoto
        WHERE g.idkh = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cập nhật session
    $_SESSION['carts'][$user_id] = $cart_items;
} catch(PDOException $e) {
    error_log($e->getMessage());
}
?>

<div class="container mt-5">
    <h2>Giỏ hàng của bạn</h2>
    
    <?php if (!isset($_SESSION['carts'][$user_id]) || empty($_SESSION['carts'][$user_id])): ?>
        <div class="alert alert-info">Giỏ hàng của bạn đang trống</div>
        <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên xe</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['carts'][$user_id] as $item): 
                        $subtotal = $item['giaxe'] * $item['soluong'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><img src="image/<?php echo $item['img']; ?>" alt="<?php echo $item['tenxe']; ?>" style="max-width: 100px;"></td>
                            <td><?php echo $item['tenxe']; ?></td>
                            <td><?php echo number_format($item['giaxe'], 0, ',', '.'); ?> VNĐ</td>
                            <td><?php echo $item['soluong']; ?></td>
                            <td><?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</td>
                            <td>
                                <a href="cart_process.php?remove=<?php echo $item['idoto']; ?>" class="btn btn-danger btn-sm">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                        <td><strong><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between mt-3">
            <a href="index.php" class="btn btn-secondary">Tiếp tục mua sắm</a>
            <a href="checkout.php" class="btn btn-primary">Thanh toán</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'layout/footer.php'; ?> 