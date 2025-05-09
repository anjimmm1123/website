// API base URL
const API_BASE_URL = 'index.php?page=api';

// API request helper function
async function apiRequest(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin' // Include cookies in the request
    };

    if (data) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(API_BASE_URL + endpoint, options);
        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'API request failed');
        }

        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Authentication API functions
const auth = {
    async login(email, password) {
        return apiRequest('/auth/login', 'POST', { email, password });
    },

    async register(data) {
        return apiRequest('/auth/register', 'POST', data);
    },

    async logout() {
        return apiRequest('/auth/logout', 'POST');
    },

    async requestPasswordReset(email) {
        return apiRequest('/auth/password/reset', 'POST', { email });
    },

    async changePassword(currentPassword, newPassword) {
        return apiRequest('/auth/password/change', 'POST', {
            current_password: currentPassword,
            new_password: newPassword
        });
    }
};

// User API functions
const user = {
    async getProfile() {
        return apiRequest('/user/profile', 'GET');
    },

    async updateProfile(data) {
        return apiRequest('/user/profile', 'PUT', data);
    },

    async updatePassword(data) {
        return apiRequest('/user/password', 'PUT', data);
    }
};

// Services API functions
const services = {
    async getAll() {
        return apiRequest('/services', 'GET');
    },

    async getById(id) {
        return apiRequest(`/services/${id}`, 'GET');
    },

    async getQuotes(data) {
        return apiRequest('/services/quotes', 'POST', data);
    }
};

// Gallery API functions
const gallery = {
    async getAll() {
        return apiRequest('/gallery', 'GET');
    },

    async getById(id) {
        return apiRequest(`/gallery/${id}`, 'GET');
    }
};

// Contact API functions
const contact = {
    async sendMessage(data) {
        return apiRequest('/contact', 'POST', data);
    }
};

// File upload helper
async function uploadFile(file, endpoint) {
    const formData = new FormData();
    formData.append('file', file);

    try {
        const response = await fetch(API_BASE_URL + endpoint, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'File upload failed');
        }

        return result;
    } catch (error) {
        console.error('Upload Error:', error);
        throw error;
    }
}

// Export API functions
window.api = {
    auth,
    user,
    services,
    gallery,
    contact,
    uploadFile
}; 