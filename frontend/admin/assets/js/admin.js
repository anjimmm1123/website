/**
 * Admin Panel JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initSidebar();
    initFormValidation();
    initImagePreview();
    initDeleteConfirmation();
    initTooltips();
    initDropdownMenus();
    initSummernote();
    initDataTables();
    
    /**
     * Initialize sidebar functionality
     */
    function initSidebar() {
        const sidebarToggler = document.querySelector('.sidebar-toggle');
        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function(e) {
                e.preventDefault();
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
    }
    
    /**
     * Initialize form validation
     */
    function initFormValidation() {
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
    }
    
    /**
     * Initialize image preview for file inputs
     */
    function initImagePreview() {
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
    }
    
    /**
     * Initialize delete confirmation
     */
    function initDeleteConfirmation() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
        const deleteConfirmButton = document.getElementById('deleteConfirmButton');
        
        if (deleteButtons.length > 0 && deleteConfirmationModal && deleteConfirmButton) {
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    const message = this.dataset.message || 'Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.';
                    
                    // Set custom message if available
                    const modalBody = deleteConfirmationModal.querySelector('.modal-body p');
                    if (modalBody) {
                        modalBody.textContent = message;
                    }
                    
                    deleteConfirmButton.setAttribute('href', href);
                    
                    const modal = new bootstrap.Modal(deleteConfirmationModal);
                    modal.show();
                });
            });
        }
    }
    
    /**
     * Initialize tooltips
     */
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    /**
     * Initialize dropdown menus
     */
    function initDropdownMenus() {
        const dropdownToggleList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        dropdownToggleList.map(function(dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
    }
    
    /**
     * Initialize Summernote editor
     */
    function initSummernote() {
        const summernoteElements = document.querySelectorAll('.summernote');
        
        if (summernoteElements.length > 0 && typeof $.fn.summernote !== 'undefined') {
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
                        for (let i = 0; i < files.length; i++) {
                            uploadSummernoteImage(files[i], this);
                        }
                    }
                }
            });
        }
        
        /**
         * Upload image for Summernote
         * @param {File} file - Image file to upload
         * @param {Object} editor - Summernote editor instance
         */
        function uploadSummernoteImage(file, editor) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]')?.value);
            
            showLoading();
            
            fetch('upload-image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $(editor).summernote('insertImage', data.url);
                } else {
                    showToast(data.message || 'Gagal mengunggah gambar.', 'danger');
                }
                hideLoading();
            })
            .catch(error => {
                console.error('Error uploading image:', error);
                showToast('Terjadi kesalahan saat mengunggah gambar.', 'danger');
                hideLoading();
            });
        }
    }
    
    /**
     * Initialize DataTables
     */
    function initDataTables() {
        const dataTables = document.querySelectorAll('.datatable');
        
        if (dataTables.length > 0 && typeof $.fn.DataTable !== 'undefined') {
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
                },
                responsive: true
            });
        }
    }
});

/**
 * Show loading overlay
 */
function showLoading() {
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'flex';
    }
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
}

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Bootstrap color variant (success, danger, warning, etc.)
 */
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

/**
 * Format date string
 * @param {string} dateString - Date string to format
 * @param {boolean} includeTime - Whether to include time
 * @return {string} Formatted date string
 */
function formatDate(dateString, includeTime = true) {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    
    if (isNaN(date.getTime())) {
        return dateString;
    }
    
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    
    if (includeTime) {
        options.hour = '2-digit';
        options.minute = '2-digit';
    }
    
    return date.toLocaleDateString('id-ID', options);
}

/**
 * Submit form with AJAX
 * @param {HTMLFormElement} form - Form to submit
 * @param {Function} successCallback - Callback function on success
 * @param {Function} errorCallback - Callback function on error
 */
function submitFormAjax(form, successCallback, errorCallback) {
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const formData = new FormData(form);
        const url = form.action;
        const method = form.method || 'POST';
        
        showLoading();
        
        fetch(url, {
            method: method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                if (typeof successCallback === 'function') {
                    successCallback(data);
                } else {
                    showToast(data.message || 'Data berhasil disimpan.', 'success');
                }
            } else {
                if (typeof errorCallback === 'function') {
                    errorCallback(data);
                } else {
                    showToast(data.message || 'Terjadi kesalahan saat menyimpan data.', 'danger');
                }
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error submitting form:', error);
            
            if (typeof errorCallback === 'function') {
                errorCallback({ success: false, message: 'Terjadi kesalahan saat mengirim data.' });
            } else {
                showToast('Terjadi kesalahan saat mengirim data.', 'danger');
            }
        });
    });
}