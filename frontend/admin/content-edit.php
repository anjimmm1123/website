<?php
// Set page title
$pageTitle = 'Edit Konten - Admin Panel';

// Include admin configuration
require_once __DIR__ . '/config.php';

// Check if user is authenticated
AdminMiddleware::requireAuth();

// Create admin manager instance
$adminManager = new AdminManager($db, $currentAdminUser);

// Get page name
$pageName = isset($_GET['page']) ? AdminMiddleware::sanitizeInput($_GET['page']) : '';
$sectionName = isset($_GET['section']) ? AdminMiddleware::sanitizeInput($_GET['section']) : '';

if (empty($pageName)) {
    setFlashMessage('Halaman tidak ditemukan.', 'danger');
    header('Location: content.php');
    exit;
}

// Update page title
$pageTitle = 'Edit Konten ' . ucfirst($pageName) . ' - Admin Panel';

// Get page content
$pageContent = $adminManager->getPageContent($pageName);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!AdminMiddleware::validateCsrfToken()) {
        setFlashMessage('Invalid form submission, please try again.', 'danger');
    } else {
        // Process each section
        foreach ($_POST['content'] as $sectionId => $content) {
            if (is_numeric($sectionId)) {
                // Update existing section
                $sectionData = [
                    'title' => AdminMiddleware::sanitizeInput($content['title']),
                    'content' => $content['content'], // Don't sanitize HTML content
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Handle image upload if provided
                if (!empty($_FILES['image']['name'][$sectionId]) && $_FILES['image']['error'][$sectionId] === UPLOAD_ERR_OK) {
                    $fileUploader = new FileUploader();
                    $fileUploader->setUploadDir(UPLOAD_DIR . 'content/');
                    
                    $uploadResult = $fileUploader->upload([
                        'name' => $_FILES['image']['name'][$sectionId],
                        'type' => $_FILES['image']['type'][$sectionId],
                        'tmp_name' => $_FILES['image']['tmp_name'][$sectionId],
                        'error' => $_FILES['image']['error'][$sectionId],
                        'size' => $_FILES['image']['size'][$sectionId]
                    ]);
                    
                    if ($uploadResult['success']) {
                        $sectionData['image_url'] = $uploadResult['web_path'];
                    } else {
                        setFlashMessage('Error uploading image: ' . $uploadResult['message'], 'warning');
                    }
                }
                
                $adminManager->updatePageContent($sectionId, $sectionData);
            } else {
                // Add new section
                $newSection = $sectionId;
                
                $sectionData = [
                    'page_name' => $pageName,
                    'section_name' => $newSection,
                    'title' => AdminMiddleware::sanitizeInput($content['title']),
                    'content' => $content['content'], // Don't sanitize HTML content
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Handle image upload if provided
                if (!empty($_FILES['image']['name'][$newSection]) && $_FILES['image']['error'][$newSection] === UPLOAD_ERR_OK) {
                    $fileUploader = new FileUploader();
                    $fileUploader->setUploadDir(UPLOAD_DIR . 'content/');
                    
                    $uploadResult = $fileUploader->upload([
                        'name' => $_FILES['image']['name'][$newSection],
                        'type' => $_FILES['image']['type'][$newSection],
                        'tmp_name' => $_FILES['image']['tmp_name'][$newSection],
                        'error' => $_FILES['image']['error'][$newSection],
                        'size' => $_FILES['image']['size'][$newSection]
                    ]);
                    
                    if ($uploadResult['success']) {
                        $sectionData['image_url'] = $uploadResult['web_path'];
                    } else {
                        setFlashMessage('Error uploading image: ' . $uploadResult['message'], 'warning');
                    }
                }
                
                $adminManager->createPageContent($sectionData);
            }
        }
        
        setFlashMessage('Konten halaman berhasil diperbarui.', 'success');
        header('Location: content-edit.php?page=' . urlencode($pageName) . (empty($sectionName) ? '' : '&section=' . urlencode($sectionName)));
        exit;
    }
}

// Page section titles
$pageTitles = [
    'home' => 'Halaman Utama',
    'about' => 'Tentang Kami',
    'programs' => 'Program Studi',
    'facilities' => 'Fasilitas',
    'admission' => 'Pendaftaran',
    'contact' => 'Kontak',
    'global' => 'Elemen Global'
];

// Default sections for each page
$defaultSections = [
    'home' => [
        'hero' => 'Banner Utama',
        'welcome' => 'Sambutan',
        'features' => 'Fitur Unggulan',
        'stats' => 'Statistik',
        'programs' => 'Program Studi',
        'testimonials' => 'Testimonial',
        'cta' => 'Call to Action'
    ],
    'about' => [
        'hero' => 'Banner',
        'history' => 'Sejarah',
        'vision' => 'Visi',
        'mission' => 'Misi',
        'values' => 'Nilai-nilai',
        'leadership' => 'Kepemimpinan',
        'achievements' => 'Prestasi'
    ],
    'programs' => [
        'hero' => 'Banner',
        'intro' => 'Pengantar',
        'program_list' => 'Daftar Program',
        'curriculum' => 'Kurikulum',
        'facilities' => 'Fasilitas',
        'faculty' => 'Fakultas',
        'careers' => 'Karir'
    ],
    'facilities' => [
        'hero' => 'Banner',
        'intro' => 'Pengantar',
        'academic' => 'Fasilitas Akademik',
        'student' => 'Fasilitas Mahasiswa',
        'sports' => 'Fasilitas Olahraga',
        'housing' => 'Asrama',
        'technology' => 'Teknologi'
    ],
    'admission' => [
        'hero' => 'Banner',
        'intro' => 'Pengantar',
        'requirements' => 'Persyaratan',
        'timeline' => 'Timeline',
        'fees' => 'Biaya',
        'scholarships' => 'Beasiswa',
        'faq' => 'FAQ'
    ],
    'contact' => [
        'hero' => 'Banner',
        'info' => 'Informasi Kontak',
        'form' => 'Form Kontak',
        'map' => 'Peta',
        'offices' => 'Kantor',
        'hours' => 'Jam Kerja'
    ],
    'global' => [
        'header' => 'Header & Menu',
        'footer' => 'Footer & Copyright',
        'seo' => 'SEO & Meta Tags'
    ]
];

// Include header
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Konten: <?php echo isset($pageTitles[$pageName]) ? $pageTitles[$pageName] : ucfirst($pageName); ?></h1>
    <div>
        <a href="content.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
        <?php if (empty($sectionName)): ?>
        <a href="/" class="btn btn-primary ms-2" target="_blank">
            <i class="fas fa-external-link-alt me-2"></i> Lihat Halaman
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <?php
            // Get default sections for this page
            $sections = isset($defaultSections[$pageName]) ? $defaultSections[$pageName] : [];
            
            // Filter by section if specified
            if (!empty($sectionName) && isset($sections[$sectionName])) {
                $filteredSections = [$sectionName => $sections[$sectionName]];
                $sections = $filteredSections;
            }
            
            // If no page content yet, show default sections for this page
            if (empty($pageContent) && !empty($sections)):
                foreach ($sections as $sectionId => $sectionTitle):
                    if (empty($sectionName) || $sectionName === $sectionId):
            ?>
            <div class="content-section mb-4 p-4 border rounded">
                <h4 class="section-title"><?php echo htmlspecialchars($sectionTitle); ?></h4>
                <div class="form-group mb-3">
                    <label for="title_<?php echo $sectionId; ?>" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="title_<?php echo $sectionId; ?>" name="content[<?php echo $sectionId; ?>][title]" value="">
                </div>
                
                <div class="form-group mb-3">
                    <label for="content_<?php echo $sectionId; ?>" class="form-label">Konten</label>
                    <textarea class="form-control summernote" id="content_<?php echo $sectionId; ?>" name="content[<?php echo $sectionId; ?>][content]" rows="6"></textarea>
                </div>
                
                <div class="form-group mb-3">
                    <label for="image_<?php echo $sectionId; ?>" class="form-label">Gambar (opsional)</label>
                    <input type="file" class="form-control image-upload" id="image_<?php echo $sectionId; ?>" name="image[<?php echo $sectionId; ?>]" data-preview="preview_<?php echo $sectionId; ?>" accept="image/*">
                    <div class="mt-2">
                        <img id="preview_<?php echo $sectionId; ?>" class="img-preview" style="display: none;">
                    </div>
                </div>
            </div>
            <?php
                    endif;
                endforeach;
            endif;
            
            // Display existing content
            if (!empty($pageContent)):
                foreach ($pageContent as $section):
                    if (empty($sectionName) || $sectionName === $section['section_name']):
                        $sectionTitle = isset($sections[$section['section_name']]) ? $sections[$section['section_name']] : ucfirst(str_replace('_', ' ', $section['section_name']));
            ?>
            <div class="content-section mb-4 p-4 border rounded">
                <h4 class="section-title"><?php echo htmlspecialchars($sectionTitle); ?></h4>
                <div class="form-group mb-3">
                    <label for="title_<?php echo $section['id']; ?>" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="title_<?php echo $section['id']; ?>" name="content[<?php echo $section['id']; ?>][title]" value="<?php echo htmlspecialchars($section['title']); ?>">
                </div>
                
                <div class="form-group mb-3">
                    <label for="content_<?php echo $section['id']; ?>" class="form-label">Konten</label>
                    <textarea class="form-control summernote" id="content_<?php echo $section['id']; ?>" name="content[<?php echo $section['id']; ?>][content]" rows="6"><?php echo htmlspecialchars($section['content']); ?></textarea>
                </div>
                
                <div class="form-group mb-3">
                    <label for="image_<?php echo $section['id']; ?>" class="form-label">Gambar (opsional)</label>
                    <input type="file" class="form-control image-upload" id="image_<?php echo $section['id']; ?>" name="image[<?php echo $section['id']; ?>]" data-preview="preview_<?php echo $section['id']; ?>" accept="image/*">
                    <div class="mt-2">
                        <?php if (!empty($section['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($section['image_url']); ?>" id="preview_<?php echo $section['id']; ?>" class="img-preview" style="display: block; max-height: 200px;">
                        <?php else: ?>
                        <img id="preview_<?php echo $section['id']; ?>" class="img-preview" style="display: none;">
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group mb-0">
                    <div class="form-text">
                        <small>Terakhir diperbarui: <?php echo formatDate($section['updated_at']); ?></small>
                    </div>
                </div>
            </div>
            <?php
                    endif;
                endforeach;
            endif;
            
            // If no sections found for this page (and not filtered by section)
            if (empty($pageContent) && empty($sections) && empty($sectionName)):
            ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Belum ada konten untuk halaman ini. Tambahkan konten baru dengan mengisi form di bawah ini.
            </div>
            
            <div class="content-section mb-4 p-4 border rounded">
                <h4 class="section-title">Konten Baru</h4>
                
                <div class="form-group mb-3">
                    <label for="section_name" class="form-label">Nama Bagian</label>
                    <input type="text" class="form-control" id="section_name" name="section_name" placeholder="Contoh: intro, banner, about, etc." required>
                    <div class="form-text">Gunakan huruf kecil tanpa spasi, gunakan underscore untuk pemisah kata.</div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="title_new" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="title_new" name="content[new_section][title]" value="">
                </div>
                
                <div class="form-group mb-3">
                    <label for="content_new" class="form-label">Konten</label>
                    <textarea class="form-control summernote" id="content_new" name="content[new_section][content]" rows="6"></textarea>
                </div>
                
                <div class="form-group mb-3">
                    <label for="image_new" class="form-label">Gambar (opsional)</label>
                    <input type="file" class="form-control image-upload" id="image_new" name="image[new_section]" data-preview="preview_new" accept="image/*">
                    <div class="mt-2">
                        <img id="preview_new" class="img-preview" style="display: none;">
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Summernote WYSIWYG editor
    $('.summernote').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                // Upload image to server
                for (let i = 0; i < files.length; i++) {
                    uploadImage(files[i], this);
                }
            }
        }
    });
    
    // Image upload function for Summernote
    function uploadImage(file, editor) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('csrf_token', '<?php echo $csrfToken; ?>');
        
        fetch('upload-image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $(editor).summernote('insertImage', data.url);
            } else {
                alert('Gagal mengupload gambar: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupload gambar.');
        });
    }
    
    // Handle section name input (for new sections)
    const sectionNameInput = document.getElementById('section_name');
    if (sectionNameInput) {
        sectionNameInput.addEventListener('blur', function() {
            // Convert to lowercase and replace spaces with underscores
            this.value = this.value.toLowerCase().replace(/\s+/g, '_');
        });
    }
});
</script>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>