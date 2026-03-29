<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

// Read pending comments
if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM `comments` WHERE `status`='Pending' ORDER BY id ASC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'approve') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("UPDATE `comments` SET `status`='Approved' WHERE `id`=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to approve comment']);
    exit;
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM `comments` WHERE `id`=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to delete comment']);
    exit;
}

if ($action === 'approve_all') {
    $stmt = $pdo->query("UPDATE `comments` SET `status`='Approved' WHERE `status`='Pending'");
    echo json_encode(['success' => true, 'count' => $stmt->rowCount()]);
    exit;
}

if ($action === 'clear_queue') {
    $stmt = $pdo->query("DELETE FROM `comments` WHERE `status`='Pending'");
    echo json_encode(['success' => true, 'count' => $stmt->rowCount()]);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
