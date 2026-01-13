<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'header.php';
?>
<div class="container mt-4">
    <h2 class="fw-bold text-success mb-4">Hi <?php echo $_SESSION['user_name']; ?> 👋</h2>
    <p class="text-muted">Welcome to your personal Blinkit dashboard.</p>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold text-primary"><i class="bi bi-bag"></i> My Orders</h5>
                    <p class="text-muted small">Check your active and past orders.</p>
                    <a href="orders.php" class="btn btn-outline-primary btn-sm">View Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold text-warning"><i class="bi bi-heart"></i> Favorites</h5>
                    <p class="text-muted small">See your saved favorite products.</p>
                    <a href="products.php" class="btn btn-outline-warning btn-sm">View Favorites</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold text-success"><i class="bi bi-basket"></i> Continue Shopping</h5>
                    <p class="text-muted small">Explore more fresh groceries.</p>
                    <a href="products.php" class="btn btn-outline-success btn-sm">Browse Products</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
