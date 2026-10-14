<?php
// room_delete.php - admin deletes a room (Delete)
require 'config.php';
need_admin();

$msg = 'Room deleted.';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int) $_POST['id'];

    // do not delete a room that still has active bookings
    $stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = ? AND status IN ('booked','checked_in')");
    $stmt->execute([$id]);

    if ($stmt->fetchColumn() > 0) {
        $msg = 'Cannot delete: this room still has active bookings.';
    } else {
        $stmt = $db->prepare('DELETE FROM rooms WHERE id = ?');
        $stmt->execute([$id]);
    }
}

header('Location: admin_rooms.php?msg=' . urlencode($msg));
exit;
