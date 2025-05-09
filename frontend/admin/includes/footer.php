                <!-- Content ends here -->
            </div>
        </div>
        
        <footer class="footer">
            <div class="container-fluid">
                <div class="row text-muted">
                    <div class="col-8 text-start">
                        <p class="mb-0">
                            &copy; <?php echo date('Y'); ?> <a href="../index.php" class="text-muted" target="_blank">STMIK Enterprise</a>
                        </p>
                    </div>
                    <div class="col-4 text-end">
                        <p class="mb-0">
                            Admin Panel v1.0
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="spinner"></div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" class="btn btn-danger" id="deleteConfirmButton">Ya, Hapus</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    
    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize datatable
        const dataTableElements = document.querySelectorAll('.datatable');
        if (dataTableElements.length > 0) {
            $('.datatable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang cocok",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }
        
        // Initialize Summernote
        const summernoteElements = document.querySelectorAll('.summernote');
        if (summernoteElements.length > 0) {
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
                lang: 'id-ID'
            });
        }
        
        // Sidebar toggle
        const sidebarToggler = document.getElementById('sidebarToggler');
        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-collapsed');
                
                // Store state in localStorage
                if (document.body.classList.contains('sidebar-collapsed')) {
                    localStorage.setItem('sidebar-collapsed', 'true');
                } else {
                    localStorage.setItem('sidebar-collapsed', 'false');
                }
            });
        }
        
        // Check sidebar state from localStorage
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
        
        // Image preview on file input
        const imageUploads = document.querySelectorAll('.image-upload');
        if (imageUploads.length > 0) {
            imageUploads.forEach(input => {
                input.addEventListener('change', function() {
                    const previewId = this.dataset.preview;
                    const preview = document.getElementById(previewId);
                    
                    if (preview) {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                            }
                            
                            reader.readAsDataURL(this.files[0]);
                        } else {
                            preview.src = '';
                            preview.style.display = 'none';
                        }
                    }
                });
            });
        }
        
        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        if (forms.length > 0) {
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        }
        
        // Delete confirmation
        const deleteButtons = document.querySelectorAll('.btn-delete');
        if (deleteButtons.length > 0) {
            const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
            const deleteConfirmButton = document.getElementById('deleteConfirmButton');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    
                    if (deleteConfirmButton) {
                        deleteConfirmButton.setAttribute('href', href);
                    }
                    
                    const modal = new bootstrap.Modal(deleteConfirmationModal);
                    modal.show();
                });
            });
        }
    });
    
    // Show loading overlay
    function showLoading() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }
    
    // Hide loading overlay
    function hideLoading() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }
    
    // Show toast notification
    function showToast(message, type = 'info') {
        // Create toast container if not exists
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">Notifikasi</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        // Show toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        
        toast.show();
        
        // Remove toast after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }
    </script>
    
    <!-- Page specific JS -->
    <?php if (isset($pageScripts) && is_array($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>