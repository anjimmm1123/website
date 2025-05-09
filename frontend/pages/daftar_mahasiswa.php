<?php
$page_title = 'Pendaftaran Mahasiswa Baru - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../backend/config/functions.php';
require_once __DIR__ . '/../../backend/auth/Auth.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form processing will be handled here
    $errors = [];
    $success = false;
    
    // Basic validation
    if (empty($_POST['full_name'])) $errors[] = 'Nama lengkap harus diisi';
    if (empty($_POST['email'])) $errors[] = 'Email harus diisi';
    if (empty($_POST['phone'])) $errors[] = 'Nomor telepon harus diisi';
    if (empty($_POST['program'])) $errors[] = 'Program studi harus dipilih';
    
    if (empty($errors)) {
        // Process registration
        // This will be implemented later
        $success = true;
    }
}
?>

<!-- CSS -->
<link rel="stylesheet" href="/frontend/assets/css/common.css">
<link rel="stylesheet" href="/frontend/assets/css/registration.css">

<!-- Hero Section -->
<section class="hero position-relative overflow-hidden">
    <div class="hero-bg position-absolute w-100 h-100">
        <div class="overlay position-absolute w-100 h-100 bg-dark opacity-75"></div>
    </div>
    
    <div class="container position-relative py-5">
        <div class="row min-vh-50 align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <h1 class="display-4 text-white mb-4">Pendaftaran Mahasiswa Baru</h1>
                <p class="lead text-white-50">
                    Bergabunglah dengan kami dan mulai perjalanan akademis Anda
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Registration Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Registration Form -->
            <div class="col-lg-8" data-aos="fade-right">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h3 mb-4">Formulir Pendaftaran</h2>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($success) && $success): ?>
                            <div class="alert alert-success">
                                Pendaftaran berhasil! Silakan cek email Anda untuk informasi selanjutnya.
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" class="needs-validation" novalidate enctype="multipart/form-data">
                            <!-- Personal Information -->
                            <div class="mb-4">
                                <h3 class="h5 mb-3">Informasi Pribadi</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                                        <div class="invalid-feedback">Nama lengkap harus diisi</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="birth_place" class="form-label">Tempat Lahir</label>
                                        <input type="text" class="form-control" id="birth_place" name="birth_place" required>
                                        <div class="invalid-feedback">Tempat lahir harus diisi</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                        <div class="invalid-feedback">Tanggal lahir harus diisi</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Jenis Kelamin</label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                        <div class="invalid-feedback">Jenis kelamin harus dipilih</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="religion" class="form-label">Agama</label>
                                        <select class="form-select" id="religion" name="religion" required>
                                            <option value="">Pilih Agama</option>
                                            <option value="Islam">Islam</option>
                                            <option value="Kristen">Kristen</option>
                                            <option value="Katolik">Katolik</option>
                                            <option value="Hindu">Hindu</option>
                                            <option value="Buddha">Buddha</option>
                                            <option value="Konghucu">Konghucu</option>
                                        </select>
                                        <div class="invalid-feedback">Agama harus dipilih</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="marital_status" class="form-label">Status Pernikahan</label>
                                        <select class="form-select" id="marital_status" name="marital_status" required>
                                            <option value="">Pilih Status</option>
                                            <option value="Belum Menikah">Belum Menikah</option>
                                            <option value="Menikah">Menikah</option>
                                        </select>
                                        <div class="invalid-feedback">Status pernikahan harus dipilih</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="mb-4">
                                <h3 class="h5 mb-3">Informasi Kontak</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="invalid-feedback">Email harus diisi dengan format yang valid</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Nomor Telepon</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                        <div class="invalid-feedback">Nomor telepon harus diisi</div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="address" class="form-label">Alamat Lengkap</label>
                                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                        <div class="invalid-feedback">Alamat lengkap harus diisi</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Academic Information -->
                            <div class="mb-4">
                                <h3 class="h5 mb-3">Informasi Akademik</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="program" class="form-label">Program Studi</label>
                                        <select class="form-select" id="program" name="program" required>
                                            <option value="">Pilih Program Studi</option>
                                            <option value="TI">Teknik Informatika (S1)</option>
                                            <option value="SI">Sistem Informasi (S1)</option>
                                            <option value="MI">Manajemen Informatika (D3)</option>
                                            <option value="KA">Komputerisasi Akuntansi (D3)</option>
                                        </select>
                                        <div class="invalid-feedback">Program studi harus dipilih</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="entry_year" class="form-label">Tahun Masuk</label>
                                        <select class="form-select" id="entry_year" name="entry_year" required>
                                            <option value="">Pilih Tahun Masuk</option>
                                            <option value="2024">2024</option>
                                        </select>
                                        <div class="invalid-feedback">Tahun masuk harus dipilih</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="high_school" class="form-label">Asal Sekolah</label>
                                        <input type="text" class="form-control" id="high_school" name="high_school" required>
                                        <div class="invalid-feedback">Asal sekolah harus diisi</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="graduation_year" class="form-label">Tahun Lulus</label>
                                        <input type="number" class="form-control" id="graduation_year" name="graduation_year" required>
                                        <div class="invalid-feedback">Tahun lulus harus diisi</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Document Upload -->
                            <div class="mb-4">
                                <h3 class="h5 mb-3">Upload Dokumen</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="ijazah" class="form-label">Ijazah (PDF)</label>
                                        <input type="file" class="form-control" id="ijazah" name="ijazah" accept=".pdf" required>
                                        <div class="invalid-feedback">File ijazah harus diupload</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="skhun" class="form-label">SKHUN (PDF)</label>
                                        <input type="file" class="form-control" id="skhun" name="skhun" accept=".pdf" required>
                                        <div class="invalid-feedback">File SKHUN harus diupload</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="ktp" class="form-label">KTP (PDF)</label>
                                        <input type="file" class="form-control" id="ktp" name="ktp" accept=".pdf" required>
                                        <div class="invalid-feedback">File KTP harus diupload</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="photo" class="form-label">Pas Foto (JPG/PNG)</label>
                                        <input type="file" class="form-control" id="photo" name="photo" accept=".jpg,.jpeg,.png" required>
                                        <div class="invalid-feedback">Pas foto harus diupload</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        Saya menyetujui semua persyaratan dan ketentuan yang berlaku
                                    </label>
                                    <div class="invalid-feedback">
                                        Anda harus menyetujui persyaratan dan ketentuan
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg hover-lift">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Pendaftaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Information -->
            <div class="col-lg-4" data-aos="fade-left">
                <!-- Important Dates -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-4">Jadwal Penting</h3>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <h4 class="h6 mb-1">Gelombang 1</h4>
                                    <p class="text-muted mb-0">1 Januari - 31 Maret 2024</p>
                                </div>
                            </li>
                            <li class="mb-3 d-flex">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <h4 class="h6 mb-1">Gelombang 2</h4>
                                    <p class="text-muted mb-0">1 April - 30 Juni 2024</p>
                                </div>
                            </li>
                            <li class="d-flex">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <h4 class="h6 mb-1">Gelombang 3</h4>
                                    <p class="text-muted mb-0">1 Juli - 31 Agustus 2024</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-4">Butuh Bantuan?</h3>
                        <p class="text-muted mb-4">
                            Jika Anda mengalami kesulitan dalam proses pendaftaran, silakan hubungi kami melalui:
                        </p>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <h4 class="h6 mb-1">Telepon</h4>
                                    <p class="text-muted mb-0">(022) 7207777</p>
                                </div>
                            </li>
                            <li class="mb-3 d-flex">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="h6 mb-1">Email</h4>
                                    <p class="text-muted mb-0">info@stmik-bandung.ac.id</p>
                                </div>
                            </li>
                            <li class="d-flex">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h4 class="h6 mb-1">Alamat</h4>
                                    <p class="text-muted mb-0">
                                        Jl. Cikutra No.113, Cikutra,<br>
                                        Kec. Cibeunying Kidul,<br>
                                        Kota Bandung, Jawa Barat 40124
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.hero {
    position: relative;
    min-height: 50vh;
}

.hero-bg img {
    object-fit: cover;
}

.min-vh-50 {
    min-height: 50vh;
}

.object-fit-cover {
    object-fit: cover;
}

.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.form-label {
    font-weight: 500;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
}

@media (max-width: 768px) {
    .hero {
        min-height: 40vh;
    }
    
    .min-vh-50 {
        min-height: 40vh;
    }
}
</style>

<script>
// Form validation
(function () {
    'use strict'
    
    var forms = document.querySelectorAll('.needs-validation')
    
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            
            form.classList.add('was-validated')
        }, false)
    })
})()

// File size validation
document.querySelectorAll('input[type="file"]').forEach(function(input) {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (file && file.size > maxSize) {
            alert('Ukuran file tidak boleh lebih dari 2MB');
            input.value = '';
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
