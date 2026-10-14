<?php
// rooms.php - guest searches for available rooms (shown as cards)
require 'config.php';
need_guest();

$today = date('Y-m-d');
$check_in = $_GET['check_in'] ?? $today;
$check_out = $_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day'));
$guests = (int) ($_GET['guests'] ?? 1);
$error = '';

if (!valid_date($check_in) || !valid_date($check_out)) {
    $error = 'Please enter valid dates.';
} elseif ($check_in < $today) {
    $error = 'Check-in date cannot be in the past.';
} elseif ($check_out <= $check_in) {
    $error = 'Check-out must be after check-in.';
}

$rooms = [];
if ($error == '') {
    // rooms that are NOT already booked on those dates
    $stmt = $db->prepare(
        "SELECT * FROM rooms
         WHERE capacity >= ?
         AND id NOT IN (
             SELECT room_id FROM bookings
             WHERE status IN ('booked','checked_in') AND check_in < ? AND check_out > ?
         )
         ORDER BY price, room_number"
    );
    $stmt->execute([$guests, $check_out, $check_in]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'header.php';
?>

<h3>Find a Room</h3>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3" id="searchForm">
            <div class="col-6 col-md-3">
                <label class="form-label">Check-in</label>
                <input type="date" name="check_in" id="check_in" class="form-control" value="<?= e($check_in) ?>" min="<?= $today ?>" required>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Check-out</label>
                <input type="date" name="check_out" id="check_out" class="form-control" value="<?= e($check_out) ?>" min="<?= $today ?>" required>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Guests</label>
                <select name="guests" class="form-select">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <option value="<?= $i ?>" <?= $guests == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>
        <div class="text-danger mt-2" id="dateError"></div>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php elseif (count($rooms) == 0): ?>
    <div class="alert alert-warning">Sorry, no rooms are available for those dates.</div>
<?php else: ?>
    <p class="text-muted"><?= count($rooms) ?> room(s) available for <?= count_nights($check_in, $check_out) ?> night(s).</p>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
    <?php foreach ($rooms as $r): ?>
        <div class="col">
            <div class="card h-100">
                <div class="room-top room-<?= e($r['type']) ?>">
                    <h5 class="mb-0"><?= e($r['type']) ?> Room</h5>
                    <small>Room <?= e($r['room_number']) ?></small>
                </div>
                <div class="card-body">
                    <p class="mb-1"><i class="bi bi-people"></i> Up to <?= $r['capacity'] ?> guest(s)</p>
                    <p class="fs-5 fw-bold mb-0"><?= peso($r['price']) ?> <small class="text-muted fw-normal">/ night</small></p>
                </div>
                <div class="card-footer bg-white">
                    <a href="book.php?room_id=<?= $r['id'] ?>&check_in=<?= e($check_in) ?>&check_out=<?= e($check_out) ?>" class="btn btn-success w-100">Book this room</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
