<?php
// List all cars with edit/delete functionality
$stmt = $conn->prepare("SELECT o.*, th.tenthuonghieu 
    FROM oto o 
    JOIN thuonghieu th ON o.idth = th.idth 
    ORDER BY o.idoto DESC");
$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Quản lý Ô tô</h2>
        <a href="?page=oto_add" class="btn btn-primary">Thêm xe mới</a>
    </div>
    
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên xe</th>
                <th>Thương hiệu</th>
                <th>Giá</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($cars as $car): ?>
            <tr>
                <td><?php echo $car['idoto']; ?></td>
                <td><?php echo $car['tenxe']; ?></td>
                <td><?php echo $car['tenthuonghieu']; ?></td>
                <td><?php echo number_format($car['giaxe']); ?> VNĐ</td>
                <td>
                    <a href="?page=oto_edit&id=<?php echo $car['idoto']; ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="?page=oto_delete&id=<?php echo $car['idoto']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Bạn có chắc muốn xóa?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div> 