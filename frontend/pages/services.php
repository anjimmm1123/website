<?php
require_once __DIR__ . '/../../backend/auth/Auth.php';
?>

<!-- CSS -->
<link rel="stylesheet" href="/frontend/assets/css/common.css">
<link rel="stylesheet" href="/frontend/assets/css/services.css">

<!-- Hero Section -->
<section class="hero position-relative overflow-hidden">
    <div class="hero-bg position-absolute w-100 h-100">
        <div class="overlay position-absolute w-100 h-100 bg-dark opacity-75"></div>
    </div>
    
    <div class="container position-relative py-5">
        <div class="row min-vh-50 align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <h1 class="display-4 text-white mb-4">Program Studi</h1>
                <p class="lead text-white-50">
                    Pilih program studi yang sesuai dengan minat dan bakat Anda
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Programs Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <img src="/frontend/assets/images/si.jpg" class="card-img-top" alt="Sistem Informasi">
                    <div class="card-body p-4">
                        <h2 class="h3 mb-4">Sistem Informasi</h2>
                        <p class="text-muted mb-4">
                            Program studi yang mempelajari pengembangan sistem informasi untuk mendukung proses bisnis.
                            Lulusan akan memiliki kemampuan dalam analisis, desain, dan implementasi sistem informasi.
                        </p>
                        <ul class="list-unstyled text-muted mb-4">
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Analisis Sistem Informasi
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Desain Database
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Pengembangan Aplikasi
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Manajemen Proyek TI
                            </li>
                        </ul>
                        <a href="/?page=register" class="btn btn-primary hover-lift">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <img src="/frontend/assets/images/ti.jpg" class="card-img-top" alt="Teknik Informatika">
                    <div class="card-body p-4">
                        <h2 class="h3 mb-4">Teknik Informatika</h2>
                        <p class="text-muted mb-4">
                            Program studi yang fokus pada pengembangan perangkat lunak dan teknologi informasi.
                            Lulusan akan memiliki kemampuan dalam pemrograman, algoritma, dan pengembangan sistem.
                        </p>
                        <ul class="list-unstyled text-muted mb-4">
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Pemrograman Lanjut
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Kecerdasan Buatan
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Jaringan Komputer
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Keamanan Sistem
                            </li>
                        </ul>
                        <a href="/?page=register" class="btn btn-primary hover-lift">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <img src="/frontend/assets/images/mi.jpg" class="card-img-top" alt="Manajemen Informatika">
                    <div class="card-body p-4">
                        <h2 class="h3 mb-4">Manajemen Informatika</h2>
                        <p class="text-muted mb-4">
                            Program studi yang menggabungkan manajemen dan teknologi informasi.
                            Lulusan akan memiliki kemampuan dalam manajemen proyek TI dan pengembangan sistem.
                        </p>
                        <ul class="list-unstyled text-muted mb-4">
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Manajemen Proyek TI
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Analisis Bisnis
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Pengembangan Sistem
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                Manajemen Data
                            </li>
                        </ul>
                        <a href="/?page=register" class="btn btn-primary hover-lift">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 mb-3">Keunggulan Program Studi</h2>
            <p class="lead text-muted">Mengapa memilih program studi kami?</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                        <h3 class="h5 mb-3">Kurikulum Terkini</h3>
                        <p class="text-muted mb-0">
                            Kurikulum yang dirancang sesuai kebutuhan industri dan teknologi terkini.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-laptop-code fa-3x text-primary mb-3"></i>
                        <h3 class="h5 mb-3">Fasilitas Modern</h3>
                        <p class="text-muted mb-0">
                            Laboratorium komputer canggih dan akses internet berkecepatan tinggi.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h3 class="h5 mb-3">Dosen Berpengalaman</h3>
                        <p class="text-muted mb-0">
                            Dosen profesional dengan pengalaman industri dan akademis yang mumpuni.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-handshake fa-3x text-primary mb-3"></i>
                        <h3 class="h5 mb-3">Kerjasama Industri</h3>
                        <p class="text-muted mb-0">
                            Kerjasama dengan perusahaan teknologi untuk magang dan penempatan kerja.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0" data-aos="fade-right">
                <h2 class="display-5 mb-3">Siap untuk Memulai Perjalanan Anda?</h2>
                <p class="lead mb-0">
                    Bergabunglah dengan kami dan jadilah bagian dari komunitas teknologi yang berkembang.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                <a href="/?page=register" class="btn btn-light btn-lg hover-lift">Daftar Sekarang</a>
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

@media (max-width: 768px) {
    .hero {
        min-height: 40vh;
    }
    
    .min-vh-50 {
        min-height: 40vh;
    }
}
</style> 