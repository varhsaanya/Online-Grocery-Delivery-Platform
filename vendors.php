<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $name = $_POST['name'];
            $contact_email = $_POST['contact_email'];
            $contact_phone = $_POST['contact_phone'];
            
            $stmt = $pdo->prepare("INSERT INTO Vendors (name, contact_email, contact_phone) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $contact_email, $contact_phone])) {
                $success = "Vendor added successfully!";
            } else {
                $error = "Error adding vendor!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $vendor_id = $_POST['vendor_id'];
            $name = $_POST['name'];
            $contact_email = $_POST['contact_email'];
            $contact_phone = $_POST['contact_phone'];
            
            $stmt = $pdo->prepare("UPDATE Vendors SET name = ?, contact_email = ?, contact_phone = ? WHERE vendor_id = ?");
            if ($stmt->execute([$name, $contact_email, $contact_phone, $vendor_id])) {
                $success = "Vendor updated successfully!";
            } else {
                $error = "Error updating vendor!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $vendor_id = $_POST['vendor_id'];
            $stmt = $pdo->prepare("DELETE FROM Vendors WHERE vendor_id = ?");
            if ($stmt->execute([$vendor_id])) {
                $success = "Vendor deleted successfully!";
            } else {
                $error = "Error deleting vendor!";
            }
        }
    }
}

// Fetch all vendors
$stmt = $pdo->query("SELECT * FROM Vendors ORDER BY vendor_id DESC");
$vendors = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-building"></i> Vendors Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVendorModal">
        <i class="bi bi-plus-circle"></i> Add New Vendor
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
                        <th>Contact Email</th>
                        <th>Contact Phone</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendors as $vendor): ?>
                        <tr>
                            <td><?php echo $vendor['vendor_id']; ?></td>
                            <td><?php echo htmlspecialchars($vendor['name']); ?></td>
                            <td><?php echo htmlspecialchars($vendor['contact_email']); ?></td>
                            <td><?php echo htmlspecialchars($vendor['contact_phone']); ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editVendorModal" 
                                    data-id="<?php echo $vendor['vendor_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($vendor['name']); ?>"
                                    data-email="<?php echo htmlspecialchars($vendor['contact_email']); ?>"
                                    data-phone="<?php echo htmlspecialchars($vendor['contact_phone']); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteVendorModal" 
                                    data-id="<?php echo $vendor['vendor_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($vendor['name']); ?>">
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

<!-- Add Vendor Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" class="form-control" name="contact_email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" class="form-control" name="contact_phone">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vendor Modal -->
<div class="modal fade" id="editVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="vendor_id" id="edit_vendor_id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" class="form-control" name="contact_email" id="edit_email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" class="form-control" name="contact_phone" id="edit_phone">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Vendor Modal -->
<div class="modal fade" id="deleteVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="vendor_id" id="delete_vendor_id">
                    <p>Are you sure you want to delete vendor <strong id="delete_vendor_name"></strong>?</p>
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
    document.getElementById('editVendorModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_vendor_id').value = button.getAttribute('data-id');
        document.getElementById('edit_name').value = button.getAttribute('data-name');
        document.getElementById('edit_email').value = button.getAttribute('data-email');
        document.getElementById('edit_phone').value = button.getAttribute('data-phone');
    });

    document.getElementById('deleteVendorModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_vendor_id').value = button.getAttribute('data-id');
        document.getElementById('delete_vendor_name').textContent = button.getAttribute('data-name');
    });
</script>

<?php include('footer.php'); ?>

