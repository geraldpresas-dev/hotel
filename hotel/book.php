<?php
// book.php - guest confirms a booking (Create)
require 'config.php';
need_guest();

$room_id = (int) ($_GET['room_id'] ?? $_POST['room_id'] ?? 0);

$stmt = $db->prepare('SELECT * FROM rooms WHERE id = ?');
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    header('Location: rooms.php');
    exit;
}

$today = date('Y-m-d');
$check_in = $_POST['check_in'] ?? $_GET['check_in'] ?? $today;
$check_out = $_POST['check_out'] ?? $_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day'));
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // server side validation
    if (!valid_date($check_in) || !valid_date($check_out)) {
        $error = 'Please enter valid dates.';
    } elseif ($check_in < $today) {
        $error = 'Check-in date cannot be in the past.';
    } elseif ($check_out <= $check_in) {
        $error = 'Check-out must be after check-in.';
    } elseif (count_nights($check_in, $check_out) > 30) {
        $error = 'You can only book up to 30 nights.';
    } elseif (!room_is_free($db, $room_id, $check_in, $check_out)) {
        $error = 'Sorry, this room is already booked on those dates.';
    } else {
        $total = count_nights($check_in, $check_out) * $room['price'];

        $stmt = $db->prepare('INSERT INTO bookings (user_id, room_id, check_in, check_out, total_price) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $room_id, $check_in, $check_out, $total]);

        header('Location: my_bookings.php?msg=' . urlencode('Room booked! See you soon.'));
        exit;
    }
}

// if the dates in the URL are broken, use today and tomorrow
if (!valid_date($check_in) || !valid_date($check_out)) {
    $check_in = $today;
    $check_out = date('Y-m-d', strtotime('+1 day'));
}

include 'header.php';
?>

<h3>Book Room <?= e($room['room_number']) ?></h3>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="room-top room-<?= e($room['type']) ?>">
                <h5 class="mb-0"><?= e($room['type']) ?> Room - <?= e($room['room_number']) ?></h5>
                <small><?= peso($room['price']) ?> per night, up to <?= $room['capacity'] ?> guest(s)</small>
            </div>
            <div class="card-body">

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post" id="bookForm" data-price="<?= $room['price'] ?>" data-room="<?= $room['id'] ?>">
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Check-in</label>
                            <input type="date" name="check_in" id="check_in" class="form-control" value="<?= e($check_in) ?>" min="<?= $today ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Check-out</label>
                            <input type="date" name="check_out" id="check_out" class="form-control" value="<?= e($check_out) ?>" min="<?= $today ?>" required>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded mb-3">
                        <div>Nights: <b id="nightsText">-</b></div>
                        <div>Total: <b id="totalText">-</b></div>
                        <div id="availMsg" class="mt-1"></div>
                    </div>

                    <div class="text-danger mb-2" id="dateError"></div>
                    <button type="submit" class="btn btn-success">Confirm Booking</button>
                    <a href="rooms.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
