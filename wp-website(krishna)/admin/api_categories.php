<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM `categories` ORDER BY name ASC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'create' || $action === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data) $data = $_POST;
    
    if ($action === 'create') {
        $stmt = $pdo->prepare("INSERT INTO `categories` (`name`, `icon`, `color`) VALUES (?, ?, ?)");
        $res = $stmt->execute([$data['name'], $data['icon'], $data['color']]);
    } else {
        $stmt = $pdo->prepare("UPDATE `categories` SET `name`=?, `icon`=?, `color`=? WHERE `id`=?");
        $res = $stmt->execute([$data['name'], $data['icon'], $data['color'], $data['id']]);
    }

    if ($res) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to save category data']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM `categories` WHERE `id`=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to delete category']);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
