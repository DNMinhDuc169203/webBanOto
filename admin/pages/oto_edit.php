<?php
// Lấy danh sách thương hiệu
$stmt = $conn->prepare("SELECT * FROM thuonghieu ORDER BY tenthuonghieu");
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nếu là sửa, lấy thông tin xe
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("
        SELECT o.*, ct.*
        FROM oto o
        LEFT JOIN chitietxeoto ct ON o.idoto = ct.idoto
        WHERE o.idoto = ?
    ");
    $stmt->execute([$_GET['id']]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Xử lý thêm/sửa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->beginTransaction();
        
        $tenxe = $_POST['tenxe'];
        $idth = $_POST['idth'];
        $giaxe = $_POST['giaxe'];
        $mausac = $_POST['mausac'];
        $dongxe = $_POST['dongxe'];
        $ngaysanxuat = $_POST['ngaysanxuat'];
        
        // Xử lý upload ảnh
        $img = '';
        if (!empty($_FILES['img']['name'])) {
            $target_dir = "../image/";
            $img = time() . '_' . basename($_FILES['img']['name']);
            $target_file = $target_dir . $img;
            
            if (!move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
                throw new Exception("Lỗi upload file!");
            }
        } elseif (isset($car)) {
            $img = $car['img'];
        }
        
        if (isset($_GET['id'])) {
            // Cập nhật ô tô
            $stmt = $conn->prepare("
                UPDATE oto 
                SET tenxe = ?, idth = ?, giaxe = ?, mausac = ?, dongxe = ?, img = ?, ngaysanxuat = ?
                WHERE idoto = ?
            ");
            $stmt->execute([$tenxe, $idth, $giaxe, $mausac, $dongxe, $img, $ngaysanxuat, $_GET['id']]);
            
            // Cập nhật chi tiết
            $stmt = $conn->prepare("
                UPDATE chitietxeoto 
                SET dongco = ?, hopso = ?, nhienlieu = ?, chongoi = ?, xuatxu = ?, mota = ?
                WHERE idoto = ?
            ");
            $stmt->execute([
                $_POST['dongco'],
                $_POST['hopso'],
                $_POST['nhienlieu'],
                $_POST['chongoi'],
                $_POST['xuatxu'],
                $_POST['mota'],
                $_GET['id']
            ]);
        } else {
            // Thêm ô tô mới
            $stmt = $conn->prepare("
                INSERT INTO oto (tenxe, idth, giaxe, mausac, dongxe, img, ngaysanxuat)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$tenxe, $idth, $giaxe, $mausac, $dongxe, $img, $ngaysanxuat]);
            
            $idoto = $conn->lastInsertId();
            
            // Thêm chi tiết
            $stmt = $conn->prepare("
                INSERT INTO chitietxeoto (idoto, dongco, hopso, nhienlieu, chongoi, xuatxu, mota)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $idoto,
                $_POST['dongco'],
                $_POST['hopso'],
                $_POST['nhienlieu'],
                $_POST['chongoi'],
                $_POST['xuatxu'],
                $_POST['mota']
            ]);
        }
        
        $conn->commit();
        echo "<script>alert('Lưu thành công!'); window.location.href='?page=oto';</script>";
    } catch(Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <?php echo isset($_GET['id']) ? "Sửa thông tin xe" : "Thêm xe mới"; ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Thông tin cơ bản -->
                    <div class="col-md-6">
                        <h6>Thông tin cơ bản</h6>
                        <div class="mb-3">
                            <label class="form-label">Tên xe</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="tenxe" 
                                   value="<?php echo isset($car) ? $car['tenxe'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thương hiệu</label>
                            <select class="form-select" name="idth" required>
                                <option value="">Chọn thương hiệu</option>
                                <?php foreach($brands as $brand): ?>
                                    <option value="<?php echo $brand['idth']; ?>"
                                            <?php echo isset($car) && $car['idth'] == $brand['idth'] ? 'selected' : ''; ?>>
                                        <?php echo $brand['tenthuonghieu']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá xe</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="giaxe" 
                                   value="<?php echo isset($car) ? $car['giaxe'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Màu sắc</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="mausac" 
                                   value="<?php echo isset($car) ? $car['mausac'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dòng xe</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="dongxe" 
                                   value="<?php echo isset($car) ? $car['dongxe'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày sản xuất</label>
                            <input type="date" 
                                   class="form-control" 
                                   name="ngaysanxuat" 
                                   value="<?php echo isset($car) ? $car['ngaysanxuat'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>
                            <?php if(isset($car) && $car['img']): ?>
                                <div class="mb-2">
                                    <img src="../image/<?php echo $car['img']; ?>" 
                                         alt="<?php echo $car['tenxe']; ?>" 
                                         class="img-thumbnail"
                                         style="max-width: 200px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" 
                                   class="form-control" 
                                   name="img" 
                                   accept="image/*"
                                   <?php echo !isset($car) ? 'required' : ''; ?>>
                            <small class="text-muted">
                                <?php if(isset($car)): ?>
                                    Để trống nếu không muốn thay đổi ảnh
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>

                    <!-- Thông số kỹ thuật -->
                    <div class="col-md-6">
                        <h6>Thông số kỹ thuật</h6>
                        <div class="mb-3">
                            <label class="form-label">Động cơ</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="dongco" 
                                   value="<?php echo isset($car) ? $car['dongco'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hộp số</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="hopso" 
                                   value="<?php echo isset($car) ? $car['hopso'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nhiên liệu</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="nhienlieu" 
                                   value="<?php echo isset($car) ? $car['nhienlieu'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số chỗ ngồi</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="chongoi" 
                                   value="<?php echo isset($car) ? $car['chongoi'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Xuất xứ</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="xuatxu" 
                                   value="<?php echo isset($car) ? $car['xuatxu'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" 
                                      name="mota" 
                                      rows="4"><?php echo isset($car) ? $car['mota'] : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <a href="?page=oto" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 