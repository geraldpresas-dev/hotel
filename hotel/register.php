<?php
require 'config.php';

$error = '';
$username = '';
$full_name = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // server side validation
    if (strlen($full_name) < 3 || strlen($full_name) > 100) {
        $error = 'Full name must be 3 to 100 characters.';
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        $error = 'Username must be 3 to 30 characters (letters, numbers, underscore).';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password != $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = 'Username is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, full_name, password, role) VALUES (?, ?, ?, 'guest')");
            $stmt->execute([$username, $full_name, $hash]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}

include 'header.php';
?>

<h5>Create a guest account</h5>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" id="registerForm">
    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="full_name" class="form-control" value="<?= e($full_name) ?>" required maxlength="100">
    </div>
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= e($username) ?>" required minlength="3" maxlength="30">
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required minlength="6">
    </div>
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm" id="confirm" class="form-control" required>
    </div>
    <div class="text-danger mb-2" id="errorMsg"></div>
    <button type="submit" class="btn btn-primary w-100">Register</button>
</form>
<p class="mt-3 mb-0 text-center">Already have an account? <a href="login.php">Login</a></p>

<?php include 'footer.php'; ?>
