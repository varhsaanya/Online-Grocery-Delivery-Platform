<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $order_id = $_POST['order_id'];
            $rider_id = $_POST['rider_id'];
            $current_status = $_POST['current_status'];
            
            $stmt = $pdo->prepare("INSERT INTO OrderDelivery (order_id, rider_id, current_status) VALUES (?, ?, ?)");
            if ($stmt->execute([$order_id, $rider_id, $current_status])) {
                $success = "Order delivery assigned successfully!";
            } else {
                $error = "Error assigning order delivery!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $order_id = $_POST['order_id'];
            $rider_id = $_POST['rider_id'];
            $current_status = $_POST['current_status'];
            $picked_time = !empty($_POST['picked_time']) ? $_POST['picked_time'] : null;
            $delivered_time = !empty($_POST['delivered_time']) ? $_POST['delivered_time'] : null;
            
            $stmt = $pdo->prepare("UPDATE OrderDelivery SET rider_id = ?, current_status = ?, picked_time = ?, delivered_time = ? WHERE order_id = ?");
            if ($stmt->execute([$rider_id, $current_status, $picked_time, $delivered_time, $order_id])) {
                $success = "Order delivery updated successfully!";
            } else {
                $error = "Error updating order delivery!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $order_id = $_POST['order_id'];
            $rider_id = $_POST['rider_id'];
            $stmt = $pdo->prepare("DELETE FROM OrderDelivery WHERE order_id = ? AND rider_id = ?");
            if ($stmt->execute([$order_id, $rider_id])) {
                $success = "Order delivery deleted successfully!";
            } else {
                $error = "Error deleting order delivery!";
            }
        }
    }
}

// Fetch all order deliveries with related data
$stmt = $pdo->query("SELECT od.*, o.order_id, u.name as user_name, dp.name as rider_name 
                     FROM OrderDelivery od 
                     JOIN Orders o ON od.order_id = o.order_id 
                     JOIN Users u ON o.user_id = u.user_id 
                     JOIN DeliveryPartners dp ON od.rider_id = dp.rider_id 
                     ORDER BY od.assigned_time DESC");
$order_deliveries = $stmt->fetchAll();

// Fetch orders for dropdown
$stmt = $pdo->query("SELECT order_id, user_id FROM Orders ORDER BY order_id DESC");
$orders = $stmt->fetchAll();

// Fetch delivery partners for dropdown
$stmt = $pdo->query("SELECT rider_id, name FROM DeliveryPartners ORDER BY name");
$delivery_partners = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-truck"></i> Order Delivery Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderDeliveryModal">
        <i class="bi bi-plus-circle"></i> Assign Delivery
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
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Rider</th>
                        <th>Status</th>
                        <th>Assigned Time</th>
                        <th>Picked Time</th>
                        <th>Delivered Time</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_deliveries as $delivery): ?>
                        <tr>
                            <td><?php echo $delivery['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($delivery['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['rider_name']); ?></td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($delivery['current_status']); ?></span></td>
                            <td><?php echo $delivery['assigned_time']; ?></td>
                            <td><?php echo $delivery['picked_time'] ?? 'N/A'; ?></td>
                            <td><?php echo $delivery['delivered_time'] ?? 'N/A'; ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editOrderDeliveryModal" 
                                    data-order-id="<?php echo $delivery['order_id']; ?>"
                                    data-rider-id="<?php echo $delivery['rider_id']; ?>"
                                    data-status="<?php echo htmlspecialchars($delivery['current_status']); ?>"
                                    data-picked-time="<?php echo $delivery['picked_time'] ?? ''; ?>"
                                    data-delivered-time="<?php echo $delivery['delivered_time'] ?? ''; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteOrderDeliveryModal" 
                                    data-order-id="<?php echo $delivery['order_id']; ?>"
                                    data-rider-id="<?php echo $delivery['rider_id']; ?>">
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

<!-- Add Order Delivery Modal -->
<div class="modal fade" id="addOrderDeliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <select class="form-select" name="order_id" required>
                            <option value="">Select Order</option>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?php echo $order['order_id']; ?>">Order #<?php echo $order['order_id']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Partner</label>
                        <select class="form-select" name="rider_id" required>
                            <option value="">Select Delivery Partner</option>
                            <?php foreach ($delivery_partners as $partner): ?>
                                <option value="<?php echo $partner['rider_id']; ?>"><?php echo htmlspecialchars($partner['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="current_status" required>
                            <option value="assigned">Assigned</option>
                            <option value="picked">Picked</option>
                            <option value="in_transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Order Delivery Modal -->
<div class="modal fade" id="editOrderDeliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Order Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="order_id" id="edit_order_id">
                    <div class="mb-3">
                        <label class="form-label">Delivery Partner</label>
                        <select class="form-select" name="rider_id" id="edit_rider_id" required>
                            <option value="">Select Delivery Partner</option>
                            <?php foreach ($delivery_partners as $partner): ?>
                                <option value="<?php echo $partner['rider_id']; ?>"><?php echo htmlspecialchars($partner['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="current_status" id="edit_status" required>
                            <option value="assigned">Assigned</option>
                            <option value="picked">Picked</option>
                            <option value="in_transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Picked Time (Optional)</label>
                        <input type="datetime-local" class="form-control" name="picked_time" id="edit_picked_time">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivered Time (Optional)</label>
                        <input type="datetime-local" class="form-control" name="delivered_time" id="edit_delivered_time">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Order Delivery Modal -->
<div class="modal fade" id="deleteOrderDeliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Order Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="order_id" id="delete_order_id">
                    <input type="hidden" name="rider_id" id="delete_rider_id">
                    <p>Are you sure you want to delete this delivery assignment?</p>
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
    document.getElementById('editOrderDeliveryModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_order_id').value = button.getAttribute('data-order-id');
        document.getElementById('edit_rider_id').value = button.getAttribute('data-rider-id');
        document.getElementById('edit_status').value = button.getAttribute('data-status');
        const pickedTime = button.getAttribute('data-picked-time');
        const deliveredTime = button.getAttribute('data-delivered-time');
        if (pickedTime) {
            const date = new Date(pickedTime);
            document.getElementById('edit_picked_time').value = date.toISOString().slice(0, 16);
        }
        if (deliveredTime) {
            const date = new Date(deliveredTime);
            document.getElementById('edit_delivered_time').value = date.toISOString().slice(0, 16);
        }
    });

    document.getElementById('deleteOrderDeliveryModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_order_id').value = button.getAttribute('data-order-id');
        document.getElementById('delete_rider_id').value = button.getAttribute('data-rider-id');
    });
</script>

<?php include('footer.php'); ?>

