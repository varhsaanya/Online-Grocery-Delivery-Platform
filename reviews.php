<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $user_id = $_POST['user_id'];
            $product_id = $_POST['product_id'];
            $rating = $_POST['rating'];
            $comment = $_POST['comment'];
            
            $stmt = $pdo->prepare("INSERT INTO Reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $product_id, $rating, $comment])) {
                $success = "Review added successfully!";
            } else {
                $error = "Error adding review!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $review_id = $_POST['review_id'];
            $user_id = $_POST['user_id'];
            $product_id = $_POST['product_id'];
            $rating = $_POST['rating'];
            $comment = $_POST['comment'];
            
            $stmt = $pdo->prepare("UPDATE Reviews SET user_id = ?, product_id = ?, rating = ?, comment = ? WHERE review_id = ?");
            if ($stmt->execute([$user_id, $product_id, $rating, $comment, $review_id])) {
                $success = "Review updated successfully!";
            } else {
                $error = "Error updating review!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $review_id = $_POST['review_id'];
            $stmt = $pdo->prepare("DELETE FROM Reviews WHERE review_id = ?");
            if ($stmt->execute([$review_id])) {
                $success = "Review deleted successfully!";
            } else {
                $error = "Error deleting review!";
            }
        }
    }
}

// Fetch all reviews with user and product names
$stmt = $pdo->query("SELECT r.*, u.name as user_name, p.name as product_name 
                     FROM Reviews r 
                     JOIN Users u ON r.user_id = u.user_id 
                     JOIN Product p ON r.product_id = p.product_id 
                     ORDER BY r.created_at DESC");
$reviews = $stmt->fetchAll();

// Fetch users for dropdown
$stmt = $pdo->query("SELECT user_id, name FROM Users ORDER BY name");
$users = $stmt->fetchAll();

// Fetch products for dropdown
$stmt = $pdo->query("SELECT product_id, name FROM Product ORDER BY name");
$products = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-star"></i> Reviews Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReviewModal">
        <i class="bi bi-plus-circle"></i> Add New Review
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
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Created At</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo $review['review_id']; ?></td>
                            <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                            <td>
                                <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $review['rating']) {
                                        echo '<i class="bi bi-star-fill text-warning"></i>';
                                    } else {
                                        echo '<i class="bi bi-star text-muted"></i>';
                                    }
                                }
                                ?>
                                <span class="ms-1">(<?php echo $review['rating']; ?>)</span>
                            </td>
                            <td><?php echo htmlspecialchars($review['comment']); ?></td>
                            <td><?php echo $review['created_at']; ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editReviewModal" 
                                    data-id="<?php echo $review['review_id']; ?>"
                                    data-user-id="<?php echo $review['user_id']; ?>"
                                    data-product-id="<?php echo $review['product_id']; ?>"
                                    data-rating="<?php echo $review['rating']; ?>"
                                    data-comment="<?php echo htmlspecialchars($review['comment']); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteReviewModal" 
                                    data-id="<?php echo $review['review_id']; ?>">
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

<!-- Add Review Modal -->
<div class="modal fade" id="addReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Review</h5>
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
                        <label class="form-label">Product</label>
                        <select class="form-select" name="product_id" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <select class="form-select" name="rating" required>
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea class="form-control" name="comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Review Modal -->
<div class="modal fade" id="editReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="review_id" id="edit_review_id">
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
                        <label class="form-label">Product</label>
                        <select class="form-select" name="product_id" id="edit_product_id" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <select class="form-select" name="rating" id="edit_rating" required>
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea class="form-control" name="comment" id="edit_comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Review Modal -->
<div class="modal fade" id="deleteReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="review_id" id="delete_review_id">
                    <p>Are you sure you want to delete this review?</p>
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
    document.getElementById('editReviewModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_review_id').value = button.getAttribute('data-id');
        document.getElementById('edit_user_id').value = button.getAttribute('data-user-id');
        document.getElementById('edit_product_id').value = button.getAttribute('data-product-id');
        document.getElementById('edit_rating').value = button.getAttribute('data-rating');
        document.getElementById('edit_comment').value = button.getAttribute('data-comment');
    });

    document.getElementById('deleteReviewModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_review_id').value = button.getAttribute('data-id');
    });
</script>

<?php include('footer.php'); ?>

