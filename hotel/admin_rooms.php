<?php
// admin_rooms.php - admin sees all rooms (Read)
require 'config.php';
need_admin();

$rooms = $db->query('SELECT * FROM rooms ORDER BY room_number')->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Rooms</h3>
    <a href="room_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Room</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info"><?= e($_GET['msg']) ?></div>
<?php endif; ?>

<div class="card">
<div class="table-responsive">
<table class="table table-hover mb-0 align-middle">
    <thead class="table-light">
        <tr><th>Room No.</th><th>Type</th><th>Price / night</th><th>Capacity</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($rooms as $r): ?>
        <tr>
            <td><?= e($r['room_number']) ?></td>
            <td><?= e($r['type']) ?></td>
            <td><?= peso($r['price']) ?></td>
            <td><?= $r['capacity'] ?></td>
            <td class="text-nowrap">
                <a href="room_form.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <form method="post" action="room_delete.php" class="d-inline" onsubmit="return confirm('Delete this room?');">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>

<?php include 'footer.php'; ?>
