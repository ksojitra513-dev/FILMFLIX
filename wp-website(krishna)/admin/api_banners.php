<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM `banners` ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'create') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data) $data = $_POST;
    
    $stmt = $pdo->prepare("INSERT INTO `banners` (`title`, `subtitle`, `imagurl`) VALUES (?, ?, ?)");
    if ($stmt->execute([$data['title'], $data['subtitle'], $data['imagurl']])) {
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['error' => 'Failed to create banner']);
    }
    exit;
}

if ($action === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data) $data = $_POST;
    
    $stmt = $pdo->prepare("UPDATE `banners` SET `title`=?, `subtitle`=?, `imagurl`=? WHERE `id`=?");
    if ($stmt->execute([$data['title'], $data['subtitle'], $data['imagurl'], $data['id']])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update banner']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM `banners` WHERE `id`=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to delete banner']);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
