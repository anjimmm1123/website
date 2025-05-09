<?php
// Page title sudah ditetapkan di index.php
require_once __DIR__ . '/../../backend/auth/Auth.php';

// Mendapatkan daftar gambar dari database
try {
    $query = "SELECT * FROM gallery WHERE is_active = 1 ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $gallery_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $gallery_images = [];
    error_log('Error mengambil data galeri: ' . $e->getMessage());
}

// Filter by category if set
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>

<!-- CSS -->
<link rel="stylesheet" href="frontend/assets/css/gallery.css">

<!-- Main Content -->
<div class="container my-5">
    <h1 class="text-center mb-5 animate__animated animate__fadeIn">Galeri STMIK Enterprise</h1>

    <!-- Filter Categories -->
    <div class="text-center mb-4">
        <div class="btn-group" role="group">
            <a href="?page=gallery" class="btn <?php echo $category === 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">Semua</a>
            <a href="?page=gallery&category=kampus" class="btn <?php echo $category === 'kampus' ? 'btn-primary' : 'btn-outline-primary'; ?>">Kampus</a>
            <a href="?page=gallery&category=kegiatan" class="btn <?php echo $category === 'kegiatan' ? 'btn-primary' : 'btn-outline-primary'; ?>">Kegiatan</a>
            <a href="?page=gallery&category=mahasiswa" class="btn <?php echo $category === 'mahasiswa' ? 'btn-primary' : 'btn-outline-primary'; ?>">Mahasiswa</a>
            <a href="?page=gallery&category=prestasi" class="btn <?php echo $category === 'prestasi' ? 'btn-primary' : 'btn-outline-primary'; ?>">Prestasi</a>
        </div>
    </div>

    <!-- Gallery Images -->
    <div class="row g-4" id="gallery-container">
        <?php if (empty($gallery_images)): ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Belum ada gambar dalam galeri.
                </div>
                <!-- Placeholder images for demo -->
                <div class="row g-4 mt-2">
                    <?php for ($i = 1; $i <= 9; $i++): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card gallery-item animate__animated animate__fadeIn">
                                <img src="frontend/assets/images/gallery-placeholder-<?php echo $i; ?>.svg" class="card-img-top" alt="Placeholder Image">
                                <div class="card-body">
                                    <h5 class="card-title">Gambar Placeholder #<?php echo $i; ?></h5>
                                    <p class="card-text">Ini adalah contoh gambar placeholder untuk galeri.</p>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($gallery_images as $image): ?>
                <?php if ($category === 'all' || $image['category'] === $category): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card gallery-item animate__animated animate__fadeIn">
                            <a href="<?php echo htmlspecialchars($image['image_url']); ?>" data-lightbox="gallery" data-title="<?php echo htmlspecialchars($image['title']); ?>">
                                <img src="<?php echo htmlspecialchars($image['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($image['title']); ?>">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($image['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($image['description']); ?></p>
                                <p class="text-muted"><small>Kategori: <?php echo ucfirst(htmlspecialchars($image['category'])); ?></small></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Lightbox JS for image preview -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    // Initialize lightbox
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': "Gambar %1 dari %2"
    });
</script>