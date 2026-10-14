<?php
// booking_status.php - admin changes a booking status (Update)
require 'config.php';
need_admin();

// which status can change to which
$allowed = [
    'booked'     => ['checked_in', 'cancelled'],
    'checked_in' => ['checked_out'],
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int) $_POST['id'];
    $new_status = $_POST['status'];

    $stmt = $db->prepare('SELECT status FROM bookings WHERE id = ?');
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();

    if ($current && isset($allowed[$current]) && in_array($new_status, $allowed[$current])) {
        $stmt = $db->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $stmt->execute([$new_status, $id]);
        $msg = 'Booking updated.';
    } else {
        $msg = 'That change is not allowed.';
    }
}

header('Location: admin_bookings.php?msg=' . urlencode($msg));
exit;
