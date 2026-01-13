<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $status = $_POST['status'];
            
            $stmt = $pdo->prepare("INSERT INTO DeliveryPartners (name, phone, status) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $phone, $status])) {
                $success = "Delivery partner added successfully!";
            } else {
                $error = "Error adding delivery partner!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $rider_id = $_POST['rider_id'];
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $status = $_POST['status'];
            
            $stmt = $pdo->prepare("UPDATE DeliveryPartners SET name = ?, phone = ?, status = ? WHERE rider_id = ?");
            if ($stmt->execute([$name, $phone, $status, $rider_id])) {
                $success = "Delivery partner updated successfully!";
            } else {
                $error = "Error updating delivery partner!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $rider_id = $_POST['rider_id'];
            $stmt = $pdo->prepare("DELETE FROM DeliveryPartners WHERE rider_id = ?");
            if ($stmt->execute([$rider_id])) {
                $success = "Delivery partner deleted successfully!";
            } else {
                $error = "Error deleting delivery partner!";
            }
        }
    }
}

// Fetch all delivery partners
$stmt = $pdo->query("SELECT * FROM DeliveryPartners ORDER BY rider_id DESC");
$delivery_partners = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-truck"></i> Delivery Partners Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeliveryPartnerModal">
        <i class="bi bi-plus-circle"></i> Add New Delivery Partner
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
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($delivery_partners as $partner): ?>
                        <tr>
                            <td><?php echo $partner['rider_id']; ?></td>
                            <td><?php echo htmlspecialchars($partner['name']); ?></td>
                            <td><?php echo htmlspecialchars($partner['phone']); ?></td>
                            <td><span class="badge bg-<?php echo $partner['status'] == 'available' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($partner['status']); ?></span></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editDeliveryPartnerModal" 
                                    data-id="<?php echo $partner['rider_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($partner['name']); ?>"
                                    data-phone="<?php echo htmlspecialchars($partner['phone']); ?>"
                                    data-status="<?php echo htmlspecialchars($partner['status']); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDeliveryPartnerModal" 
                                    data-id="<?php echo $partner['rider_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($partner['name']); ?>">
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

<!-- Add Delivery Partner Modal -->
<div class="modal fade" id="addDeliveryPartnerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Delivery Partner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="available">Available</option>
                            <option value="busy">Busy</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Delivery Partner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Delivery Partner Modal -->
<div class="modal fade" id="editDeliveryPartnerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Delivery Partner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="rider_id" id="edit_rider_id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" id="edit_phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status" required>
                            <option value="available">Available</option>
                            <option value="busy">Busy</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Delivery Partner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Delivery Partner Modal -->
<div class="modal fade" id="deleteDeliveryPartnerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Delivery Partner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="rider_id" id="delete_rider_id">
                    <p>Are you sure you want to delete delivery partner <strong id="delete_rider_name"></strong>?</p>
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
    document.getElementById('editDeliveryPartnerModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_rider_id').value = button.getAttribute('data-id');
        document.getElementById('edit_name').value = button.getAttribute('data-name');
        document.getElementById('edit_phone').value = button.getAttribute('data-phone');
        document.getElementById('edit_status').value = button.getAttribute('data-status');
    });

    document.getElementById('deleteDeliveryPartnerModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_rider_id').value = button.getAttribute('data-id');
        document.getElementById('delete_rider_name').textContent = button.getAttribute('data-name');
    });
</script>

<?php include('footer.php'); ?>

