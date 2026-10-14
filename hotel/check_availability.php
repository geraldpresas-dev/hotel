<?php
// check_availability.php - called by JavaScript on the booking page
// tells if the room is free on the chosen dates (returns JSON)
require 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['valid' => false]);
    exit;
}

$room_id = (int) ($_GET['room_id'] ?? 0);
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';

if (!valid_date($check_in) || !valid_date($check_out) || $check_out <= $check_in) {
    echo json_encode(['valid' => false]);
    exit;
}

echo json_encode([
    'valid' => true,
    'available' => room_is_free($db, $room_id, $check_in, $check_out)
]);
