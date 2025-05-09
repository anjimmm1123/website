<?php
// Set page title
$pageTitle = 'Tambah Item Galeri - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!AdminMiddleware::validateCsrfToken()) {
        setFlashMessage('Invalid form submission, please try again.', 'danger');
    } else {
        // Check if image is uploaded
        if (empty($_FILES['image']['name']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            setFlashMessage('Gambar harus diupload.', 'danger');
        } else {
            // Sanitize input
            $galleryData = [
                'title' => AdminMiddleware::sanitizeInput($_POST['title']),
                'description' => AdminMiddleware::sanitizeInput($_POST['description']),
                'category' => AdminMiddleware::sanitizeInput($_POST['category']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            // Handle image upload
            $fileUploader = new FileUploader();
            $fileUploader->setUploadDir(UPLOAD_DIR . 'gallery/');
            
            $uploadResult = $fileUploader->upload($_FILES['image']);
            
            if ($uploadResult['success']) {
                $galleryData['image_url'] = $uploadResult['web_path'];
                
                // Create gallery item
                $galleryId = $adminManager->createGalleryItem($galleryData);
                
                if ($galleryId) {
                    setFlashMessage('Item galeri berhasil ditambahkan.', 'success');
                    header('Location: gallery.php');
                    exit;
                } else {
                    setFlashMessage('Gagal menambahkan item galeri.', 'danger');
                    
                    // Delete uploaded image since gallery item creation failed
                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . $uploadResult['web_path'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            } else {
                setFlashMessage('Error uploading image: ' . $uploadResult['message'], 'danger');
            }
        }
    }
}

// Get existing categories
try {
    $stmt = $db->query("SELECT DISTINCT category FROM gallery WHERE category IS NOT NULL AND category != ''");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $categories = [];
}

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Tambah Item Galeri</h1>
    <div>
        <a href="gallery.php" class="btn btn-secondary">
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
                        <label for="title" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                        <div class="invalid-feedback">Silakan masukkan judul.</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="category" name="category" value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : ''; ?>" list="category-list">
                            <datalist id="category-list">
                                <option value="general">General</option>
                                <option value="campus">Campus</option>
                                <option value="event">Event</option>
                                <option value="student">Student</option>
                                <option value="facility">Facility</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="form-text">Pilih kategori yang ada atau buat kategori baru.</div>
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group mb-3">
                <label for="image" class="form-label">Gambar <span class="text-danger">*</span></label>
                <input type="file" class="form-control image-upload" id="image" name="image" data-preview="preview_image" accept="image/*" required>
                <div class="invalid-feedback">Silakan pilih gambar.</div>
                <div class="mt-2">
                    <img id="preview_image" class="img-preview" style="display: none;">
                </div>
            </div>
            
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                <label class="form-check-label" for="is_active">
                    Aktif
                </label>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Simpan
                </button>
                <a href="gallery.php" class="btn btn-secondary ms-2">
                    <i class="fas fa-times me-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category input to lowercase
    const categoryInput = document.getElementById('category');
    categoryInput.addEventListener('blur', function() {
        this.value = this.value.toLowerCase();
    });
});
</script>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>