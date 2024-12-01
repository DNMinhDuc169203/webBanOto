<?php
// Lấy danh sách nhà sản xuất cho form thêm/sửa
$stmt = $conn->prepare("SELECT * FROM nhasanxuat ORDER BY tennsx");
$stmt->execute();
$manufacturers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách thương hiệu kèm thông tin nhà sản xuất
$stmt = $conn->prepare("
    SELECT th.*, nsx.tennsx 
    FROM thuonghieu th 
    JOIN nhasanxuat nsx ON th.idnsx = nsx.idnsx 
    ORDER BY th.idth DESC
");
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý thêm thương hiệu mới
if(isset($_POST['add_brand'])) {
    try {
        $tenthuonghieu = $_POST['tenthuonghieu'];
        $idnsx = $_POST['idnsx'];
        
        $stmt = $conn->prepare("INSERT INTO thuonghieu (tenthuonghieu, idnsx) VALUES (?, ?)");
        $stmt->execute([$tenthuonghieu, $idnsx]);
        
        echo "<script>alert('Thêm thương hiệu thành công!'); window.location.href='?page=thuonghieu';</script>";
    } catch(Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}

// Xử lý cập nhật thương hiệu
if(isset($_POST['edit_brand'])) {
    try {
        $idth = $_POST['idth'];
        $tenthuonghieu = $_POST['tenthuonghieu'];
        $idnsx = $_POST['idnsx'];
        
        $stmt = $conn->prepare("UPDATE thuonghieu SET tenthuonghieu = ?, idnsx = ? WHERE idth = ?");
        $stmt->execute([$tenthuonghieu, $idnsx, $idth]);
        
        echo "<script>alert('Cập nhật thương hiệu thành công!'); window.location.href='?page=thuonghieu';</script>";
    } catch(Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}

// Xử lý xóa thương hiệu
if(isset($_GET['delete'])) {
    try {
        $idth = $_GET['delete'];
        
        // Kiểm tra xem có ô tô nào thuộc thương hiệu này không
        $stmt = $conn->prepare("SELECT COUNT(*) FROM oto WHERE idth = ?");
        $stmt->execute([$idth]);
        if($stmt->fetchColumn() > 0) {
            throw new Exception("Không thể xóa thương hiệu này vì đã có xe thuộc thương hiệu!");
        }
        
        $stmt = $conn->prepare("DELETE FROM thuonghieu WHERE idth = ?");
        $stmt->execute([$idth]);
        
        echo "<script>alert('Xóa thương hiệu thành công!'); window.location.href='?page=thuonghieu';</script>";
    } catch(Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Thương hiệu</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBrandModal">
            <i class="fas fa-plus"></i> Thêm thương hiệu
        </button>
    </div>

    <!-- Bảng danh sách thương hiệu -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên thương hiệu</th>
                            <th>Nhà sản xuất</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($brands as $brand): ?>
                        <tr>
                            <td><?php echo $brand['idth']; ?></td>
                            <td><?php echo $brand['tenthuonghieu']; ?></td>
                            <td><?php echo $brand['tennsx']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-brand" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editBrandModal"
                                        data-id="<?php echo $brand['idth']; ?>"
                                        data-name="<?php echo $brand['tenthuonghieu']; ?>"
                                        data-nsx="<?php echo $brand['idnsx']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?page=thuonghieu&delete=<?php echo $brand['idth']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bạn có chắc muốn xóa thương hiệu này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal thêm thương hiệu -->
    <div class="modal fade" id="addBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm thương hiệu mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên thương hiệu</label>
                            <input type="text" class="form-control" name="tenthuonghieu" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nhà sản xuất</label>
                            <select class="form-select" name="idnsx" required>
                                <option value="">Chọn nhà sản xuất</option>
                                <?php foreach($manufacturers as $manufacturer): ?>
                                <option value="<?php echo $manufacturer['idnsx']; ?>">
                                    <?php echo $manufacturer['tennsx']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="add_brand" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal sửa thương hiệu -->
    <div class="modal fade" id="editBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa thương hiệu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="idth" id="edit_idth">
                        <div class="mb-3">
                            <label class="form-label">Tên thương hiệu</label>
                            <input type="text" class="form-control" name="tenthuonghieu" id="edit_tenthuonghieu" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nhà sản xuất</label>
                            <select class="form-select" name="idnsx" id="edit_idnsx" required>
                                <option value="">Chọn nhà sản xuất</option>
                                <?php foreach($manufacturers as $manufacturer): ?>
                                <option value="<?php echo $manufacturer['idnsx']; ?>">
                                    <?php echo $manufacturer['tennsx']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="edit_brand" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Xử lý sự kiện khi click nút sửa
document.querySelectorAll('.edit-brand').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        const nsx = this.dataset.nsx;
        
        document.getElementById('edit_idth').value = id;
        document.getElementById('edit_tenthuonghieu').value = name;
        document.getElementById('edit_idnsx').value = nsx;
    });
});
</script>
 