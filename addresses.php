<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $user_id = $_POST['user_id'];
            $street = $_POST['street'];
            $city = $_POST['city'];
            $state = $_POST['state'];
            
            $stmt = $pdo->prepare("INSERT INTO Addresses (user_id, street, city, state) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $street, $city, $state])) {
                $success = "Address added successfully!";
            } else {
                $error = "Error adding address!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $address_id = $_POST['address_id'];
            $user_id = $_POST['user_id'];
            $street = $_POST['street'];
            $city = $_POST['city'];
            $state = $_POST['state'];
            
            $stmt = $pdo->prepare("UPDATE Addresses SET user_id = ?, street = ?, city = ?, state = ? WHERE address_id = ?");
            if ($stmt->execute([$user_id, $street, $city, $state, $address_id])) {
                $success = "Address updated successfully!";
            } else {
                $error = "Error updating address!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $address_id = $_POST['address_id'];
            $stmt = $pdo->prepare("DELETE FROM Addresses WHERE address_id = ?");
            if ($stmt->execute([$address_id])) {
                $success = "Address deleted successfully!";
            } else {
                $error = "Error deleting address!";
            }
        }
    }
}

// Fetch all addresses with user names
$stmt = $pdo->query("SELECT a.*, u.name as user_name FROM Addresses a JOIN Users u ON a.user_id = u.user_id ORDER BY a.address_id DESC");
$addresses = $stmt->fetchAll();

// Fetch users for dropdown
$stmt = $pdo->query("SELECT user_id, name FROM Users ORDER BY name");
$users = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-geo-alt"></i> Addresses Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
        <i class="bi bi-plus-circle"></i> Add New Address
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
                        <th>Street</th>
                        <th>City</th>
                        <th>State</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($addresses as $address): ?>
                        <tr>
                            <td><?php echo $address['address_id']; ?></td>
                            <td><?php echo htmlspecialchars($address['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($address['street']); ?></td>
                            <td><?php echo htmlspecialchars($address['city']); ?></td>
                            <td><?php echo htmlspecialchars($address['state']); ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editAddressModal" 
                                    data-id="<?php echo $address['address_id']; ?>"
                                    data-user-id="<?php echo $address['user_id']; ?>"
                                    data-street="<?php echo htmlspecialchars($address['street']); ?>"
                                    data-city="<?php echo htmlspecialchars($address['city']); ?>"
                                    data-state="<?php echo htmlspecialchars($address['state']); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAddressModal" 
                                    data-id="<?php echo $address['address_id']; ?>">
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

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <select class="form-select" name="user_id" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
                    <button type="submit" class="btn btn-primary">Add Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="address_id" id="edit_address_id">
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <select class="form-select" name="user_id" id="edit_user_id" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
                    <button type="submit" class="btn btn-warning">Update Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Address Modal -->
<div class="modal fade" id="deleteAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="address_id" id="delete_address_id">
                    <p>Are you sure you want to delete this address?</p>
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
    document.getElementById('editAddressModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_address_id').value = button.getAttribute('data-id');
        document.getElementById('edit_user_id').value = button.getAttribute('data-user-id');
        document.getElementById('edit_street').value = button.getAttribute('data-street');
        document.getElementById('edit_city').value = button.getAttribute('data-city');
        document.getElementById('edit_state').value = button.getAttribute('data-state');
    });

    document.getElementById('deleteAddressModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_address_id').value = button.getAttribute('data-id');
    });
</script>

<?php include('footer.php'); ?>

