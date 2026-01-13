<?php include 'header.php'; ?>
<div class="container mt-4">
    <h1 class="mb-3 fw-bold text-success">Welcome to Blinkit Dashboard</h1>
    <p class="text-muted mb-5">Monitor, manage, and explore your grocery delivery system.</p>

    <!-- Stat Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <a href="users.php" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #5e60ce, #7400b8);">
                    <div class="card-body d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h5 class="mb-0">Users</h5>
                            <small>Manage all users</small>
                        </div>
                        <i class="bi bi-people fs-2"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="products.php" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #00b4d8, #0077b6);">
                    <div class="card-body d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h5 class="mb-0">Products</h5>
                            <small>View & edit items</small>
                        </div>
                        <i class="bi bi-box-seam fs-2"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="orders.php" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #ff8800, #ff5400);">
                    <div class="card-body d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h5 class="mb-0">Orders</h5>
                            <small>Track & manage</small>
                        </div>
                        <i class="bi bi-receipt fs-2"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="delivery_partners.php" class="text-decoration-none">
                <div class="card card-stat" style="background: linear-gradient(135deg, #48cae4, #0096c7);">
                    <div class="card-body d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h5 class="mb-0">Delivery</h5>
                            <small>Manage partners</small>
                        </div>
                        <i class="bi bi-truck fs-2"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Sneak Peek Section -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold text-success"><i class="bi bi-bag-check"></i> Continue Shopping</h5>
                    <p class="text-muted mb-2">Discover fresh picks just for you</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Fresh Broccoli - ₹40</li>
                        <li class="list-group-item">Red Apples - ₹120/kg</li>
                        <li class="list-group-item">Amul Milk 1L - ₹65</li>
                    </ul>
                    <a href="products.php" class="btn btn-success btn-sm mt-3">Browse All Products</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold text-warning"><i class="bi bi-heart"></i> Your Favorites</h5>
                    <p class="text-muted mb-2">Reorder what you love</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Dairy Milk Silk</li>
                        <li class="list-group-item">Lay’s Classic Chips</li>
                        <li class="list-group-item">Nescafe Coffee</li>
                    </ul>
                    <button class="btn btn-outline-warning btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Login to Save Favorites
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Past Orders -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body">
            <h5 class="fw-bold text-primary"><i class="bi bi-clock-history"></i> Recent Orders</h5>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>#1</td><td><span class="badge bg-success">Delivered</span></td><td>₹200</td><td>Today</td></tr>
                    <tr><td>#2</td><td><span class="badge bg-warning">In Transit</span></td><td>₹450</td><td>Yesterday</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-box-arrow-in-right"></i> Login</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" placeholder="Enter your email">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
