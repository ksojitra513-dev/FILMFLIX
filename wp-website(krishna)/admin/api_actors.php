<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM `actors` ORDER BY id DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'create') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data) $data = $_POST;
    
    $stmt = $pdo->prepare("INSERT INTO `actors` (`name`, `knownFor`, `role`, `status`) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$data['name'], $data['knownFor'], $data['role'], $data['status']])) {
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['error' => 'Failed to create actor']);
    }
    exit;
}

if ($action === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data) $data = $_POST;
    
    $stmt = $pdo->prepare("UPDATE `actors` SET `name`=?, `knownFor`=?, `role`=?, `status`=? WHERE `id`=?");
    if ($stmt->execute([$data['name'], $data['knownFor'], $data['role'], $data['status'], $data['id']])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update actor']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM `actors` WHERE `id`=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to delete actor']);
    exit;
}

if ($action === 'cycle_status') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT `status` FROM `actors` WHERE `id`=?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        
        $statuses = ['Active', 'Inactive', 'Retired'];
        $next = $statuses[(array_search($current, $statuses) + 1) % 3];
        
        $upd = $pdo->prepare("UPDATE `actors` SET `status`=? WHERE `id`=?");
        if ($upd->execute([$next, $id])) {
            echo json_encode(['success' => true, 'new_status' => $next]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to cycle status']);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
