<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $vendor_id = $_POST['vendor_id'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            $unit = $_POST['unit'];
            
            $stmt = $pdo->prepare("INSERT INTO Product (vendor_id, name, price, category_id, unit) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$vendor_id, $name, $price, $category_id, $unit])) {
                $success = "Product added successfully!";
            } else {
                $error = "Error adding product!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $product_id = $_POST['product_id'];
            $vendor_id = $_POST['vendor_id'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            $unit = $_POST['unit'];
            
            $stmt = $pdo->prepare("UPDATE Product SET vendor_id = ?, name = ?, price = ?, category_id = ?, unit = ? WHERE product_id = ?");
            if ($stmt->execute([$vendor_id, $name, $price, $category_id, $unit, $product_id])) {
                $success = "Product updated successfully!";
            } else {
                $error = "Error updating product!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $product_id = $_POST['product_id'];
            $stmt = $pdo->prepare("DELETE FROM Product WHERE product_id = ?");
            if ($stmt->execute([$product_id])) {
                $success = "Product deleted successfully!";
            } else {
                $error = "Error deleting product!";
            }
        }
    }
}

// Fetch all products with vendor and category names
$stmt = $pdo->query("SELECT p.*, v.name as vendor_name, c.name as category_name FROM Product p JOIN Vendors v ON p.vendor_id = v.vendor_id JOIN Categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC");
$products = $stmt->fetchAll();

// Fetch vendors for dropdown
$stmt = $pdo->query("SELECT vendor_id, name FROM Vendors ORDER BY name");
$vendors = $stmt->fetchAll();

// Fetch categories for dropdown
$stmt = $pdo->query("SELECT category_id, name FROM Categories ORDER BY name");
$categories = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam"></i> Products Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="bi bi-plus-circle"></i> Add New Product
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
                        <th>Name</th>
                        <th>Vendor</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Unit</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['product_id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['vendor_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td>₹<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($product['unit']); ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal" 
                                    data-id="<?php echo $product['product_id']; ?>"
                                    data-vendor-id="<?php echo $product['vendor_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                    data-price="<?php echo $product['price']; ?>"
                                    data-category-id="<?php echo $product['category_id']; ?>"
                                    data-unit="<?php echo htmlspecialchars($product['unit']); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal" 
                                    data-id="<?php echo $product['product_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($product['name']); ?>">
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Vendor</label>
                        <select class="form-select" name="vendor_id" required>
                            <option value="">Select Vendor</option>
                            <?php foreach ($vendors as $vendor): ?>
                                <option value="<?php echo $vendor['vendor_id']; ?>"><?php echo htmlspecialchars($vendor['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <input type="text" class="form-control" name="unit" placeholder="e.g., kg, piece, liter" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="mb-3">
                        <label class="form-label">Vendor</label>
                        <select class="form-select" name="vendor_id" id="edit_vendor_id" required>
                            <option value="">Select Vendor</option>
                            <?php foreach ($vendors as $vendor): ?>
                                <option value="<?php echo $vendor['vendor_id']; ?>"><?php echo htmlspecialchars($vendor['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" id="edit_price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" id="edit_category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <input type="text" class="form-control" name="unit" id="edit_unit" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="product_id" id="delete_product_id">
                    <p>Are you sure you want to delete product <strong id="delete_product_name"></strong>?</p>
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
    document.getElementById('editProductModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_product_id').value = button.getAttribute('data-id');
        document.getElementById('edit_vendor_id').value = button.getAttribute('data-vendor-id');
        document.getElementById('edit_name').value = button.getAttribute('data-name');
        document.getElementById('edit_price').value = button.getAttribute('data-price');
        document.getElementById('edit_category_id').value = button.getAttribute('data-category-id');
        document.getElementById('edit_unit').value = button.getAttribute('data-unit');
    });

    document.getElementById('deleteProductModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_product_id').value = button.getAttribute('data-id');
        document.getElementById('delete_product_name').textContent = button.getAttribute('data-name');
    });
</script>

<?php include('footer.php'); ?>

