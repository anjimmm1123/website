// Contact Form Validation
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Send form data to backend
            fetch('../backend/process_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully!');
                    contactForm.reset();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the message.');
            });
        });
    }
});

// Gallery Image Modal
document.addEventListener('DOMContentLoaded', function() {
    const galleryItems = document.querySelectorAll('.gallery-item img');
    galleryItems.forEach(item => {
        item.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            const modalImg = document.querySelector('#imageModal .modal-body img');
            modalImg.src = this.src;
            modal.show();
        });
    });
});

// Admin Panel Functions
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('show');
        });
    }
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
}); 