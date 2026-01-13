<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $product_id = $_POST['product_id'];
            $store_id = $_POST['store_id'];
            $quantity = $_POST['quantity'];
            
            $stmt = $pdo->prepare("INSERT INTO Inventory (product_id, store_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = ?");
            if ($stmt->execute([$product_id, $store_id, $quantity, $quantity])) {
                $success = "Inventory added/updated successfully!";
            } else {
                $error = "Error adding inventory!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $inventory_id = $_POST['inventory_id'];
            $product_id = $_POST['product_id'];
            $store_id = $_POST['store_id'];
            $quantity = $_POST['quantity'];
            
            $stmt = $pdo->prepare("UPDATE Inventory SET product_id = ?, store_id = ?, quantity = ? WHERE inventory_id = ?");
            if ($stmt->execute([$product_id, $store_id, $quantity, $inventory_id])) {
                $success = "Inventory updated successfully!";
            } else {
                $error = "Error updating inventory!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $inventory_id = $_POST['inventory_id'];
            $stmt = $pdo->prepare("DELETE FROM Inventory WHERE inventory_id = ?");
            if ($stmt->execute([$inventory_id])) {
                $success = "Inventory deleted successfully!";
            } else {
                $error = "Error deleting inventory!";
            }
        }
    }
}

// Fetch all inventory with product and store names
$stmt = $pdo->query("SELECT i.*, p.name as product_name, s.name as store_name FROM Inventory i JOIN Product p ON i.product_id = p.product_id JOIN Stores s ON i.store_id = s.store_id ORDER BY i.inventory_id DESC");
$inventory = $stmt->fetchAll();

// Fetch products for dropdown
$stmt = $pdo->query("SELECT product_id, name FROM Product ORDER BY name");
$products = $stmt->fetchAll();

// Fetch stores for dropdown
$stmt = $pdo->query("SELECT store_id, name FROM Stores ORDER BY name");
$stores = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clipboard-data"></i> Inventory Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
        <i class="bi bi-plus-circle"></i> Add New Inventory
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
                        <th>Product</th>
                        <th>Store</th>
                        <th>Quantity</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory as $item): ?>
                        <tr>
                            <td><?php echo $item['inventory_id']; ?></td>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['store_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editInventoryModal" 
                                    data-id="<?php echo $item['inventory_id']; ?>"
                                    data-product-id="<?php echo $item['product_id']; ?>"
                                    data-store-id="<?php echo $item['store_id']; ?>"
                                    data-quantity="<?php echo $item['quantity']; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteInventoryModal" 
                                    data-id="<?php echo $item['inventory_id']; ?>">
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

<!-- Add Inventory Modal -->
<div class="modal fade" id="addInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
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
                        <label class="form-label">Store</label>
                        <select class="form-select" name="store_id" required>
                            <option value="">Select Store</option>
                            <?php foreach ($stores as $store): ?>
                                <option value="<?php echo $store['store_id']; ?>"><?php echo htmlspecialchars($store['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Inventory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Inventory Modal -->
<div class="modal fade" id="editInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="inventory_id" id="edit_inventory_id">
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
                        <label class="form-label">Store</label>
                        <select class="form-select" name="store_id" id="edit_store_id" required>
                            <option value="">Select Store</option>
                            <?php foreach ($stores as $store): ?>
                                <option value="<?php echo $store['store_id']; ?>"><?php echo htmlspecialchars($store['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" id="edit_quantity" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Inventory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Inventory Modal -->
<div class="modal fade" id="deleteInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="inventory_id" id="delete_inventory_id">
                    <p>Are you sure you want to delete this inventory record?</p>
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
    document.getElementById('editInventoryModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_inventory_id').value = button.getAttribute('data-id');
        document.getElementById('edit_product_id').value = button.getAttribute('data-product-id');
        document.getElementById('edit_store_id').value = button.getAttribute('data-store-id');
        document.getElementById('edit_quantity').value = button.getAttribute('data-quantity');
    });

    document.getElementById('deleteInventoryModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_inventory_id').value = button.getAttribute('data-id');
    });
</script>

<?php include('footer.php'); ?>

