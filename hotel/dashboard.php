<?php
// dashboard.php - admin home page with numbers
require 'config.php';
need_admin();

$total_rooms = $db->query('SELECT COUNT(*) FROM rooms')->fetchColumn();
$total_guests = $db->query("SELECT COUNT(*) FROM users WHERE role = 'guest'")->fetchColumn();
$upcoming = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'booked'")->fetchColumn();
$in_hotel = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'checked_in'")->fetchColumn();
$revenue = $db->query("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status IN ('booked','checked_in','checked_out')")->fetchColumn();

$recent = $db->query(
    'SELECT bookings.*, users.full_name, rooms.room_number
     FROM bookings
     JOIN users ON bookings.user_id = users.id
     JOIN rooms ON bookings.room_id = rooms.id
     ORDER BY bookings.id DESC LIMIT 5'
)->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<h3>Dashboard</h3>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card"><div class="card-body">
            <div class="text-muted">Total Rooms</div>
            <div class="stat-number text-primary"><?= $total_rooms ?></div>
        </div></div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card"><div class="card-body">
            <div class="text-muted">Registered Guests</div>
            <div class="stat-number text-success"><?= $total_guests ?></div>
        </div></div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card"><div class="card-body">
            <div class="text-muted">Upcoming Bookings</div>
            <div class="stat-number text-warning"><?= $upcoming ?></div>
        </div></div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card"><div class="card-body">
            <div class="text-muted">Guests In Hotel</div>
            <div class="stat-number text-info"><?= $in_hotel ?></div>
        </div></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="text-muted">Total Revenue (not including cancelled)</div>
        <div class="stat-number"><?= peso($revenue) ?></div>
    </div>
</div>

<h5>Recent Bookings</h5>
<div class="card">
<div class="table-responsive">
<table class="table mb-0">
    <thead class="table-light">
        <tr><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr>
    </thead>
    <tbody>
    <?php foreach ($recent as $b): ?>
        <tr>
            <td><?= e($b['full_name']) ?></td>
            <td><?= e($b['room_number']) ?></td>
            <td><?= e($b['check_in']) ?></td>
            <td><?= e($b['check_out']) ?></td>
            <td><?= status_badge($b['status']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>

<?php include 'footer.php'; ?>
