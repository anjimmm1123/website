</main>

<!-- Footer -->
<footer class="main-footer">
    <div class="waves">
        <div class="wave wave-1"></div>
        <div class="wave wave-2"></div>
        <div class="wave wave-3"></div>
    </div>
    
    <div class="container-custom">
        <div class="footer-content">
            <div class="footer-column">
                <h3 class="footer-heading">STMIK Enterprise</h3>
                <p>Kampus terdepan dalam pendidikan teknologi informasi dan komunikasi di Indonesia. Mencetak lulusan berkualitas dengan keahlian teknologi terkini.</p>
                
                <div class="social-links">
                    <a href="#" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-column">
                <h3 class="footer-heading">Program Studi</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>?page=programs&id=1" class="footer-link">Sistem Informasi</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=programs&id=2" class="footer-link">Teknik Informatika</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=programs&id=3" class="footer-link">Manajemen Informatika</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=programs" class="footer-link">Semua Program</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3 class="footer-heading">Informasi</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>?page=about" class="footer-link">Tentang Kami</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=gallery" class="footer-link">Galeri</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=news" class="footer-link">Berita & Acara</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=facilities" class="footer-link">Fasilitas</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=careers" class="footer-link">Karir</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3 class="footer-heading">Kontak</h3>
                <ul class="footer-contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Jl. Pendidikan No. 123, Jakarta Selatan</span>
                    </li>
                    <li>
                        <i class="fas fa-phone-alt"></i>
                        <span>(021) 123-456</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>info@stmik-enterprise.ac.id</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Senin - Jumat: 08:00 - 16:00</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All Rights Reserved.</p>
            </div>
            <div class="footer-bottom-links">
                <a href="<?php echo BASE_URL; ?>?page=privacy-policy">Kebijakan Privasi</a>
                <a href="<?php echo BASE_URL; ?>?page=terms">Syarat & Ketentuan</a>
                <a href="<?php echo BASE_URL; ?>?page=sitemap">Peta Situs</a>
            </div>
        </div>
    </div>
</footer>

<!-- AOS - Animate On Scroll Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<!-- Base Scripts -->
<script src="<?php echo BASE_URL; ?>frontend/assets/js/animations.js"></script>
<script src="<?php echo BASE_URL; ?>frontend/assets/js/main.js"></script>

<!-- Page Specific JS -->
<?php
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'home';
if (file_exists(ROOT_PATH . '/frontend/assets/js/' . $currentPage . '.js')) {
    echo '<script src="' . BASE_URL . 'frontend/assets/js/' . $currentPage . '.js"></script>';
}
?>

<!-- Initialize AOS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    });
</script>

</body>
</html>