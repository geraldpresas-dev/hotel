<?php
// index.php - sends the user to the right page
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
} elseif ($_SESSION['role'] == 'admin') {
    header('Location: dashboard.php');
} else {
    header('Location: rooms.php');
}
exit;
