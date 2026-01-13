<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $name = $_POST['name'];
            $street = $_POST['street'];
            $city = $_POST['city'];
            $state = $_POST['state'];
            
            $stmt = $pdo->prepare("INSERT INTO Stores (name, street, city, state) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $street, $city, $state])) {
                $success = "Store added successfully!";
            } else {
                $error = "Error adding store!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $store_id = $_POST['store_id'];
            $name = $_POST['name'];
            $street = $_POST['street'];
            $city = $_POST['city'];
            $state = $_POST['state'];
            
            $stmt = $pdo->prepare("UPDATE Stores SET name = ?, street = ?, city = ?, state = ? WHERE store_id = ?");
            if ($stmt->execute([$name, $street, $city, $state, $store_id])) {
                $success = "Store updated successfully!";
            } else {
                $error = "Error updating store!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $store_id = $_POST['store_id'];
            $stmt = $pdo->prepare("DELETE FROM Stores WHERE store_id = ?");
            if ($stmt->execute([$store_id])) {
                $success = "Store deleted successfully!";
            } else {
                $error = "Error deleting store!";
            }
        }
    }
}

// Fetch all stores
$stmt = $pdo->query("SELECT * FROM Stores ORDER BY store_id DESC");
$stores = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-shop"></i> Stores Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoreModal">
        <i class="bi bi-plus-circle"></i> Add New Store
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
                        <th>Street</th>
                        <th>City</th>
                        <th>State</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stores as $store): ?>
                        <tr>
                            <td><?php echo $store['store_id']; ?></td>
                            <td><?php echo htmlspecialchars($store['name']); ?></td>
                            <td><?php echo htmlspecialchars($store['street']); ?></td>
                            <td><?php echo htmlspecialchars($store['city']); ?></td>
                            <td><?php echo htmlspecialchars($store['state']); ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editStoreModal" 
                                    data-id="<?php echo $store['store_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($store['name']); ?>"
                                    data-street="<?php echo htmlspecialchars($store['street']); ?>"
                                    data-city="<?php echo htmlspecialchars($store['city']); ?>"
                                    data-state="<?php echo htmlspecialchars($store['state']); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteStoreModal" 
                                    data-id="<?php echo $store['store_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($store['name']); ?>">
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

<!-- Add Store Modal -->
<div class="modal fade" id="addStoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Store</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Street</label>
                        <input type="text" class="form-control" name="street" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" name="state" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Store</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Store Modal -->
<div class="modal fade" id="editStoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Store</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="store_id" id="edit_store_id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Street</label>
                        <input type="text" class="form-control" name="street" id="edit_street" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="city" id="edit_city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" name="state" id="edit_state" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Store</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Store Modal -->
<div class="modal fade" id="deleteStoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Store</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="store_id" id="delete_store_id">
                    <p>Are you sure you want to delete store <strong id="delete_store_name"></strong>?</p>
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
    document.getElementById('editStoreModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_store_id').value = button.getAttribute('data-id');
        document.getElementById('edit_name').value = button.getAttribute('data-name');
        document.getElementById('edit_street').value = button.getAttribute('data-street');
        document.getElementById('edit_city').value = button.getAttribute('data-city');
        document.getElementById('edit_state').value = button.getAttribute('data-state');
    });

    document.getElementById('deleteStoreModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_store_id').value = button.getAttribute('data-id');
        document.getElementById('delete_store_name').textContent = button.getAttribute('data-name');
    });
</script>

<?php include('footer.php'); ?>

