<?php
session_name('hotel_session');
session_start();
session_destroy();
header('Location: login.php');
exit;
