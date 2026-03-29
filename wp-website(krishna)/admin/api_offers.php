<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM `offers` ORDER BY id DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'create') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data) $data = $_POST;
    
    $stmt = $pdo->prepare("INSERT INTO `offers` (`code`, `desc`, `status`, `color`, `exp`) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$data['code'], $data['desc'], $data['status'], $data['color'], $data['exp']])) {
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['error' => 'Failed to create offer']);
    }
    exit;
}

if ($action === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data) $data = $_POST;
    
    $stmt = $pdo->prepare("UPDATE `offers` SET `code`=?, `desc`=?, `status`=?, `color`=?, `exp`=? WHERE `id`=?");
    if ($stmt->execute([$data['code'], $data['desc'], $data['status'], $data['color'], $data['exp'], $data['id']])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update offer']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM `offers` WHERE `id`=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to delete offer']);
    exit;
}

if ($action === 'toggle_status') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT `status` FROM `offers` WHERE `id`=?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        
        $next = $current === 'Active' ? 'Expired' : 'Active';
        
        $upd = $pdo->prepare("UPDATE `offers` SET `status`=? WHERE `id`=?");
        if ($upd->execute([$next, $id])) {
            echo json_encode(['success' => true, 'new_status' => $next]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to toggle status']);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
