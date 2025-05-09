<div class="error-container">
    <div class="container-custom">
        <div class="error-content">
            <h1 class="error-title">404</h1>
            <h2 class="error-subtitle">Halaman Tidak Ditemukan</h2>
            <p class="error-description">Maaf, halaman yang Anda cari tidak dapat ditemukan. Halaman mungkin telah dipindahkan, dihapus, atau URL yang Anda masukkan salah.</p>
            <a href="<?php echo BASE_URL; ?>" class="btn-primary-custom">Kembali ke Beranda</a>
        </div>
    </div>
</div>

<style>
    .error-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
        padding: 3rem 0;
        text-align: center;
    }
    
    .error-content {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .error-title {
        font-size: 10rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0;
        line-height: 1;
        opacity: 0.8;
    }
    
    .error-subtitle {
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        color: var(--dark-color);
    }
    
    .error-description {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        color: var(--secondary-color);
    }
    
    @media (max-width: 768px) {
        .error-title {
            font-size: 7rem;
        }
        
        .error-subtitle {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 576px) {
        .error-title {
            font-size: 5rem;
        }
        
        .error-subtitle {
            font-size: 1.5rem;
        }
        
        .error-description {
            font-size: 1rem;
        }
    }
</style>