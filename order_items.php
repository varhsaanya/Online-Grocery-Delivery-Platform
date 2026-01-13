<?php
include('db.php');

$order_id_filter = isset($_GET['order_id']) ? $_GET['order_id'] : null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $order_id = $_POST['order_id'];
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            $unit_price = $_POST['unit_price'];
            
            $stmt = $pdo->prepare("INSERT INTO OrderItem (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$order_id, $product_id, $quantity, $unit_price])) {
                $success = "Order item added successfully!";
            } else {
                $error = "Error adding order item!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $order_item_id = $_POST['order_item_id'];
            $order_id = $_POST['order_id'];
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            $unit_price = $_POST['unit_price'];
            
            $stmt = $pdo->prepare("UPDATE OrderItem SET order_id = ?, product_id = ?, quantity = ?, unit_price = ? WHERE order_item_id = ?");
            if ($stmt->execute([$order_id, $product_id, $quantity, $unit_price, $order_item_id])) {
                $success = "Order item updated successfully!";
            } else {
                $error = "Error updating order item!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $order_item_id = $_POST['order_item_id'];
            $stmt = $pdo->prepare("DELETE FROM OrderItem WHERE order_item_id = ?");
            if ($stmt->execute([$order_item_id])) {
                $success = "Order item deleted successfully!";
            } else {
                $error = "Error deleting order item!";
            }
        }
    }
}

// Build query with optional filter
$query = "SELECT oi.*, p.name as product_name, o.order_id, u.name as user_name 
          FROM OrderItem oi 
          JOIN Product p ON oi.product_id = p.product_id 
          JOIN Orders o ON oi.order_id = o.order_id 
          JOIN Users u ON o.user_id = u.user_id";
if ($order_id_filter) {
    $query .= " WHERE oi.order_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$order_id_filter]);
} else {
    $stmt = $pdo->query($query);
}
$order_items = $stmt->fetchAll();

// Fetch orders for dropdown
$stmt = $pdo->query("SELECT order_id, user_id FROM Orders ORDER BY order_id DESC");
$orders = $stmt->fetchAll();

// Fetch products for dropdown
$stmt = $pdo->query("SELECT product_id, name FROM Product ORDER BY name");
$products = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-list-ul"></i> Order Items Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderItemModal">
        <i class="bi bi-plus-circle"></i> Add New Order Item
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
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?php echo $item['order_item_id']; ?></td>
                            <td><?php echo $item['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($item['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₹<?php echo number_format($item['unit_price'], 2); ?></td>
                            <td>₹<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editOrderItemModal" 
                                    data-id="<?php echo $item['order_item_id']; ?>"
                                    data-order-id="<?php echo $item['order_id']; ?>"
                                    data-product-id="<?php echo $item['product_id']; ?>"
                                    data-quantity="<?php echo $item['quantity']; ?>"
                                    data-unit-price="<?php echo $item['unit_price']; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteOrderItemModal" 
                                    data-id="<?php echo $item['order_item_id']; ?>">
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

<!-- Add Order Item Modal -->
<div class="modal fade" id="addOrderItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Order Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <select class="form-select" name="order_id" required>
                            <option value="">Select Order</option>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?php echo $order['order_id']; ?>" <?php echo ($order_id_filter && $order['order_id'] == $order_id_filter) ? 'selected' : ''; ?>>
                                    Order #<?php echo $order['order_id']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <select class="form-select" name="product_id" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit Price</label>
                        <input type="number" step="0.01" class="form-control" name="unit_price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Order Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Order Item Modal -->
<div class="modal fade" id="editOrderItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Order Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="order_item_id" id="edit_order_item_id">
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <select class="form-select" name="order_id" id="edit_order_id" required>
                            <option value="">Select Order</option>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?php echo $order['order_id']; ?>">Order #<?php echo $order['order_id']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <select class="form-select" name="product_id" id="edit_product_id" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" id="edit_quantity" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit Price</label>
                        <input type="number" step="0.01" class="form-control" name="unit_price" id="edit_unit_price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Order Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Order Item Modal -->
<div class="modal fade" id="deleteOrderItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Order Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="order_item_id" id="delete_order_item_id">
                    <p>Are you sure you want to delete this order item?</p>
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
    document.getElementById('editOrderItemModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_order_item_id').value = button.getAttribute('data-id');
        document.getElementById('edit_order_id').value = button.getAttribute('data-order-id');
        document.getElementById('edit_product_id').value = button.getAttribute('data-product-id');
        document.getElementById('edit_quantity').value = button.getAttribute('data-quantity');
        document.getElementById('edit_unit_price').value = button.getAttribute('data-unit-price');
    });

    document.getElementById('deleteOrderItemModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_order_item_id').value = button.getAttribute('data-id');
    });
</script>

<?php include('footer.php'); ?>

