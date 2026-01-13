<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blinkit Grocery Delivery System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #00a859 !important;
        }
        .card-stat {
            transition: transform 0.2s;
        }
        .card-stat:hover {
            transform: translateY(-5px);
        }
        .table-actions {
            white-space: nowrap;
        }
        /* Make navbar stand out */
.navbar {
    background: linear-gradient(90deg, #00a859, #007f5f);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Brand */
.navbar-brand {
    font-weight: 700;
    font-size: 1.3rem;
    color: #fff !important;
    letter-spacing: 0.5px;
}

/* Nav links */
.navbar-nav .nav-link {
    color: #f1f1f1 !important;
    font-weight: 500;
    transition: color 0.3s ease, background-color 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: #fff !important;
    background: rgba(255,255,255,0.1);
    border-radius: 6px;
}

/* Dropdowns */
.dropdown-menu {
    border-radius: 8px;
    border: none;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Active page highlight */
.nav-item .active {
    background-color: rgba(255,255,255,0.15);
    border-radius: 6px;
}

/* Dashboard cards */
.card-stat {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    color: #fff;
    font-weight: 600;
}

.card-stat .card-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 1.1rem;
}

.card-stat i {
    font-size: 2rem;
    opacity: 0.8;
}

/* Smooth hover */
.card-stat:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-cart-check"></i> Blinkit
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav ms-auto">
    <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
            <a class="nav-link"><i class="bi bi-person-circle"></i> <?php echo $_SESSION['user_name']; ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>
    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
        </li>
    <?php endif; ?>
</ul>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-people"></i> Users & Addresses
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="users.php">Users</a></li>
                            <li><a class="dropdown-item" href="addresses.php">Addresses</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-shop"></i> Vendors & Stores
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="vendors.php">Vendors</a></li>
                            <li><a class="dropdown-item" href="stores.php">Stores</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-box-seam"></i> Products
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="categories.php">Categories</a></li>
                            <li><a class="dropdown-item" href="products.php">Products</a></li>
                            <li><a class="dropdown-item" href="inventory.php">Inventory</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-cart"></i> Shopping
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="carts.php">Carts</a></li>
                            <li><a class="dropdown-item" href="cart_items.php">Cart Items</a></li>
                            <li><a class="dropdown-item" href="coupons.php">Coupons</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-receipt"></i> Orders
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="orders.php">Orders</a></li>
                            <li><a class="dropdown-item" href="order_items.php">Order Items</a></li>
                            <li><a class="dropdown-item" href="payments.php">Payments</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-truck"></i> Delivery
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="delivery_partners.php">Delivery Partners</a></li>
                            <li><a class="dropdown-item" href="order_delivery.php">Order Delivery</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reviews.php"><i class="bi bi-star"></i> Reviews</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid mt-4">

