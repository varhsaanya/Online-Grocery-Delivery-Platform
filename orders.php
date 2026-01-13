<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $user_id = $_POST['user_id'];
            $store_id = $_POST['store_id'];
            $address_id = $_POST['address_id'];
            $total_amount = $_POST['total_amount'];
            $coupon_id = !empty($_POST['coupon_id']) ? $_POST['coupon_id'] : null;
            $status = $_POST['status'];
            $delivery_time_estimate = !empty($_POST['delivery_time_estimate']) ? $_POST['delivery_time_estimate'] : null;
            
            $stmt = $pdo->prepare("INSERT INTO Orders (user_id, store_id, address_id, total_amount, coupon_id, status, delivery_time_estimate) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $store_id, $address_id, $total_amount, $coupon_id, $status, $delivery_time_estimate])) {
                $success = "Order added successfully!";
            } else {
                $error = "Error adding order!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $order_id = $_POST['order_id'];
            $user_id = $_POST['user_id'];
            $store_id = $_POST['store_id'];
            $address_id = $_POST['address_id'];
            $total_amount = $_POST['total_amount'];
            $coupon_id = !empty($_POST['coupon_id']) ? $_POST['coupon_id'] : null;
            $status = $_POST['status'];
            $delivery_time_estimate = !empty($_POST['delivery_time_estimate']) ? $_POST['delivery_time_estimate'] : null;
            
            $stmt = $pdo->prepare("UPDATE Orders SET user_id = ?, store_id = ?, address_id = ?, total_amount = ?, coupon_id = ?, status = ?, delivery_time_estimate = ? WHERE order_id = ?");
            if ($stmt->execute([$user_id, $store_id, $address_id, $total_amount, $coupon_id, $status, $delivery_time_estimate, $order_id])) {
                $success = "Order updated successfully!";
            } else {
                $error = "Error updating order!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $order_id = $_POST['order_id'];
            $stmt = $pdo->prepare("DELETE FROM Orders WHERE order_id = ?");
            if ($stmt->execute([$order_id])) {
                $success = "Order deleted successfully!";
            } else {
                $error = "Error deleting order!";
            }
        }
    }
}

// Fetch all orders with related data
$stmt = $pdo->query("SELECT o.*, u.name as user_name, s.name as store_name, a.street, a.city, a.state, c.code as coupon_code 
                     FROM Orders o 
                     JOIN Users u ON o.user_id = u.user_id 
                     JOIN Stores s ON o.store_id = s.store_id 
                     JOIN Addresses a ON o.address_id = a.address_id 
                     LEFT JOIN Coupons c ON o.coupon_id = c.coupon_id 
                     ORDER BY o.order_id DESC");
$orders = $stmt->fetchAll();

// Fetch users for dropdown
$stmt = $pdo->query("SELECT user_id, name FROM Users ORDER BY name");
$users = $stmt->fetchAll();

// Fetch stores for dropdown
$stmt = $pdo->query("SELECT store_id, name FROM Stores ORDER BY name");
$stores = $stmt->fetchAll();

// Fetch addresses for dropdown
$stmt = $pdo->query("SELECT address_id, user_id, street, city, state FROM Addresses ORDER BY address_id DESC");
$addresses = $stmt->fetchAll();

// Fetch coupons for dropdown
$stmt = $pdo->query("SELECT coupon_id, code FROM Coupons ORDER BY code");
$coupons = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-receipt"></i> Orders Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
        <i class="bi bi-plus-circle"></i> Add New Order
    </button>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Store</th>
                        <th>Total Amount</th>
                        <th>Coupon</th>
                        <th>Status</th>
                        <th>Order Time</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['store_name']); ?></td>
                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo $order['coupon_code'] ? htmlspecialchars($order['coupon_code']) : 'None'; ?></td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($order['status']); ?></span></td>
                            <td><?php echo $order['order_time']; ?></td>
                            <td class="table-actions">
                                <a href="order_items.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> View Items
                                </a>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editOrderModal" 
                                    data-id="<?php echo $order['order_id']; ?>"
                                    data-user-id="<?php echo $order['user_id']; ?>"
                                    data-store-id="<?php echo $order['store_id']; ?>"
                                    data-address-id="<?php echo $order['address_id']; ?>"
                                    data-total-amount="<?php echo $order['total_amount']; ?>"
                                    data-coupon-id="<?php echo $order['coupon_id'] ?? ''; ?>"
                                    data-status="<?php echo htmlspecialchars($order['status']); ?>"
                                    data-delivery-time="<?php echo $order['delivery_time_estimate'] ?? ''; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteOrderModal" 
                                    data-id="<?php echo $order['order_id']; ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User</label>
                            <select class="form-select" name="user_id" required>
                                <option value="">Select User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store</label>
                            <select class="form-select" name="store_id" required>
                                <option value="">Select Store</option>
                                <?php foreach ($stores as $store): ?>
                                    <option value="<?php echo $store['store_id']; ?>"><?php echo htmlspecialchars($store['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address</label>
                            <select class="form-select" name="address_id" required>
                                <option value="">Select Address</option>
                                <?php foreach ($addresses as $address): ?>
                                    <option value="<?php echo $address['address_id']; ?>">
                                        <?php echo htmlspecialchars($address['street'] . ', ' . $address['city'] . ', ' . $address['state']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Amount</label>
                            <input type="number" step="0.01" class="form-control" name="total_amount" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Coupon (Optional)</label>
                            <select class="form-select" name="coupon_id">
                                <option value="">None</option>
                                <?php foreach ($coupons as $coupon): ?>
                                    <option value="<?php echo $coupon['coupon_id']; ?>"><?php echo htmlspecialchars($coupon['code']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Delivery Time Estimate (Optional)</label>
                            <input type="datetime-local" class="form-control" name="delivery_time_estimate">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="order_id" id="edit_order_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User</label>
                            <select class="form-select" name="user_id" id="edit_user_id" required>
                                <option value="">Select User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store</label>
                            <select class="form-select" name="store_id" id="edit_store_id" required>
                                <option value="">Select Store</option>
                                <?php foreach ($stores as $store): ?>
                                    <option value="<?php echo $store['store_id']; ?>"><?php echo htmlspecialchars($store['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address</label>
                            <select class="form-select" name="address_id" id="edit_address_id" required>
                                <option value="">Select Address</option>
                                <?php foreach ($addresses as $address): ?>
                                    <option value="<?php echo $address['address_id']; ?>">
                                        <?php echo htmlspecialchars($address['street'] . ', ' . $address['city'] . ', ' . $address['state']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Amount</label>
                            <input type="number" step="0.01" class="form-control" name="total_amount" id="edit_total_amount" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Coupon (Optional)</label>
                            <select class="form-select" name="coupon_id" id="edit_coupon_id">
                                <option value="">None</option>
                                <?php foreach ($coupons as $coupon): ?>
                                    <option value="<?php echo $coupon['coupon_id']; ?>"><?php echo htmlspecialchars($coupon['code']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Delivery Time Estimate (Optional)</label>
                            <input type="datetime-local" class="form-control" name="delivery_time_estimate" id="edit_delivery_time">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Order Modal -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="order_id" id="delete_order_id">
                    <p>Are you sure you want to delete this order?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('editOrderModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_order_id').value = button.getAttribute('data-id');
        document.getElementById('edit_user_id').value = button.getAttribute('data-user-id');
        document.getElementById('edit_store_id').value = button.getAttribute('data-store-id');
        document.getElementById('edit_address_id').value = button.getAttribute('data-address-id');
        document.getElementById('edit_total_amount').value = button.getAttribute('data-total-amount');
        document.getElementById('edit_coupon_id').value = button.getAttribute('data-coupon-id') || '';
        document.getElementById('edit_status').value = button.getAttribute('data-status');
        const deliveryTime = button.getAttribute('data-delivery-time');
        if (deliveryTime) {
            const date = new Date(deliveryTime);
            document.getElementById('edit_delivery_time').value = date.toISOString().slice(0, 16);
        }
    });

    document.getElementById('deleteOrderModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_order_id').value = button.getAttribute('data-id');
    });
</script>

<?php include('footer.php'); ?>

