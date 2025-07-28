<?php

if (!defined('_TanHien')) {
    die('Access denied');
}

// Expected variables passed in before include:
// $modules (array of modules)
// $post (optional, associative array if editing)
// $filter (optional, form data after validation failure)
// $errorArr (optional, validation errors)
// $msg and $msg_type (optional, flash messages)
// $isEdit (boolean), $isAdmin (boolean), $userId (int)

$selectedModuleId = !empty($filter['module_id']) ? $filter['module_id'] : (!empty($post['module_id']) ? $post['module_id'] : '');
$content = !empty($filter['content']) ? htmlspecialchars($filter['content']) : (!empty($post['content']) ? htmlspecialchars($post['content']) : '');

?>

<form class="post-form" action="" method="POST" enctype="multipart/form-data">

  <div class="cover-section">
    <button type="button" class="add-cover-btn">
      <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd"
          d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
          clip-rule="evenodd"></path>
      </svg>
      <span>Add Cover</span>
    </button>

    <input type="file" id="cover_image" name="cover_image" accept="image/*" style="display: none;">

    <div id="image-preview" class="image-preview" style="display: none;">
      <!-- Preview image will be inserted here -->
    </div>

    <button type="button" id="remove-image" class="remove-image-btn" style="display: none;">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd"
          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
          clip-rule="evenodd"></path>
      </svg>
      Remove Image
    </button>

    <div class="error">
      <?php echo !empty($errorArr['cover_image']) ? reset($errorArr['cover_image']) : ''; ?>
    </div>
  </div>

  <div class="form-group">
    <select class="module-select" name="module_id">
      <option value="">Select a module...</option>
      <?php foreach ($modules as $module): ?>
      <option value="<?php echo $module['module_id']; ?>"
        <?php echo ($selectedModuleId == $module['module_id']) ? 'selected' : ''; ?>>
        <?php echo htmlspecialchars($module['module_name']); ?>
      </option>
      <?php endforeach; ?>
    </select>
    <div class="error">
      <?php echo !empty($errorArr['module_id']) ? reset($errorArr['module_id']) : ''; ?>
    </div>
  </div>

  <div class="content-area">
    <textarea class="content-textarea" name="content"
      placeholder="Write your post content here..."><?php echo $content; ?></textarea>
    <div class="error">
      <?php echo !empty($errorArr['content']) ? reset($errorArr['content']) : ''; ?>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Post' : 'Create Post'; ?></button>
    <a href="?module=dashboard&action=dashboard_posts" class="btn btn-secondary">Cancel</a>
  </div>

</form>

<?php if ($isEdit && $isAdmin && !empty($post) && $post['user_id'] != $userId): ?>
<div class="admin-info">
  <p><strong>Admin Note:</strong> You are editing a post by user: <?php echo htmlspecialchars($post['username']); ?></p>
</div>
<?php endif; ?>