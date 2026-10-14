<?php
// admin_bookings.php - admin sees all bookings and updates their status
require 'config.php';
need_admin();

$status = $_GET['status'] ?? 'all';

$sql = 'SELECT bookings.*, users.full_name, rooms.room_number
        FROM bookings
        JOIN users ON bookings.user_id = users.id
        JOIN rooms ON bookings.room_id = rooms.id';
$params = [];

if (in_array($status, ['booked', 'checked_in', 'checked_out', 'cancelled'])) {
    $sql .= ' WHERE bookings.status = ?';
    $params[] = $status;
}
$sql .= ' ORDER BY bookings.check_in DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<h3>Bookings</h3>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info"><?= e($_GET['msg']) ?></div>
<?php endif; ?>

<form method="get" class="mb-3">
    <select name="status" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
        <option value="all">All bookings</option>
        <option value="booked" <?= $status == 'booked' ? 'selected' : '' ?>>Booked</option>
        <option value="checked_in" <?= $status == 'checked_in' ? 'selected' : '' ?>>Checked in</option>
        <option value="checked_out" <?= $status == 'checked_out' ? 'selected' : '' ?>>Checked out</option>
        <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select>
</form>

<div class="card">
<div class="table-responsive">
<table class="table table-hover mb-0 align-middle">
    <thead class="table-light">
        <tr><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th><th>Action</th></tr>
    </thead>
    <tbody>
    <?php if (count($list) == 0): ?>
        <tr><td colspan="7" class="text-center py-3">No bookings found.</td></tr>
    <?php endif; ?>

    <?php foreach ($list as $b): ?>
        <tr>
            <td><?= e($b['full_name']) ?></td>
            <td><?= e($b['room_number']) ?></td>
            <td><?= e($b['check_in']) ?></td>
            <td><?= e($b['check_out']) ?></td>
            <td><?= peso($b['total_price']) ?></td>
            <td><?= status_badge($b['status']) ?></td>
            <td class="text-nowrap">
                <?php if ($b['status'] == 'booked'): ?>
                    <form method="post" action="booking_status.php" class="d-inline">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <input type="hidden" name="status" value="checked_in">
                        <button class="btn btn-sm btn-success">Check-in</button>
                    </form>
                    <form method="post" action="booking_status.php" class="d-inline" onsubmit="return confirm('Cancel this booking?');">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <input type="hidden" name="status" value="cancelled">
                        <button class="btn btn-sm btn-outline-danger">Cancel</button>
                    </form>
                <?php elseif ($b['status'] == 'checked_in'): ?>
                    <form method="post" action="booking_status.php" class="d-inline">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <input type="hidden" name="status" value="checked_out">
                        <button class="btn btn-sm btn-secondary">Check-out</button>
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
