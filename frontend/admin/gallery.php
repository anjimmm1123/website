<?php
// Set page title
$pageTitle = 'Galeri - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Handle delete gallery item
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $galleryId = (int)$_GET['delete'];
    
    // Get gallery item to delete its image
    $galleryItem = $adminManager->getGalleryItemById($galleryId);
    
    // Delete gallery item
    if ($adminManager->deleteGalleryItem($galleryId)) {
        // Delete image file if exists
        if ($galleryItem && !empty($galleryItem['image_url'])) {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . $galleryItem['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        setFlashMessage('Item galeri berhasil dihapus.', 'success');
    } else {
        setFlashMessage('Gagal menghapus item galeri.', 'danger');
    }
    
    // Redirect to avoid resubmission
    header('Location: gallery.php');
    exit;
}

// Get gallery items
$galleryItems = $adminManager->getAllGalleryItems();

// Get categories
$categories = [];
if (!empty($galleryItems)) {
    foreach ($galleryItems as $item) {
        if (!empty($item['category']) && !in_array($item['category'], $categories)) {
            $categories[] = $item['category'];
        }
    }
}

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Galeri</h1>
    <div>
        <a href="gallery-add.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Tambah Gambar
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <?php if (!empty($categories)): ?>
        <div class="mb-4">
            <h5>Filter Kategori</h5>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active filter-btn" data-category="all">Semua</button>
                <?php foreach ($categories as $category): ?>
                <button type="button" class="btn btn-outline-primary filter-btn" data-category="<?php echo htmlspecialchars($category); ?>">
                    <?php echo htmlspecialchars(ucfirst($category)); ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (empty($galleryItems)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Belum ada item dalam galeri. Klik tombol "Tambah Gambar" untuk menambahkan.
        </div>
        <?php else: ?>
        <div class="row g-3 gallery-container">
            <?php foreach ($galleryItems as $item): ?>
            <div class="col-md-4 col-sm-6 gallery-item" data-category="<?php echo htmlspecialchars($item['category'] ?? 'general'); ?>">
                <div class="card h-100 card-lift gallery-card">
                    <div class="gallery-img-container">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="card-img-top gallery-img">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                        <p class="card-text">
                            <?php 
                            $description = $item['description'] ?? '';
                            echo (strlen($description) > 100) ? substr(htmlspecialchars($description), 0, 100) . '...' : htmlspecialchars($description);
                            ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info"><?php echo htmlspecialchars(ucfirst($item['category'] ?? 'general')); ?></span>
                            <div class="btn-group">
                                <a href="gallery-edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="gallery.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>Ditambahkan: <?php echo formatDate($item['created_at']); ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.gallery-img-container {
    height: 200px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;
}

.gallery-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-card:hover .gallery-img {
    transform: scale(1.05);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter gallery by category
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get category to filter
            const category = this.dataset.category;
            
            // Show/hide gallery items based on category
            galleryItems.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>