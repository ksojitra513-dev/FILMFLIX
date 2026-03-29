<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM `payments` ORDER BY payment_date DESC, id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'cycle_status') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT `status` FROM `payments` WHERE `id`=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $order = ['Completed', 'Pending', 'Failed', 'Refunded'];
            $newStatus = $order[(array_search($row['status'], $order) + 1) % 4];
            $stmt = $pdo->prepare("UPDATE `payments` SET `status`=? WHERE `id`=?");
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
