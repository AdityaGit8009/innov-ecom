<?php
require_once __DIR__ . '/check_admin.php';
include "includes/header.php";

function h($value)
{
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$edit = null;

if (isset($_GET['edit'])) {
  $edit_id = (int)$_GET['edit'];
  $editQ = mysqli_query($conn, "SELECT * FROM training_technologies WHERE id='$edit_id' LIMIT 1");
  $edit = $editQ ? mysqli_fetch_assoc($editQ) : null;
}

if (isset($_GET['delete'])) {
  $delete_id = (int)$_GET['delete'];

  mysqli_query($conn, "DELETE FROM training_technologies WHERE id='$delete_id'");

  echo "<script>
        alert('Technology deleted successfully');
        window.location.href='manage_training_technologies.php';
    </script>";
  exit;
}

$techQ = mysqli_query($conn, "SELECT * FROM training_technologies ORDER BY sort_order ASC, id DESC");
?>
<style>
  .action-dropdown .btn {
    border: 0;
    background: transparent;
    color: #334155;
    font-size: 20px;
    padding: 4px 8px;
    border-radius: 8px;
  }

  .action-dropdown .btn:hover,
  .action-dropdown .btn:focus {
    background: #f1f5f9;
    color: #0f172a;
    box-shadow: none;
  }

  .action-dropdown .dropdown-toggle::after {
    display: none;
  }

  .action-dropdown .dropdown-menu {
    border: 0;
    border-radius: 12px;
    box-shadow: 0 12px 35px rgba(15, 23, 42, 0.15);
    padding: 8px;
    min-width: 160px;
  }

  .action-dropdown .dropdown-item {
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #334155;
  }

  .action-dropdown .dropdown-item:hover {
    background: #f8fafc;
  }

  .action-dropdown .dropdown-item.text-danger:hover {
    background: #fee2e2;
    color: #991b1b !important;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">
    <i class="bx bx-book-content"></i>
    Manage Training Technologies
  </h4>

  <a href="manage_training_technologies.php" class="btn btn-secondary btn-sm">
    <i class="bx bx-refresh"></i> Reset
  </a>
</div>

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">Technology saved successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
  <div class="alert alert-danger">Please enter Technology Title and Slug.</div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'slug_exists'): ?>
  <div class="alert alert-danger">This slug already exists. Please use another slug.</div>
<?php endif; ?>

<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title">
      <?= $edit ? 'Edit Technology' : 'Add Technology'; ?>
    </h5>

    <form action="save_training_technology.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= h($edit['id'] ?? 0); ?>">

      <div class="row g-3">
        <div class="col-lg-6">
          <label class="form-label">Technology Title</label>
          <input type="text" name="title" class="form-control"
            value="<?= h($edit['title'] ?? ''); ?>" required>
        </div>

        <div class="col-lg-6">
          <label class="form-label">Slug</label>
          <input type="text" name="slug" class="form-control"
            value="<?= h($edit['slug'] ?? ''); ?>"
            placeholder="embedded-system" required>
        </div>

        <div class="col-lg-6">
          <label class="form-label">Card Image</label>
          <input type="file" name="image" class="form-control">

          <?php if (!empty($edit['image'])): ?>
            <div class="mt-2">
              <img src="../<?= h($edit['image']); ?>" height="80" class="rounded border">
            </div>
          <?php endif; ?>
        </div>

        <div class="col-lg-6">
          <label class="form-label">Detail Page Image</label>
          <input type="file" name="detail_image" class="form-control">

          <?php if (!empty($edit['detail_image'])): ?>
            <div class="mt-2">
              <img src="../<?= h($edit['detail_image']); ?>" height="80" class="rounded border">
            </div>
          <?php endif; ?>
        </div>

        <div class="col-lg-4">
          <label class="form-label">Short Text</label>
          <input type="text" name="short_text" class="form-control"
            value="<?= h($edit['short_text'] ?? 'Certification'); ?>">
        </div>

        <div class="col-lg-4">
          <label class="form-label">Rating</label>
          <input type="number" name="rating" class="form-control"
            min="1" max="5"
            value="<?= h($edit['rating'] ?? 5); ?>">
        </div>

        <div class="col-lg-4">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-control"
            value="<?= h($edit['sort_order'] ?? 0); ?>">
        </div>

        <div class="col-lg-12">
          <label class="form-label">Detail Page Title</label>
          <input type="text" name="detail_title" class="form-control"
            value="<?= h($edit['detail_title'] ?? ''); ?>"
            placeholder="EMBEDDED SYSTEM & EMBEDDED PROGRAMMING">
        </div>

        <div class="col-lg-12">
          <label class="form-label">Detail Description</label>
          <textarea name="detail_description" rows="5" class="form-control"><?= h($edit['detail_description'] ?? ''); ?></textarea>
        </div>

        <div class="col-lg-8">
          <label class="form-label">Batch Text</label>
          <input type="text" name="batch_text" class="form-control"
            value="<?= h($edit['batch_text'] ?? 'New Batch Starting Soon – Enroll Today!'); ?>">
        </div>

        <div class="col-lg-4">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <option value="1" <?= isset($edit['status']) && $edit['status'] == 1 ? 'selected' : ''; ?>>Active</option>
            <option value="0" <?= isset($edit['status']) && $edit['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
          </select>
        </div>
      </div>

      <div class="mt-3">
        <button class="btn btn-success">
          <i class="bx bx-save"></i>
          <?= $edit ? 'Update Technology' : 'Save Technology'; ?>
        </button>

        <?php if ($edit): ?>
          <a href="manage_training_technologies.php" class="btn btn-secondary">
            Cancel
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Technology List</h5>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th width="60">Sr.</th>
            <th width="90">Image</th>
            <th>Title</th>
            <th>Slug</th>
            <th>Rating</th>
            <th>Status</th>
            <th width="80">Action</th>
          </tr>
        </thead>

        <tbody>
          <?php if ($techQ && mysqli_num_rows($techQ) > 0): ?>
            <?php $i = 1;
            while ($tech = mysqli_fetch_assoc($techQ)): ?>
              <tr>
                <td><?= $i++; ?></td>

                <td>
                  <?php if (!empty($tech['image'])): ?>
                    <img src="../<?= h($tech['image']); ?>" height="55" class="rounded border">
                  <?php else: ?>
                    <span class="text-muted">No Image</span>
                  <?php endif; ?>
                </td>

                <td><?= h($tech['title']); ?></td>
                <td><?= h($tech['slug']); ?></td>
                <td><?= (int)$tech['rating']; ?> Star</td>

                <td>
                  <?php if ((int)$tech['status'] === 1): ?>
                    <span class="badge bg-success">Active</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Inactive</span>
                  <?php endif; ?>
                </td>

                <td class="text-center">
                  <div class="dropdown action-dropdown">
                    <button class="btn dropdown-toggle"
                      type="button"
                      data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                      <li>
                        <a href="manage_technology_content.php?id=<?= (int)$tech['id']; ?>" class="dropdown-item">
                          <i class="bx bx-layer"></i> Content
                        </a>
                      </li>

                      <li>
                        <a href="manage_training_technologies.php?edit=<?= (int)$tech['id']; ?>" class="dropdown-item">
                          <i class="bx bx-edit"></i> Edit
                        </a>
                      </li>

                      <li>
                        <a href="../technology.php?slug=<?= urlencode($tech['slug']); ?>" target="_blank" class="dropdown-item">
                          <i class="bx bx-show"></i> View
                        </a>
                      </li>

                      <li>
                        <a href="manage_training_technologies.php?delete=<?= (int)$tech['id']; ?>"
                          onclick="return confirm('Are you sure?')"
                          class="dropdown-item text-danger">
                          <i class="bx bx-trash"></i> Delete
                        </a>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted">No technology found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>