<!-- Hero Section -->
<section class="hero-section">
    <div class="container-custom">
        <div class="hero-content">
            <div class="row">
                <div class="hero-text fade-in-left">
                    <h1 class="hero-title">Wujudkan Karir Teknologi Masa Depan</h1>
                    <p class="hero-description">STMIK Enterprise menyediakan pendidikan teknologi berkualitas dengan kurikulum yang dirancang bersama industri untuk menghasilkan lulusan yang siap menghadapi tantangan digital.</p>
                    <div class="hero-buttons">
                        <a href="<?php echo BASE_URL; ?>?page=programs" class="btn-primary-custom">Jelajahi Program</a>
                        <a href="<?php echo BASE_URL; ?>?page=application" class="btn-outline-custom">Daftar Sekarang</a>
                    </div>
                </div>
                <div class="hero-image fade-in-right">
                    <img src="<?php echo BASE_URL; ?>frontend/assets/images/hero-image.svg" alt="STMIK Enterprise" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container-custom">
        <div class="stats-container">
            <div class="stat-item zoom-in">
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="counter" data-count="1500">0</h3>
                <p>Mahasiswa Aktif</p>
            </div>
            <div class="stat-item zoom-in delay-1">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3 class="counter" data-count="75">0</h3>
                <p>Dosen Berkualitas</p>
            </div>
            <div class="stat-item zoom-in delay-2">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3 class="counter" data-count="120">0</h3>
                <p>Penghargaan</p>
            </div>
            <div class="stat-item zoom-in delay-3">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="counter" data-count="250">0</h3>
                <p>Perusahaan Mitra</p>
            </div>
        </div>
    </div>
</section>

<!-- Programs Section -->
<section class="programs-section">
    <div class="container-custom">
        <div class="section-header text-center fade-in">
            <h2 class="section-title">Program Studi Unggulan</h2>
            <p class="section-subtitle">Pilih program studi yang sesuai dengan minat dan bakat Anda</p>
        </div>
        
        <div class="programs-grid">
            <?php
            try {
                $stmt = $db->query("SELECT id, name, description, image_url FROM programs WHERE is_active = 1 LIMIT 3");
                $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($programs as $index => $program) {
                    $delayClass = $index > 0 ? "delay-" . $index : "";
                    ?>
                    <div class="program-card fade-in <?php echo $delayClass; ?>">
                        <div class="program-image">
                            <img src="<?php echo BASE_URL . $program['image_url']; ?>" alt="<?php echo $program['name']; ?>">
                        </div>
                        <div class="program-content">
                            <h3 class="program-title"><?php echo $program['name']; ?></h3>
                            <p class="program-description"><?php echo truncateText($program['description'], 120); ?></p>
                            <a href="<?php echo BASE_URL . '?page=programs&id=' . $program['id']; ?>" class="btn-outline-custom">Pelajari Lebih Lanjut</a>
                        </div>
                    </div>
                    <?php
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Tidak dapat memuat program studi.</div>';
                error_log($e->getMessage());
            }
            ?>
        </div>
        
        <div class="text-center mt-5 fade-in">
            <a href="<?php echo BASE_URL; ?>?page=programs" class="btn-primary-custom">Lihat Semua Program</a>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="why-section">
    <div class="container-custom">
        <div class="row">
            <div class="why-image fade-in-left">
                <img src="<?php echo BASE_URL; ?>frontend/assets/images/why-us.svg" alt="Mengapa memilih STMIK Enterprise" class="img-fluid">
            </div>
            <div class="why-content fade-in-right">
                <div class="section-header">
                    <h2 class="section-title">Mengapa Memilih Kami</h2>
                    <p class="section-subtitle">Keunggulan STMIK Enterprise yang membedakan kami dari yang lain</p>
                </div>
                
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="feature-content">
                            <h3 class="feature-title">Dosen Praktisi Industri</h3>
                            <p class="feature-description">Dosen kami adalah praktisi aktif di industri teknologi dengan pengalaman nyata.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <div class="feature-content">
                            <h3 class="feature-title">Kurikulum Berbasis Industri</h3>
                            <p class="feature-description">Kurikulum kami dikembangkan bersama perusahaan teknologi terkemuka.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="feature-content">
                            <h3 class="feature-title">Kerjasama Perusahaan</h3>
                            <p class="feature-description">Lebih dari 250 perusahaan mitra untuk magang dan rekrutmen lulusan.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="feature-content">
                            <h3 class="feature-title">Sertifikasi Industri</h3>
                            <p class="feature-description">Program sertifikasi industri terintegrasi dalam kurikulum.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="container-custom">
        <div class="section-header text-center fade-in">
            <h2 class="section-title">Apa Kata Mereka</h2>
            <p class="section-subtitle">Pendapat alumni dan mahasiswa tentang STMIK Enterprise</p>
        </div>
        
        <div class="testimonials-carousel swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-image">
                            <img src="<?php echo BASE_URL; ?>frontend/assets/images/testimonial-1.svg" alt="Testimonial">
                        </div>
                        <div class="testimonial-content">
                            <p class="testimonial-text">"STMIK Enterprise memberikan fondasi yang kuat untuk karir saya di teknologi. Kurikulum yang relevan dengan industri dan dosen yang berpengalaman sangat membantu saya."</p>
                            <div class="testimonial-author">
                                <h4>Budi Santoso</h4>
                                <p>Software Engineer, Tech Company</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-image">
                            <img src="<?php echo BASE_URL; ?>frontend/assets/images/testimonial-2.svg" alt="Testimonial">
                        </div>
                        <div class="testimonial-content">
                            <p class="testimonial-text">"Program sertifikasi industri dan magang yang disediakan STMIK Enterprise membuat saya memiliki keunggulan saat melamar pekerjaan. Sangat direkomendasikan!"</p>
                            <div class="testimonial-author">
                                <h4>Diana Putri</h4>
                                <p>Data Scientist, E-commerce</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-image">
                            <img src="<?php echo BASE_URL; ?>frontend/assets/images/testimonial-3.svg" alt="Testimonial">
                        </div>
                        <div class="testimonial-content">
                            <p class="testimonial-text">"Sebagai mahasiswa aktif, saya sangat terkesan dengan fasilitas modern dan lingkungan belajar yang mendukung di STMIK Enterprise. Dosen-dosennya juga sangat membantu."</p>
                            <div class="testimonial-author">
                                <h4>Ahmad Rizki</h4>
                                <p>Mahasiswa Teknik Informatika</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- Partners -->
<section class="partners-section">
    <div class="container-custom">
        <div class="section-header text-center fade-in">
            <h2 class="section-title">Mitra Industri Kami</h2>
            <p class="section-subtitle">Bekerja sama dengan perusahaan teknologi terkemuka</p>
        </div>
        
        <div class="partners-grid fade-in">
            <div class="partner-item">
                <img src="<?php echo BASE_URL; ?>frontend/assets/images/partner-1.svg" alt="Partner 1">
            </div>
            <div class="partner-item">
                <img src="<?php echo BASE_URL; ?>frontend/assets/images/partner-2.svg" alt="Partner 2">
            </div>
            <div class="partner-item">
                <img src="<?php echo BASE_URL; ?>frontend/assets/images/partner-3.svg" alt="Partner 3">
            </div>
            <div class="partner-item">
                <img src="<?php echo BASE_URL; ?>frontend/assets/images/partner-4.svg" alt="Partner 4">
            </div>
            <div class="partner-item">
                <img src="<?php echo BASE_URL; ?>frontend/assets/images/partner-5.svg" alt="Partner 5">
            </div>
            <div class="partner-item">
                <img src="<?php echo BASE_URL; ?>frontend/assets/images/partner-6.svg" alt="Partner 6">
            </div>
        </div>
    </div>
</section>

<!-- CTA Banner -->
<section class="cta-section">
    <div class="container-custom">
        <div class="cta-container">
            <div class="cta-content fade-in">
                <h2 class="cta-title">Siap Memulai Perjalanan Pendidikan Anda?</h2>
                <p class="cta-description">Daftar sekarang dan jadilah bagian dari komunitas teknologi kami. Masa depan karir teknologi Anda dimulai di sini.</p>
                <div class="cta-buttons">
                    <a href="<?php echo BASE_URL; ?>?page=application" class="btn-primary-custom btn-cta">Daftar Sekarang</a>
                    <a href="<?php echo BASE_URL; ?>?page=contact" class="btn-outline-custom">Hubungi Kami</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Initialize Swiper -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper carousel for testimonials
    const swiper = new Swiper('.testimonials-carousel', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
});
</script>

<style>
/* Hero Section */
.hero-section {
    padding: 5rem 0;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    position: relative;
    overflow: hidden;
    min-height: 80vh;
    display: flex;
    align-items: center;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: rotateGradient 20s linear infinite;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.hero-text, .hero-image {
    flex: 1;
    padding: 0 15px;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.hero-description {
    font-size: 1.25rem;
    margin-bottom: 2.5rem;
    opacity: 0.9;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
}

.hero-image img {
    max-width: 100%;
    height: auto;
    animation: float 4s ease-in-out infinite;
}

@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-15px);
    }
    100% {
        transform: translateY(0px);
    }
}

/* Stats Section */
.stats-section {
    padding: 4rem 0;
    background-color: white;
    margin-top: -3rem;
    position: relative;
    z-index: 10;
}

.stats-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    background-color: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    padding: 2rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    flex: 1;
    min-width: 200px;
}

.stat-icon {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.stat-item h3 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.stat-item p {
    color: var(--secondary-color);
    font-size: 1.1rem;
}

/* Programs Section */
.programs-section {
    padding: 5rem 0;
    background-color: var(--light-color);
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1.2rem;
    color: var(--secondary-color);
    max-width: 800px;
    margin: 0 auto;
}

.programs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.program-card {
    background-color: white;
    border-radius: var(--border-radius-md);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.program-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-md);
}

.program-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.program-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.program-card:hover .program-image img {
    transform: scale(1.1);
}

.program-content {
    padding: 1.5rem;
}

.program-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--dark-color);
}

.program-description {
    color: var(--secondary-color);
    margin-bottom: 1.5rem;
}

/* Why Choose Us Section */
.why-section {
    padding: 5rem 0;
    background-color: white;
}

.why-image, .why-content {
    flex: 1;
    padding: 0 15px;
}

.why-content .section-header {
    text-align: left;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.feature-icon {
    width: 50px;
    height: 50px;
    background-color: rgba(var(--primary-rgb), 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary-color);
    flex-shrink: 0;
}

.feature-title {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.feature-description {
    color: var(--secondary-color);
}

/* Testimonials Section */
.testimonials-section {
    padding: 5rem 0;
    background-color: var(--light-color);
}

.testimonials-carousel {
    padding-bottom: 3rem;
}

.testimonial-card {
    background-color: white;
    border-radius: var(--border-radius-md);
    box-shadow: var(--shadow-sm);
    padding: 1.5rem;
    margin: 1rem;
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.testimonial-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-md);
}

.testimonial-image {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto 1.5rem;
    border: 3px solid var(--primary-color);
}

.testimonial-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.testimonial-text {
    font-style: italic;
    margin-bottom: 1.5rem;
    color: var(--secondary-color);
    position: relative;
}

.testimonial-text::before,
.testimonial-text::after {
    content: '"';
    font-size: 3rem;
    color: rgba(var(--primary-rgb), 0.1);
    position: absolute;
}

.testimonial-text::before {
    top: -20px;
    left: -10px;
}

.testimonial-text::after {
    bottom: -40px;
    right: -10px;
}

.testimonial-author h4 {
    font-size: 1.2rem;
    margin-bottom: 0.25rem;
    color: var(--dark-color);
}

.testimonial-author p {
    color: var(--primary-color);
    font-size: 0.9rem;
}

/* Partners Section */
.partners-section {
    padding: 5rem 0;
    background-color: white;
}

.partners-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 2rem;
    align-items: center;
    margin-top: 2rem;
}

.partner-item {
    display: flex;
    justify-content: center;
    align-items: center;
    filter: grayscale(100%);
    opacity: 0.6;
    transition: all 0.3s ease;
}

.partner-item:hover {
    filter: grayscale(0%);
    opacity: 1;
}

.partner-item img {
    max-width: 100%;
    max-height: 80px;
}

/* CTA Section */
.cta-section {
    padding: 5rem 0;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="rgba(255, 255, 255, 0.1)"/></svg>');
    background-size: cover;
    background-position: center;
    opacity: 0.1;
}

.cta-container {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

.cta-description {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.mt-5 {
    margin-top: 3rem;
}

.alert {
    padding: 1rem;
    border-radius: var(--border-radius-md);
    margin-bottom: 1rem;
}

.alert-danger {
    background-color: rgba(var(--danger-rgb), 0.1);
    color: var(--danger-color);
    border: 1px solid rgba(var(--danger-rgb), 0.2);
}

.img-fluid {
    max-width: 100%;
    height: auto;
}

/* Media Queries */
@media (max-width: 992px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .row {
        flex-direction: column;
    }
    
    .hero-text, .hero-image,
    .why-image, .why-content {
        width: 100%;
        margin-bottom: 2rem;
    }
    
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 4rem 0;
        min-height: auto;
    }
    
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-description {
        font-size: 1rem;
    }
    
    .stats-container {
        flex-direction: column;
    }
    
    .stat-item {
        margin-bottom: 2rem;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-title {
        font-size: 2rem;
    }
    
    .hero-buttons, .cta-buttons {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .partners-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .programs-grid {
        grid-template-columns: 1fr;
    }
    
    .feature-item {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .feature-icon {
        margin-bottom: 1rem;
    }
}
</style>