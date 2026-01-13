<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $code = $_POST['code'];
            $discount_type = $_POST['discount_type'];
            $discount_value = $_POST['discount_value'];
            $valid_from = $_POST['valid_from'];
            $valid_to = $_POST['valid_to'];
            $usage_limit = !empty($_POST['usage_limit']) ? $_POST['usage_limit'] : null;
            
            $stmt = $pdo->prepare("INSERT INTO Coupons (code, discount_type, discount_value, valid_from, valid_to, usage_limit) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$code, $discount_type, $discount_value, $valid_from, $valid_to, $usage_limit])) {
                $success = "Coupon added successfully!";
            } else {
                $error = "Error adding coupon!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $coupon_id = $_POST['coupon_id'];
            $code = $_POST['code'];
            $discount_type = $_POST['discount_type'];
            $discount_value = $_POST['discount_value'];
            $valid_from = $_POST['valid_from'];
            $valid_to = $_POST['valid_to'];
            $usage_limit = !empty($_POST['usage_limit']) ? $_POST['usage_limit'] : null;
            
            $stmt = $pdo->prepare("UPDATE Coupons SET code = ?, discount_type = ?, discount_value = ?, valid_from = ?, valid_to = ?, usage_limit = ? WHERE coupon_id = ?");
            if ($stmt->execute([$code, $discount_type, $discount_value, $valid_from, $valid_to, $usage_limit, $coupon_id])) {
                $success = "Coupon updated successfully!";
            } else {
                $error = "Error updating coupon!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $coupon_id = $_POST['coupon_id'];
            $stmt = $pdo->prepare("DELETE FROM Coupons WHERE coupon_id = ?");
            if ($stmt->execute([$coupon_id])) {
                $success = "Coupon deleted successfully!";
            } else {
                $error = "Error deleting coupon!";
            }
        }
    }
}

// Fetch all coupons
$stmt = $pdo->query("SELECT * FROM Coupons ORDER BY coupon_id DESC");
$coupons = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-ticket-perforated"></i> Coupons Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
        <i class="bi bi-plus-circle"></i> Add New Coupon
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
                        <th>Code</th>
                        <th>Discount Type</th>
                        <th>Discount Value</th>
                        <th>Valid From</th>
                        <th>Valid To</th>
                        <th>Usage Limit</th>
                        <th>Times Used</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $coupon): ?>
                        <tr>
                            <td><?php echo $coupon['coupon_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($coupon['code']); ?></strong></td>
                            <td><?php echo ucfirst($coupon['discount_type']); ?></td>
                            <td><?php echo $coupon['discount_type'] == 'percent' ? $coupon['discount_value'] . '%' : '₹' . number_format($coupon['discount_value'], 2); ?></td>
                            <td><?php echo $coupon['valid_from']; ?></td>
                            <td><?php echo $coupon['valid_to']; ?></td>
                            <td><?php echo $coupon['usage_limit'] ?? 'Unlimited'; ?></td>
                            <td><?php echo $coupon['times_used']; ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCouponModal" 
                                    data-id="<?php echo $coupon['coupon_id']; ?>"
                                    data-code="<?php echo htmlspecialchars($coupon['code']); ?>"
                                    data-discount-type="<?php echo $coupon['discount_type']; ?>"
                                    data-discount-value="<?php echo $coupon['discount_value']; ?>"
                                    data-valid-from="<?php echo $coupon['valid_from']; ?>"
                                    data-valid-to="<?php echo $coupon['valid_to']; ?>"
                                    data-usage-limit="<?php echo $coupon['usage_limit'] ?? ''; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCouponModal" 
                                    data-id="<?php echo $coupon['coupon_id']; ?>"
                                    data-code="<?php echo htmlspecialchars($coupon['code']); ?>">
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

<!-- Add Coupon Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Type</label>
                        <select class="form-select" name="discount_type" required>
                            <option value="percent">Percent</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Value</label>
                        <input type="number" step="0.01" class="form-control" name="discount_value" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valid From</label>
                        <input type="date" class="form-control" name="valid_from" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valid To</label>
                        <input type="date" class="form-control" name="valid_to" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usage Limit (Optional)</label>
                        <input type="number" class="form-control" name="usage_limit" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Coupon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Coupon Modal -->
<div class="modal fade" id="editCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="coupon_id" id="edit_coupon_id">
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" id="edit_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Type</label>
                        <select class="form-select" name="discount_type" id="edit_discount_type" required>
                            <option value="percent">Percent</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Value</label>
                        <input type="number" step="0.01" class="form-control" name="discount_value" id="edit_discount_value" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valid From</label>
                        <input type="date" class="form-control" name="valid_from" id="edit_valid_from" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valid To</label>
                        <input type="date" class="form-control" name="valid_to" id="edit_valid_to" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usage Limit (Optional)</label>
                        <input type="number" class="form-control" name="usage_limit" id="edit_usage_limit" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Coupon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Coupon Modal -->
<div class="modal fade" id="deleteCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="coupon_id" id="delete_coupon_id">
                    <p>Are you sure you want to delete coupon <strong id="delete_coupon_code"></strong>?</p>
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
    document.getElementById('editCouponModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_coupon_id').value = button.getAttribute('data-id');
        document.getElementById('edit_code').value = button.getAttribute('data-code');
        document.getElementById('edit_discount_type').value = button.getAttribute('data-discount-type');
        document.getElementById('edit_discount_value').value = button.getAttribute('data-discount-value');
        document.getElementById('edit_valid_from').value = button.getAttribute('data-valid-from');
        document.getElementById('edit_valid_to').value = button.getAttribute('data-valid-to');
        document.getElementById('edit_usage_limit').value = button.getAttribute('data-usage-limit') || '';
    });

    document.getElementById('deleteCouponModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_coupon_id').value = button.getAttribute('data-id');
        document.getElementById('delete_coupon_code').textContent = button.getAttribute('data-code');
    });
</script>

<?php include('footer.php'); ?>

