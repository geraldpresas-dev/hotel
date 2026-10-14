<?php
// my_bookings.php - guest sees own bookings (Read) and can cancel
require 'config.php';
need_guest();

$stmt = $db->prepare(
    'SELECT bookings.*, rooms.room_number, rooms.type
     FROM bookings JOIN rooms ON bookings.room_id = rooms.id
     WHERE bookings.user_id = ?
     ORDER BY bookings.check_in DESC'
);
$stmt->execute([$_SESSION['user_id']]);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<h3>My Bookings</h3>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= e($_GET['msg']) ?></div>
<?php endif; ?>

<div class="card">
<div class="table-responsive">
<table class="table table-hover mb-0 align-middle">
    <thead class="table-light">
        <tr><th>Room</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
    <?php if (count($list) == 0): ?>
        <tr><td colspan="6" class="text-center py-3">No bookings yet. <a href="rooms.php">Find a room</a></td></tr>
    <?php endif; ?>

    <?php foreach ($list as $b): ?>
        <tr>
            <td><?= e($b['type']) ?> (<?= e($b['room_number']) ?>)</td>
            <td><?= e($b['check_in']) ?></td>
            <td><?= e($b['check_out']) ?></td>
            <td><?= peso($b['total_price']) ?></td>
            <td><?= status_badge($b['status']) ?></td>
            <td>
                <?php if ($b['status'] == 'booked'): ?>
                    <form method="post" action="cancel_booking.php" onsubmit="return confirm('Cancel this booking?');">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>

<?php include 'footer.php'; ?>
