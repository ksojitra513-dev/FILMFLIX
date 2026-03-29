<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM `movies` ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'create' || $action === 'update') {
    $data = $_POST;
    if (empty($data)) {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
    }
    
    // Default values if missing
    $title = $data['title'] ?? 'Untitled';
    $cat = $data['category'] ?? 'Action';
    $year = (int)($data['year'] ?? date('Y'));
    $desc = $data['description'] ?? '';
    $rating = (float)($data['rating'] ?? 0);
    $trailer = $data['trailer_url'] ?? '';
    $status = $data['status'] ?? 'Pending';
    $type = $data['type'] ?? 'Movie';
    $poster = $data['poster_url'] ?? '';

    // Handle File Upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $filename = time() . '_' . basename($_FILES['poster']['name']);
        $targetFile = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile)) {
            $poster = $filename; // Store relative path
        }
    }

    if ($action === 'create') {
        $stmt = $pdo->prepare("INSERT INTO `movies` (`title`, `category`, `year`, `description`, `rating`, `trailer_url`, `status`, `type`, `poster_url`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $res = $stmt->execute([$title, $cat, $year, $desc, $rating, $trailer, $status, $type, $poster]);
        $resId = $pdo->lastInsertId();
    } else {
        $id = $data['id'] ?? null;
        if (!$id) { echo json_encode(['error' => 'Missing ID for update']); exit; }
        
        $stmt = $pdo->prepare("UPDATE `movies` SET `title`=?, `category`=?, `year`=?, `description`=?, `rating`=?, `trailer_url`=?, `status`=?, `type`=?, `poster_url`=? WHERE `id`=?");
        $res = $stmt->execute([$title, $cat, $year, $desc, $rating, $trailer, $status, $type, $poster, $id]);
        $resId = $id;
    }

    if ($res) {
        echo json_encode(['success' => true, 'id' => (int)$resId, 'poster' => $poster]);
    } else {
        echo json_encode(['error' => 'Failed to save movie data to database']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM `movies` WHERE `id`=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to delete movie']);
    exit;
}

if ($action === 'cycle_status') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT `status` FROM `movies` WHERE `id`=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $order = ['Draft', 'Pending', 'Published'];
            $newStatus = $order[(array_search($row['status'], $order) + 1) % 3];
            $stmt = $pdo->prepare("UPDATE `movies` SET `status`=? WHERE `id`=?");
            if ($stmt->execute([$newStatus, $id])) {
                echo json_encode(['success' => true, 'new_status' => $newStatus]);
                exit;
            }
        }
    }
    echo json_encode(['error' => 'Failed to cycle status']);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
