<?php $logged_in = isset($_SESSION['user_id']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sunrise Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<?php if ($logged_in): ?>
<!-- logged in pages: sidebar on the left -->
<div class="d-md-flex">

    <div class="offcanvas-md offcanvas-start sidebar" tabindex="-1" id="sidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="bi bi-brightness-high"></i> Sunrise Hotel</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <h5 class="brand d-none d-md-block"><i class="bi bi-brightness-high"></i> Sunrise Hotel</h5>

            <nav class="nav flex-column">
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <?php nav_link('dashboard.php', 'Dashboard', 'speedometer2'); ?>
                    <?php nav_link('admin_rooms.php', 'Rooms', 'door-open', ['room_form.php']); ?>
                    <?php nav_link('admin_bookings.php', 'Bookings', 'calendar-check'); ?>
                <?php else: ?>
                    <?php nav_link('rooms.php', 'Find a Room', 'search', ['book.php']); ?>
                    <?php nav_link('my_bookings.php', 'My Bookings', 'journal-text'); ?>
                <?php endif; ?>
            </nav>

            <div class="mt-auto user-box">
                <div><i class="bi bi-person-circle"></i> <?= e($_SESSION['full_name'] ?? $_SESSION['username']) ?></div>
                <small class="text-uppercase"><?= e($_SESSION['role']) ?></small><br>
                <a href="logout.php" class="btn btn-sm btn-outline-light mt-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>

    <div class="flex-grow-1">
        <div class="topbar d-md-none">
            <button class="btn btn-dark btn-sm" data-bs-toggle="offcanvas" data-bs-target="#sidebar"><i class="bi bi-list"></i> Menu</button>
            <span class="ms-2 fw-bold">Sunrise Hotel</span>
        </div>
        <div class="p-3 p-md-4">

<?php else: ?>
<!-- login / register pages: centered box -->
<div class="public-wrap">
    <div class="public-card">
        <h4 class="text-center mb-3"><i class="bi bi-brightness-high"></i> Maromels Hotel</h4>
<?php endif; ?>
