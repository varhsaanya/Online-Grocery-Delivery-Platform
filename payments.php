<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $order_id = $_POST['order_id'];
            $amount = $_POST['amount'];
            $payment_method = $_POST['payment_method'];
            $status = $_POST['status'];
            $transaction_ref = !empty($_POST['transaction_ref']) ? $_POST['transaction_ref'] : null;
            
            $stmt = $pdo->prepare("INSERT INTO Payment (order_id, amount, payment_method, status, transaction_ref) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$order_id, $amount, $payment_method, $status, $transaction_ref])) {
                $payment_id = $pdo->lastInsertId();
                // Update order with payment_id
                $stmt = $pdo->prepare("UPDATE Orders SET payment_id = ? WHERE order_id = ?");
                $stmt->execute([$payment_id, $order_id]);
                $success = "Payment added successfully!";
            } else {
                $error = "Error adding payment!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $payment_id = $_POST['payment_id'];
            $order_id = $_POST['order_id'];
            $amount = $_POST['amount'];
            $payment_method = $_POST['payment_method'];
            $status = $_POST['status'];
            $transaction_ref = !empty($_POST['transaction_ref']) ? $_POST['transaction_ref'] : null;
            
            $stmt = $pdo->prepare("UPDATE Payment SET order_id = ?, amount = ?, payment_method = ?, status = ?, transaction_ref = ? WHERE payment_id = ?");
            if ($stmt->execute([$order_id, $amount, $payment_method, $status, $transaction_ref, $payment_id])) {
                $success = "Payment updated successfully!";
            } else {
                $error = "Error updating payment!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $payment_id = $_POST['payment_id'];
            $stmt = $pdo->prepare("DELETE FROM Payment WHERE payment_id = ?");
            if ($stmt->execute([$payment_id])) {
                $success = "Payment deleted successfully!";
            } else {
                $error = "Error deleting payment!";
            }
        }
    }
}

// Fetch all payments with order details
$stmt = $pdo->query("SELECT p.*, o.order_id, u.name as user_name 
                     FROM Payment p 
                     JOIN Orders o ON p.order_id = o.order_id 
                     JOIN Users u ON o.user_id = u.user_id 
                     ORDER BY p.payment_id DESC");
$payments = $stmt->fetchAll();

// Fetch orders for dropdown
$stmt = $pdo->query("SELECT order_id, user_id FROM Orders ORDER BY order_id DESC");
$orders = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-credit-card"></i> Payments Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
        <i class="bi bi-plus-circle"></i> Add New Payment
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
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Transaction Ref</th>
                        <th>Payment Time</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo $payment['payment_id']; ?></td>
                            <td><?php echo $payment['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($payment['user_name']); ?></td>
                            <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td><span class="badge bg-<?php echo $payment['status'] == 'completed' ? 'success' : ($payment['status'] == 'pending' ? 'warning' : 'danger'); ?>"><?php echo htmlspecialchars($payment['status']); ?></span></td>
                            <td><?php echo $payment['transaction_ref'] ? htmlspecialchars($payment['transaction_ref']) : 'N/A'; ?></td>
                            <td><?php echo $payment['payment_time']; ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPaymentModal" 
                                    data-id="<?php echo $payment['payment_id']; ?>"
                                    data-order-id="<?php echo $payment['order_id']; ?>"
                                    data-amount="<?php echo $payment['amount']; ?>"
                                    data-payment-method="<?php echo htmlspecialchars($payment['payment_method']); ?>"
                                    data-status="<?php echo htmlspecialchars($payment['status']); ?>"
                                    data-transaction-ref="<?php echo htmlspecialchars($payment['transaction_ref'] ?? ''); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deletePaymentModal" 
                                    data-id="<?php echo $payment['payment_id']; ?>">
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

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Payment</h5>
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
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="upi">UPI</option>
                            <option value="net_banking">Net Banking</option>
                            <option value="wallet">Wallet</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Reference (Optional)</label>
                        <input type="text" class="form-control" name="transaction_ref">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="payment_id" id="edit_payment_id">
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
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" name="amount" id="edit_amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" id="edit_payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="upi">UPI</option>
                            <option value="net_banking">Net Banking</option>
                            <option value="wallet">Wallet</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Reference (Optional)</label>
                        <input type="text" class="form-control" name="transaction_ref" id="edit_transaction_ref">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Payment Modal -->
<div class="modal fade" id="deletePaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="payment_id" id="delete_payment_id">
                    <p>Are you sure you want to delete this payment?</p>
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
    document.getElementById('editPaymentModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_payment_id').value = button.getAttribute('data-id');
        document.getElementById('edit_order_id').value = button.getAttribute('data-order-id');
        document.getElementById('edit_amount').value = button.getAttribute('data-amount');
        document.getElementById('edit_payment_method').value = button.getAttribute('data-payment-method');
        document.getElementById('edit_status').value = button.getAttribute('data-status');
        document.getElementById('edit_transaction_ref').value = button.getAttribute('data-transaction-ref') || '';
    });

    document.getElementById('deletePaymentModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_payment_id').value = button.getAttribute('data-id');
    });
</script>

<?php include('footer.php'); ?>

