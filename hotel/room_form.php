<?php
// room_form.php - admin adds (Create) or edits (Update) a room
require 'config.php';
need_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$room = ['room_number' => '', 'type' => 'Single', 'price' => '', 'capacity' => 1];

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM rooms WHERE id = ?');
    $stmt->execute([$id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        header('Location: admin_rooms.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room = [
        'room_number' => trim($_POST['room_number']),
        'type'        => $_POST['type'],
        'price'       => $_POST['price'],
        'capacity'    => (int) $_POST['capacity'],
    ];

    // server side validation
    if ($room['room_number'] == '' || strlen($room['room_number']) > 10) {
        $error = 'Room number is required (max 10 characters).';
    } elseif (!in_array($room['type'], ['Single', 'Double', 'Deluxe', 'Suite'])) {
        $error = 'Invalid room type.';
    } elseif (!is_numeric($room['price']) || $room['price'] <= 0 || $room['price'] > 100000) {
        $error = 'Price must be a number greater than 0.';
    } elseif ($room['capacity'] < 1 || $room['capacity'] > 10) {
        $error = 'Capacity must be from 1 to 10.';
    } else {
        // room number must be unique
        $stmt = $db->prepare('SELECT id FROM rooms WHERE room_number = ? AND id != ?');
        $stmt->execute([$room['room_number'], $id]);

        if ($stmt->fetch()) {
            $error = 'That room number already exists.';
        } else {
            if ($id > 0) {
                $stmt = $db->prepare('UPDATE rooms SET room_number = ?, type = ?, price = ?, capacity = ? WHERE id = ?');
                $stmt->execute([$room['room_number'], $room['type'], $room['price'], $room['capacity'], $id]);
                $msg = 'Room updated.';
            } else {
                $stmt = $db->prepare('INSERT INTO rooms (room_number, type, price, capacity) VALUES (?, ?, ?, ?)');
                $stmt->execute([$room['room_number'], $room['type'], $room['price'], $room['capacity']]);
                $msg = 'Room added.';
            }
            header('Location: admin_rooms.php?msg=' . urlencode($msg));
            exit;
        }
    }
}

include 'header.php';
?>

<h3><?= $id > 0 ? 'Edit Room' : 'Add Room' ?></h3>

<div class="row">
    <div class="col-lg-6">
        <div class="card"><div class="card-body">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Room Number</label>
                    <input type="text" name="room_number" class="form-control" value="<?= e($room['room_number']) ?>" maxlength="10" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <?php foreach (['Single', 'Double', 'Deluxe', 'Suite'] as $t): ?>
                            <option <?= $room['type'] == $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price per night</label>
                    <input type="number" name="price" class="form-control" value="<?= e($room['price']) ?>" min="1" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Capacity (guests)</label>
                    <input type="number" name="capacity" class="form-control" value="<?= e($room['capacity']) ?>" min="1" max="10" required>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="admin_rooms.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div></div>
    </div>
</div>

<?php include 'footer.php'; ?>
