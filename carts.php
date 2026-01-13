<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $user_id = $_POST['user_id'];
            
            $stmt = $pdo->prepare("INSERT INTO Carts (user_id) VALUES (?)");
            if ($stmt->execute([$user_id])) {
                $success = "Cart added successfully!";
            } else {
                $error = "Error adding cart!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $cart_id = $_POST['cart_id'];
            $stmt = $pdo->prepare("DELETE FROM Carts WHERE cart_id = ?");
            if ($stmt->execute([$cart_id])) {
                $success = "Cart deleted successfully!";
            } else {
                $error = "Error deleting cart!";
            }
        }
    }
}

// Fetch all carts with user names
$stmt = $pdo->query("SELECT c.*, u.name as user_name FROM Carts c JOIN Users u ON c.user_id = u.user_id ORDER BY c.cart_id DESC");
$carts = $stmt->fetchAll();

// Fetch users for dropdown
$stmt = $pdo->query("SELECT user_id, name FROM Users ORDER BY name");
$users = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cart"></i> Carts Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCartModal">
        <i class="bi bi-plus-circle"></i> Add New Cart
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
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carts as $cart): ?>
                        <tr>
                            <td><?php echo $cart['cart_id']; ?></td>
                            <td><?php echo htmlspecialchars($cart['user_name']); ?></td>
                            <td><?php echo $cart['created_at']; ?></td>
                            <td><?php echo $cart['updated_at']; ?></td>
                            <td class="table-actions">
                                <a href="cart_items.php?cart_id=<?php echo $cart['cart_id']; ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> View Items
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCartModal" 
                                    data-id="<?php echo $cart['cart_id']; ?>">
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

<!-- Add Cart Modal -->
<div class="modal fade" id="addCartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Cart</h5>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Cart</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Cart Modal -->
<div class="modal fade" id="deleteCartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="cart_id" id="delete_cart_id">
                    <p>Are you sure you want to delete this cart?</p>
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
    document.getElementById('deleteCartModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_cart_id').value = button.getAttribute('data-id');
    });
</script>

<?php include('footer.php'); ?>

