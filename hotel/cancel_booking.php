<?php
// cancel_booking.php - guest cancels their own booking (only if still "booked")
require 'config.php';
need_guest();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int) $_POST['id'];
    $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'booked'");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

header('Location: my_bookings.php?msg=' . urlencode('Booking cancelled.'));
exit;
