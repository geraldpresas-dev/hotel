<?php
// config.php - database connection, session, and helper functions
// Change these to your hosting database details.
$host = 'sql302.infinityfree.com';
$dbname = 'if0_43172623_hotel';
$dbuser = 'if0_43172623';
$dbpass = 'v6lPb9KBOD';

// own session name, so other projects on localhost don't share this login
session_name('hotel_session');
session_start();

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed. Check config.php');
}

// makes text safe to print (prevents XSS)
function e($text) {
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// page needs login
function need_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// page is for admin only
function need_admin() {
    need_login();
    if ($_SESSION['role'] != 'admin') {
        die('Access denied. Admins only.');
    }
}

// page is for guests only (admin goes back to the dashboard)
function need_guest() {
    need_login();
    if ($_SESSION['role'] == 'admin') {
        header('Location: dashboard.php');
        exit;
    }
}

// shows peso amount like P1,200.00
function peso($amount) {
    return 'P' . number_format($amount, 2);
}

// true if the text is a real date like 2026-10-25
function valid_date($text) {
    $d = DateTime::createFromFormat('Y-m-d', $text);
    return $d && $d->format('Y-m-d') == $text;
}

// number of nights between two dates
function count_nights($check_in, $check_out) {
    return (int) ((strtotime($check_out) - strtotime($check_in)) / 86400);
}

// true if nobody booked the room on those dates
function room_is_free($db, $room_id, $check_in, $check_out) {
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM bookings
         WHERE room_id = ? AND status IN ('booked','checked_in')
         AND check_in < ? AND check_out > ?"
    );
    $stmt->execute([$room_id, $check_out, $check_in]);
    return $stmt->fetchColumn() == 0;
}

// colored badge for a booking status
function status_badge($status) {
    $colors = ['booked' => 'primary', 'checked_in' => 'success', 'checked_out' => 'secondary', 'cancelled' => 'danger'];
    return '<span class="badge bg-' . $colors[$status] . '">' . e(str_replace('_', ' ', $status)) . '</span>';
}

// one link in the sidebar (highlighted if it is the current page)
function nav_link($file, $label, $icon, $other_pages = []) {
    $current = basename($_SERVER['PHP_SELF']);
    $active = ($current == $file || in_array($current, $other_pages)) ? 'active' : '';
    echo '<a class="nav-link ' . $active . '" href="' . $file . '"><i class="bi bi-' . $icon . ' me-2"></i>' . $label . '</a>';
}
