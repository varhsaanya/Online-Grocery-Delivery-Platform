<?php
include('db.php');

$cart_id_filter = isset($_GET['cart_id']) ? $_GET['cart_id'] : null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $cart_id = $_POST['cart_id'];
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            
            $stmt = $pdo->prepare("INSERT INTO CartItem (cart_id, product_id, quantity) VALUES (?, ?, ?)");
            if ($stmt->execute([$cart_id, $product_id, $quantity])) {
                $success = "Cart item added successfully!";
            } else {
                $error = "Error adding cart item!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $cart_item_id = $_POST['cart_item_id'];
            $cart_id = $_POST['cart_id'];
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            
            $stmt = $pdo->prepare("UPDATE CartItem SET cart_id = ?, product_id = ?, quantity = ? WHERE cart_item_id = ?");
            if ($stmt->execute([$cart_id, $product_id, $quantity, $cart_item_id])) {
                $success = "Cart item updated successfully!";
            } else {
                $error = "Error updating cart item!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $cart_item_id = $_POST['cart_item_id'];
            $stmt = $pdo->prepare("DELETE FROM CartItem WHERE cart_item_id = ?");
            if ($stmt->execute([$cart_item_id])) {
                $success = "Cart item deleted successfully!";
            } else {
                $error = "Error deleting cart item!";
            }
        }
    }
}

// Build query with optional filter
$query = "SELECT ci.*, p.name as product_name, c.cart_id, u.name as user_name 
          FROM CartItem ci 
          JOIN Product p ON ci.product_id = p.product_id 
          JOIN Carts c ON ci.cart_id = c.cart_id 
          JOIN Users u ON c.user_id = u.user_id";
if ($cart_id_filter) {
    $query .= " WHERE ci.cart_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$cart_id_filter]);
} else {
    $stmt = $pdo->query($query);
}
$cart_items = $stmt->fetchAll();

// Fetch carts for dropdown
$stmt = $pdo->query("SELECT cart_id, user_id FROM Carts ORDER BY cart_id DESC");
$carts = $stmt->fetchAll();

// Fetch products for dropdown
$stmt = $pdo->query("SELECT product_id, name FROM Product ORDER BY name");
$products = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart-check"></i> Cart Items Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCartItemModal">
        <i class="bi bi-plus-circle"></i> Add New Cart Item
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
                        <th>Cart ID</th>
                        <th>User</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo $item['cart_item_id']; ?></td>
                            <td><?php echo $item['cart_id']; ?></td>
                            <td><?php echo htmlspecialchars($item['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCartItemModal" 
                                    data-id="<?php echo $item['cart_item_id']; ?>"
                                    data-cart-id="<?php echo $item['cart_id']; ?>"
                                    data-product-id="<?php echo $item['product_id']; ?>"
                                    data-quantity="<?php echo $item['quantity']; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCartItemModal" 
                                    data-id="<?php echo $item['cart_item_id']; ?>">
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

<!-- Add Cart Item Modal -->
<div class="modal fade" id="addCartItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Cart Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Cart</label>
                        <select class="form-select" name="cart_id" required>
                            <option value="">Select Cart</option>
                            <?php foreach ($carts as $cart): ?>
                                <option value="<?php echo $cart['cart_id']; ?>" <?php echo ($cart_id_filter && $cart['cart_id'] == $cart_id_filter) ? 'selected' : ''; ?>>
                                    Cart #<?php echo $cart['cart_id']; ?>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Cart Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Cart Item Modal -->
<div class="modal fade" id="editCartItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Cart Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="cart_item_id" id="edit_cart_item_id">
                    <div class="mb-3">
                        <label class="form-label">Cart</label>
                        <select class="form-select" name="cart_id" id="edit_cart_id" required>
                            <option value="">Select Cart</option>
                            <?php foreach ($carts as $cart): ?>
                                <option value="<?php echo $cart['cart_id']; ?>">Cart #<?php echo $cart['cart_id']; ?></option>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Cart Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Cart Item Modal -->
<div class="modal fade" id="deleteCartItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Cart Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="cart_item_id" id="delete_cart_item_id">
                    <p>Are you sure you want to delete this cart item?</p>
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
    document.getElementById('editCartItemModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_cart_item_id').value = button.getAttribute('data-id');
        document.getElementById('edit_cart_id').value = button.getAttribute('data-cart-id');
        document.getElementById('edit_product_id').value = button.getAttribute('data-product-id');
        document.getElementById('edit_quantity').value = button.getAttribute('data-quantity');
    });

    document.getElementById('deleteCartItemModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_cart_item_id').value = button.getAttribute('data-id');
    });
</script>

<?php include('footer.php'); ?>

