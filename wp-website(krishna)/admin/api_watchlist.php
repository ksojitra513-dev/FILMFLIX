<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

if ($action === 'read') {
    $stmt = $pdo->query("SELECT * FROM `watchlist` ORDER BY timestamp DESC, id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'create' || $action === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data) $data = $_POST;
    
    if ($action === 'create') {
        $stmt = $pdo->prepare("INSERT INTO `watchlist` (`user_name`, `avatar`, `title`, `type`, `priority`, `added_date`, `timestamp`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $res = $stmt->execute([$data['user_name'], $data['avatar'], $data['title'], $data['type'], $data['priority'], $data['added_date'], $data['timestamp']]);
    } else {
        $stmt = $pdo->prepare("UPDATE `watchlist` SET `user_name`=?, `title`=?, `type`=?, `priority`=? WHERE `id`=?");
        $res = $stmt->execute([$data['user_name'], $data['title'], $data['type'], $data['priority'], $data['id']]);
    }

    if ($res) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to save watchlist data']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM `watchlist` WHERE `id`=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to delete entry']);
    exit;
}

if ($action === 'cycle_priority') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT `priority` FROM `watchlist` WHERE `id`=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $order = ['Low', 'Medium', 'High'];
            $newPriority = $order[(array_search($row['priority'], $order) + 1) % 3];
            $stmt = $pdo->prepare("UPDATE `watchlist` SET `priority`=? WHERE `id`=?");
            if ($stmt->execute([$newPriority, $id])) {
                echo json_encode(['success' => true, 'new_priority' => $newPriority]);
                exit;
            }
        }
    }
    echo json_encode(['error' => 'Failed to cycle priority']);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
