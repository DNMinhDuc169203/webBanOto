<?php
require_once 'config.php';

try {
    if(isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
        
        // Tìm kiếm trong bảng thuonghieu
        $stmt = $conn->prepare("
            SELECT o.* 
            FROM oto o
            INNER JOIN thuonghieu th ON o.idth = th.idth
            WHERE th.tenthuonghieu LIKE :keyword
        ");
        
        $stmt->execute(['keyword' => "%$keyword%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Trả về kết quả dạng JSON
        header('Content-Type: application/json');
        echo json_encode($results);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 