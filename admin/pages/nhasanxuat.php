<?php
// Lấy danh sách nhà sản xuất
$stmt = $conn->prepare("SELECT * FROM nhasanxuat ORDER BY idnsx DESC");
$stmt->execute();
$manufacturers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý thêm nhà sản xuất mới
if(isset($_POST['add_manufacturer'])) {
    $tennsx = $_POST['tennsx'];
    $quocgia = $_POST['quocgia'];
    
    $stmt = $conn->prepare("INSERT INTO nhasanxuat (tennsx, quocgia) VALUES (?, ?)");
    if($stmt->execute([$tennsx, $quocgia])) {
        echo "<script>alert('Thêm nhà sản xuất thành công!'); window.location.href='?page=nhasanxuat';</script>";
    }
}

// Xử lý sửa nhà sản xuất
if(isset($_POST['edit_manufacturer'])) {
    $id = $_POST['edit_id'];
    $tennsx = $_POST['edit_tennsx'];
    $quocgia = $_POST['edit_quocgia'];
    
    $stmt = $conn->prepare("UPDATE nhasanxuat SET tennsx = ?, quocgia = ? WHERE idnsx = ?");
    if($stmt->execute([$tennsx, $quocgia, $id])) {
        echo "<script>alert('Cập nhật nhà sản xuất thành công!'); window.location.href='?page=nhasanxuat';</script>";
    }
}

// Xử lý xóa nhà sản xuất
if(isset($_GET['delete'])) {
    $idnsx = $_GET['delete'];
    
    // Kiểm tra xem có thương hiệu nào đang sử dụng nhà sản xuất này không
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM thuonghieu WHERE idnsx = ?");
    $check_stmt->execute([$idnsx]);
    $count = $check_stmt->fetchColumn();
    
    if($count > 0) {
        echo "<script>alert('Không thể xóa! Nhà sản xuất này đang được sử dụng bởi các thương hiệu.'); window.location.href='?page=nhasanxuat';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM nhasanxuat WHERE idnsx = ?");
        if($stmt->execute([$idnsx])) {
            echo "<script>alert('Xóa nhà sản xuất thành công!'); window.location.href='?page=nhasanxuat';</script>";
        }
    }
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Nhà sản xuất</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addManufacturerModal">
            <i class="fas fa-plus"></i> Thêm nhà sản xuất
        </button>
    </div>

    <!-- Bảng danh sách nhà sản xuất -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên nhà sản xuất</th>
                <th>Quốc gia</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($manufacturers as $manufacturer): ?>
            <tr>
                <td><?php echo $manufacturer['idnsx']; ?></td>
                <td><?php echo $manufacturer['tennsx']; ?></td>
                <td><?php echo $manufacturer['quocgia']; ?></td>
                <td>
                    <button class="btn btn-sm btn-warning edit-manufacturer" 
                            data-id="<?php echo $manufacturer['idnsx']; ?>"
                            data-name="<?php echo $manufacturer['tennsx']; ?>"
                            data-country="<?php echo $manufacturer['quocgia']; ?>"
                            data-bs-toggle="modal" 
                            data-bs-target="#editManufacturerModal">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="?page=nhasanxuat&delete=<?php echo $manufacturer['idnsx']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Bạn có chắc muốn xóa nhà sản xuất này?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal thêm nhà sản xuất -->
    <div class="modal fade" id="addManufacturerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm nhà sản xuất mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên nhà sản xuất</label>
                            <input type="text" class="form-control" name="tennsx" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quốc gia</label>
                            <input type="text" class="form-control" name="quocgia" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="add_manufacturer" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal sửa nhà sản xuất -->
    <div class="modal fade" id="editManufacturerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa nhà sản xuất</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Tên nhà sản xuất</label>
                            <input type="text" class="form-control" name="edit_tennsx" id="edit_tennsx" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quốc gia</label>
                            <input type="text" class="form-control" name="edit_quocgia" id="edit_quocgia" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="edit_manufacturer" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Xử lý sự kiện khi nhấn nút sửa
document.querySelectorAll('.edit-manufacturer').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        const country = this.dataset.country;
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_tennsx').value = name;
        document.getElementById('edit_quocgia').value = country;
    });
});
</script>