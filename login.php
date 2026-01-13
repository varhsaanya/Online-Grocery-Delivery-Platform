<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("❌ SQL Error: " . mysqli_error($conn));  // shows why query failed
    }

    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['password'] === $password) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['is_admin'] = $row['is_admin'] ?? 0;

            if ($_SESSION['is_admin']) {
                header("Location: index.php");
            } else {
                header("Location: home_user.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Blinkit Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 400px;">
        <h3 class="text-center text-success mb-3"><i class="bi bi-box-arrow-in-right"></i> Login</h3>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-success w-100">Login</button>
        </form>
        <div class="text-center mt-3">
            <a href="register.php" class="text-decoration-none">Don’t have an account? Register</a>
        </div>
    </div>
</div>
</body>
</html>
