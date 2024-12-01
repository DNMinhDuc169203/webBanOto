<?php
// Lấy danh sách ô tô
$stmt = $conn->prepare("
    SELECT o.*, th.tenthuonghieu, nsx.tennsx, ct.dongco, ct.hopso, ct.nhienlieu, ct.chongoi, ct.xuatxu
    FROM oto o
    JOIN thuonghieu th ON o.idth = th.idth
    JOIN nhasanxuat nsx ON th.idnsx = nsx.idnsx
    LEFT JOIN chitietxeoto ct ON o.idoto = ct.idoto
    ORDER BY o.idoto DESC
");
$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Ô tô</h2>
        <a href="?page=oto_edit" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm mới
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tên xe</th>
                            <th>Thương hiệu</th>
                            <th>Nhà sản xuất</th>
                            <th>Giá</th>
                            <th>Thông số</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cars as $car): ?>
                            <tr>
                                <td><?php echo $car['idoto']; ?></td>
                                <td>
                                    <img src="../image/<?php echo $car['img']; ?>" 
                                         alt="<?php echo $car['tenxe']; ?>"
                                         style="max-width: 100px;">
                                </td>
                                <td><?php echo $car['tenxe']; ?></td>
                                <td><?php echo $car['tenthuonghieu']; ?></td>
                                <td><?php echo $car['tennsx']; ?></td>
                                <td><?php echo number_format($car['giaxe'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <small>
                                        <strong>Động cơ:</strong> <?php echo $car['dongco']; ?><br>
                                        <strong>Hộp số:</strong> <?php echo $car['hopso']; ?><br>
                                        <strong>Nhiên liệu:</strong> <?php echo $car['nhienlieu']; ?><br>
                                        <strong>Chỗ ngồi:</strong> <?php echo $car['chongoi']; ?><br>
                                        <strong>Xuất xứ:</strong> <?php echo $car['xuatxu']; ?>
                                    </small>
                                </td>
                                <td>
                                    <a href="?page=oto_edit&id=<?php echo $car['idoto']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="idoto" value="<?php echo $car['idoto']; ?>">
                                        <button type="submit" 
                                                name="delete_car" 
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa xe này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 