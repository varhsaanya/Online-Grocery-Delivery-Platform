<?php
include('db.php');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $name = $_POST['name'];
            $parent_category_id = !empty($_POST['parent_category_id']) ? $_POST['parent_category_id'] : null;
            
            $stmt = $pdo->prepare("INSERT INTO Categories (name, parent_category_id) VALUES (?, ?)");
            if ($stmt->execute([$name, $parent_category_id])) {
                $success = "Category added successfully!";
            } else {
                $error = "Error adding category!";
            }
        } elseif ($_POST['action'] == 'edit') {
            $category_id = $_POST['category_id'];
            $name = $_POST['name'];
            $parent_category_id = !empty($_POST['parent_category_id']) ? $_POST['parent_category_id'] : null;
            
            $stmt = $pdo->prepare("UPDATE Categories SET name = ?, parent_category_id = ? WHERE category_id = ?");
            if ($stmt->execute([$name, $parent_category_id, $category_id])) {
                $success = "Category updated successfully!";
            } else {
                $error = "Error updating category!";
            }
        } elseif ($_POST['action'] == 'delete') {
            $category_id = $_POST['category_id'];
            $stmt = $pdo->prepare("DELETE FROM Categories WHERE category_id = ?");
            if ($stmt->execute([$category_id])) {
                $success = "Category deleted successfully!";
            } else {
                $error = "Error deleting category!";
            }
        }
    }
}

// Fetch all categories with parent names
$stmt = $pdo->query("SELECT c.*, p.name as parent_name FROM Categories c LEFT JOIN Categories p ON c.parent_category_id = p.category_id ORDER BY c.category_id DESC");
$categories = $stmt->fetchAll();

// Fetch categories for parent dropdown
$stmt = $pdo->query("SELECT category_id, name FROM Categories ORDER BY name");
$parent_categories = $stmt->fetchAll();

include('header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-tags"></i> Categories Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="bi bi-plus-circle"></i> Add New Category
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
                        <th>Parent Category</th>
                        <th class="table-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['category_id']; ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo $category['parent_name'] ? htmlspecialchars($category['parent_name']) : 'None'; ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCategoryModal" 
                                    data-id="<?php echo $category['category_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                    data-parent-id="<?php echo $category['parent_category_id'] ?? ''; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal" 
                                    data-id="<?php echo $category['category_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($category['name']); ?>">
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Category (Optional)</label>
                        <select class="form-select" name="parent_category_id">
                            <option value="">None</option>
                            <?php foreach ($parent_categories as $parent): ?>
                                <option value="<?php echo $parent['category_id']; ?>"><?php echo htmlspecialchars($parent['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Category (Optional)</label>
                        <select class="form-select" name="parent_category_id" id="edit_parent_id">
                            <option value="">None</option>
                            <?php foreach ($parent_categories as $parent): ?>
                                <option value="<?php echo $parent['category_id']; ?>"><?php echo htmlspecialchars($parent['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="category_id" id="delete_category_id">
                    <p>Are you sure you want to delete category <strong id="delete_category_name"></strong>?</p>
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
    document.getElementById('editCategoryModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_category_id').value = button.getAttribute('data-id');
        document.getElementById('edit_name').value = button.getAttribute('data-name');
        document.getElementById('edit_parent_id').value = button.getAttribute('data-parent-id') || '';
    });

    document.getElementById('deleteCategoryModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('delete_category_id').value = button.getAttribute('data-id');
        document.getElementById('delete_category_name').textContent = button.getAttribute('data-name');
    });
</script>

<?php include('footer.php'); ?>

