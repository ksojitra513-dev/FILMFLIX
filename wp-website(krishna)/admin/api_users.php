<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'read';

if ($action === 'read') {
    $stmt = $pdo->query("SELECT id, name, role, sub, status, img, DATE_FORMAT(join_date, '%b %d, %Y') as date FROM `users` ORDER BY id DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'create') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(!$data) $data = $_POST;
    
    $stmt = $pdo->prepare("INSERT INTO `users` (`name`, `role`, `sub`, `status`) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$data['name'], $data['role'], $data['sub'], $data['status']])) {
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['error' => 'Failed to create user']);
    }
    exit;
}

if ($action === 'update' || $action === 'update_profile') {
    $data = $_POST;
    if (empty($data)) {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    $id = $data['id'] ?? null;
    if (!$id) { echo json_encode(['error' => 'Missing ID']); exit; }

    if ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE `users` SET `name`=?, `role`=?, `sub`=?, `status`=? WHERE `id`=?");
        $res = $stmt->execute([$data['name'], $data['role'], $data['sub'], $data['status'], $id]);
    } else {
        // Full profile update
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $pass = $data['password'] ?? '';
        $city = $data['city'] ?? '';
        $num = $data['number'] ?? '';
        $role = $data['role'] ?? 'Admin';
        
        // Handle optional img upload
        $img = $data['img_existing'] ?? '1.jpg';
        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $filename = time() . '_' . basename($_FILES['img']['name']);
            if (move_uploaded_file($_FILES['img']['tmp_name'], $uploadDir . $filename)) {
                $img = $filename;
            }
        }

        if ($pass) {
            $stmt = $pdo->prepare("UPDATE `users` SET `name`=?, `email`=?, `password`=?, `city`=?, `number`=?, `role`=?, `img`=? WHERE `id`=?");
            $res = $stmt->execute([$name, $email, $pass, $city, $num, $role, $img, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE `users` SET `name`=?, `email`=?, `city`=?, `number`=?, `role`=?, `img`=? WHERE `id`=?");
            $res = $stmt->execute([$name, $email, $city, $num, $role, $img, $id]);
        }
    }

    if ($res) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update database']);
    }
    exit;
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM `users` WHERE `id`=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to delete user']);
    exit;
}

if ($action === 'cycle_status') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT `status` FROM `users` WHERE `id`=?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        
        $statuses = ['Active', 'Inactive', 'Suspended'];
        $next = $statuses[(array_search($current, $statuses) + 1) % 3];
        
        $upd = $pdo->prepare("UPDATE `users` SET `status`=? WHERE `id`=?");
        if ($upd->execute([$next, $id])) {
            echo json_encode(['success' => true, 'new_status' => $next]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to cycle status']);
    exit;
}

if ($action === 'restore') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $upd = $pdo->prepare("UPDATE `users` SET `status`='Active' WHERE `id`=?");
        if ($upd->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
    echo json_encode(['error' => 'Failed to restore user']);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
