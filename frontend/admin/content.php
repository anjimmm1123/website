<?php
// Set page title
$pageTitle = 'Edit Konten - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Get pages from database
try {
    $stmt = $db->query("SELECT DISTINCT page_name FROM page_content ORDER BY page_name");
    $pages = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    setFlashMessage('Error retrieving pages: ' . $e->getMessage(), 'danger');
    $pages = [];
}

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Konten Website</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Kelola Konten Halaman</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Pilih halaman untuk mengedit konten:</p>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 card-lift">
                            <div class="card-body text-center">
                                <div class="icon-circle mx-auto">
                                    <i class="fas fa-home"></i>
                                </div>
                                <h5 class="card-title">Halaman Utama</h5>
                                <p class="card-text">Edit konten halaman beranda, banner, dan highlight.</p>
                                <a href="content-edit.php?page=home" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i> Edit Konten
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 card-lift">
                            <div class="card-body text-center">
                                <div class="icon-circle mx-auto">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <h5 class="card-title">Tentang Kami</h5>
                                <p class="card-text">Edit informasi institusi, visi, misi, dan sejarah.</p>
                                <a href="content-edit.php?page=about" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i> Edit Konten
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 card-lift">
                            <div class="card-body text-center">
                                <div class="icon-circle mx-auto">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <h5 class="card-title">Program Studi</h5>
                                <p class="card-text">Edit deskripsi program studi dan informasi akademik.</p>
                                <a href="content-edit.php?page=programs" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i> Edit Konten
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 card-lift">
                            <div class="card-body text-center">
                                <div class="icon-circle mx-auto">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h5 class="card-title">Fasilitas</h5>
                                <p class="card-text">Edit informasi fasilitas kampus dan sarana prasarana.</p>
                                <a href="content-edit.php?page=facilities" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i> Edit Konten
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 card-lift">
                            <div class="card-body text-center">
                                <div class="icon-circle mx-auto">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <h5 class="card-title">Pendaftaran</h5>
                                <p class="card-text">Edit persyaratan pendaftaran dan prosedur penerimaan.</p>
                                <a href="content-edit.php?page=admission" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i> Edit Konten
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 card-lift">
                            <div class="card-body text-center">
                                <div class="icon-circle mx-auto">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <h5 class="card-title">Kontak</h5>
                                <p class="card-text">Edit informasi kontak dan lokasi kampus.</p>
                                <a href="content-edit.php?page=contact" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i> Edit Konten
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($pages)): ?>
                <hr class="my-4">
                <h5>Halaman Lainnya</h5>
                <div class="row g-3">
                    <?php foreach ($pages as $page): ?>
                        <?php if (!in_array($page, ['home', 'about', 'programs', 'facilities', 'admission', 'contact'])): ?>
                        <div class="col-md-3">
                            <a href="content-edit.php?page=<?php echo urlencode($page); ?>" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-file-alt me-2"></i> <?php echo ucfirst(htmlspecialchars($page)); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <div class="col-md-3">
                        <a href="content-add.php" class="btn btn-success w-100 py-3">
                            <i class="fas fa-plus me-2"></i> Tambah Halaman
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Elemen Global</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Edit elemen yang muncul di beberapa halaman:</p>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="content-edit.php?page=global&section=header" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-heading me-2"></i> Header &amp; Menu
                        </a>
                    </div>
                    
                    <div class="col-md-4">
                        <a href="content-edit.php?page=global&section=footer" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-paragraph me-2"></i> Footer &amp; Copyright
                        </a>
                    </div>
                    
                    <div class="col-md-4">
                        <a href="content-edit.php?page=global&section=seo" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-search me-2"></i> SEO &amp; Meta Tags
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>