<?php
// Set page title
$pageTitle = 'Edit Program Studi - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Check if program ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlashMessage('ID program studi tidak valid.', 'danger');
    header('Location: programs.php');
    exit;
}

$programId = (int)$_GET['id'];

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Get program data
$program = $adminManager->getProgramById($programId);

if (!$program) {
    setFlashMessage('Program studi tidak ditemukan.', 'danger');
    header('Location: programs.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!AdminMiddleware::validateCsrfToken()) {
        setFlashMessage('Invalid form submission, please try again.', 'danger');
    } else {
        // Sanitize input
        $programData = [
            'code' => AdminMiddleware::sanitizeInput($_POST['code']),
            'name' => AdminMiddleware::sanitizeInput($_POST['name']),
            'description' => $_POST['description'], // Don't sanitize HTML content
            'curriculum' => $_POST['curriculum'], // Don't sanitize HTML content
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Handle image upload if provided
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileUploader = new FileUploader();
            $fileUploader->setUploadDir(UPLOAD_DIR . 'programs/');
            
            $uploadResult = $fileUploader->upload($_FILES['image']);
            
            if ($uploadResult['success']) {
                $programData['image_url'] = $uploadResult['web_path'];
                
                // Delete old image if exists
                if (!empty($program['image_url'])) {
                    $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . $program['image_url'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            } else {
                setFlashMessage('Error uploading image: ' . $uploadResult['message'], 'warning');
            }
        } else {
            // Keep existing image
            $programData['image_url'] = $program['image_url'];
        }
        
        // Update program
        if ($adminManager->updateProgram($programId, $programData)) {
            setFlashMessage('Program studi berhasil diperbarui.', 'success');
            header('Location: programs.php');
            exit;
        } else {
            setFlashMessage('Gagal memperbarui program studi.', 'danger');
        }
    }
}

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Program Studi</h1>
    <div>
        <a href="programs.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code" class="form-label">Kode Program <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($program['code']); ?>" required>
                        <div class="invalid-feedback">Silakan masukkan kode program.</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Nama Program <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($program['name']); ?>" required>
                        <div class="invalid-feedback">Silakan masukkan nama program.</div>
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control summernote" id="description" name="description" rows="6" required><?php echo htmlspecialchars($program['description']); ?></textarea>
                <div class="invalid-feedback">Silakan masukkan deskripsi program.</div>
            </div>
            
            <div class="form-group mb-3">
                <label for="curriculum" class="form-label">Kurikulum</label>
                <textarea class="form-control summernote" id="curriculum" name="curriculum" rows="6"><?php echo htmlspecialchars($program['curriculum'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group mb-3">
                <label for="image" class="form-label">Gambar Program</label>
                <input type="file" class="form-control image-upload" id="image" name="image" data-preview="preview_image" accept="image/*">
                <div class="mt-2">
                    <?php if (!empty($program['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($program['image_url']); ?>" id="preview_image" class="img-preview" style="display: block;">
                    <?php else: ?>
                    <img id="preview_image" class="img-preview" style="display: none;">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo $program['is_active'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_active">
                    Program Aktif
                </label>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
                <a href="programs.php" class="btn btn-secondary ms-2">
                    <i class="fas fa-times me-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>