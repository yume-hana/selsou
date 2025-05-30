
document.addEventListener('DOMContentLoaded', function () {
    // DOM Elements
    const fileInput = document.getElementById('fileInput');
    const profileAvatar = document.getElementById('profileAvatar') || 'profile_images/user.jpg' 
    const profileImage = document.getElementById('profileImage')||'profile_images/user.jpg' 
    const savePhotoBtn = document.getElementById('savePhotoBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const statusMessage = document.getElementById('statusMessage');
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const fieldValue = document.getElementById('fieldValue');
    const modalTitle = document.getElementById('modalTitle');
    const fieldLabel = document.getElementById('fieldLabel');
    const closeModal = document.querySelector('.close-modal');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const editButtons = document.querySelectorAll('.edit-btn');
   
    // Variables to store data
    let originalImageSrc = profileAvatar.src;
    let selectedFile = null;
    let isPhotoChanged = false;
    let currentField = '';
    let userData = {}; // Store user data from server

    // Load user profile data when page loads
    loadUserProfile();

    // Event Listeners
    fileInput.addEventListener('change', handleFileSelect);
    savePhotoBtn.addEventListener('click', saveProfilePhoto);
    cancelBtn.addEventListener('click', cancelPhotoChange);

    // Edit button event listeners
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const field = button.getAttribute('data-field');
            openEditModal(field);
        });
    });

    // Modal event listeners
    closeModal.addEventListener('click', closeEditModal);
    cancelEditBtn.addEventListener('click', closeEditModal);

    // Close modal if clicked outside
    window.addEventListener('click', (event) => {
        if (event.target === editModal) {
            closeEditModal();
        }
    });

    // Form submission
    editForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const value = fieldValue.value.trim();

        if (value) {
            updateFieldValue(currentField, value);
            closeEditModal();
        }
    });

    /**
     * Load user profile data from server
     */
    function loadUserProfile() {
        // Show loading indicators
        showStatusMessage('Loading profile data...', 'info');

        // Fetch user data from server
        fetch('api/get_profile.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load profile data');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    userData = data.user;
                    updateProfileUI(userData);
                    statusMessage.style.display = 'none';
                } else {
                    showStatusMessage('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error loading profile:', error);
                showStatusMessage('Failed to load profile data. Please refresh the page.', 'error');
            });
    }

    /**
     * Update profile UI with user data
     */
    function updateProfileUI(userData) {
        // Update profile images if available
        if (userData.profile_photo) {
            profileAvatar.src = userData.profile_photo;
            profileImage.src = userData.profile_photo;
            originalImageSrc = userData.profile_photo;
        }

        // Update user information in header
        document.getElementById('userName').textContent = `${userData.first_name} ${userData.last_name}`;
        document.getElementById('userEmail').textContent = userData.email;
        document.getElementById('userID').textContent = 'ID: ' + userData.student_id;
        document.getElementById('displayUsername').textContent = userData.username;

        // Update personal details
        document.getElementById('firstName').textContent = userData.first_name;
        document.getElementById('lastName').textContent = userData.last_name;
        document.getElementById('username').textContent = userData.username;
        document.getElementById('email').textContent = userData.email;
        document.getElementById('address').textContent = userData.address || 'Not specified';

        // Update additional details
        document.getElementById('gender').textContent = userData.gender || 'Not specified';
        document.getElementById('nationality').textContent = userData.nationality || 'Not specified';
        document.getElementById('dob').textContent = userData.date_of_birth || 'Not specified';
    }

    /**
     * Handle file selection for profile photo
     */
    function handleFileSelect(event) {
        const file = event.target.files[0];

        if (!file) return;

        // Check file type
        const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validImageTypes.includes(file.type)) {
            showStatusMessage('Please select a valid image file (JPEG, PNG, GIF, WebP)', 'error');
            return;
        }

        // Check file size (max 5MB)
        const maxSizeInBytes = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSizeInBytes) {
            showStatusMessage('Image file size should be less than 5MB', 'error');
            return;
        }

        // Save selected file for later upload
        selectedFile = file;

        // Preview the selected image
        const reader = new FileReader();
        reader.onload = function (e) {
            profileAvatar.src = e.target.result;
            isPhotoChanged = true;

            // Show save/cancel buttons
            savePhotoBtn.style.display = 'block';
            cancelBtn.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    /**
     * Save profile photo to server
     */
    function saveProfilePhoto() {
        if (!selectedFile || !isPhotoChanged) {
            return;
        }

        // Create form data for file upload
        const formData = new FormData();
        formData.append('profile_photo', selectedFile);
        formData.append('registration_nbr', studentId); // From PHP session

        // Show loading state
        savePhotoBtn.disabled = true;
        savePhotoBtn.textContent = 'Saving...';
        showStatusMessage('Uploading photo...', 'info');

        // Send to server
        fetch('api/update_profile_image.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showStatusMessage('Profile photo updated successfully!', 'success');
                    originalImageSrc = data.photo_url || profileAvatar.src; // Update with server URL
                    profileImage.src = originalImageSrc; // Update navbar profile image too
                    isPhotoChanged = false;

                    // Update user data
                    userData.profile_photo = originalImageSrc;
                } else {
                    showStatusMessage(data.message || 'Failed to update profile photo', 'error');
                    // Revert to original if error
                    profileAvatar.src = originalImageSrc;
                }
            })
            .catch(error => {
                console.error('Error uploading profile photo:', error);
                showStatusMessage('Error uploading profile photo. Please try again.', 'error');
                // Revert to original on error
                profileAvatar.src = originalImageSrc;
            })
            .finally(() => {
                // Reset UI state
                savePhotoBtn.disabled = false;
                savePhotoBtn.textContent = 'Save Photo';
                savePhotoBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
            });
    }

    /**
     * Cancel photo change and revert to original
     */
    function cancelPhotoChange() {
        // Revert to original photo
        profileAvatar.src = originalImageSrc;

        // Reset file input
        fileInput.value = '';
        selectedFile = null;
        isPhotoChanged = false;

        // Hide buttons and status message
        savePhotoBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
        statusMessage.style.display = 'none';
    }

    /**
     * Open the edit modal for a specific field
     */
    function openEditModal(field) {
        currentField = field;

        // Get current value
        const currentValue = document.getElementById(field).textContent;

        // Set field label and modal title
        switch (field) {
            case 'username':
                modalTitle.textContent = 'Edit Username';
                fieldLabel.textContent = 'Username:';
                break;
            case 'email':
                modalTitle.textContent = 'Edit Email Address';
                fieldLabel.textContent = 'Email:';
                // Set input type to email for validation
                fieldValue.type = 'email';
                break;
            case 'address':
                modalTitle.textContent = 'Edit Address';
                fieldLabel.textContent = 'Address:';
                break;
            default:
                modalTitle.textContent = 'Edit Field';
                fieldLabel.textContent = 'Value:';
        }

        // Set current value in input
        fieldValue.value = currentValue === 'Not specified' ? '' : currentValue;

        // Show modal
        editModal.style.display = 'block';

        // Focus on input
        fieldValue.focus();
    }

    /**
     * Close the edit modal
     */
    function closeEditModal() {
        editModal.style.display = 'none';
        fieldValue.value = '';
        currentField = '';

        // Reset input type to text (in case it was changed)
        fieldValue.type = 'text';
    }

    /**
     * Validate email format
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Update field value and save to server
     */
    function updateFieldValue(field, value) {
        // Check for email format if updating email
        if (field === 'email' && !isValidEmail(value)) {
            showStatusMessage('Please enter a valid email address', 'error');
            return;
        }

        // Show loading message
        showStatusMessage('Updating ' + field + '...', 'info');

        // Prepare data to send
        const data = {
            field: field,
            value: value,
            student_id: studentId // From PHP session
        };

        fetch('api/update_profile_field.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update UI
                    document.getElementById(field).textContent = value;

                    // Update display username if username is changed
                    if (field === 'username') {
                        document.getElementById('displayUsername').textContent = value;
                        userData.username = value;
                    } else if (field === 'email') {
                        document.getElementById('userEmail').textContent = value;
                        userData.email = value;
                    }

                    showStatusMessage(field + ' updated successfully!', 'success');

                    // Store in user data
                    userData[field] = value;
                } else {
                    showStatusMessage('Error: ' + (data.message || 'Failed to update ' + field), 'error');
                }
            })
            .catch(error => {
                console.error('Error updating field:', error);
                showStatusMessage('Error updating ' + field + '. Please try again.', 'error');
            });
    }

    /**
     * Show status message with type (success, error, info)
     */
    function showStatusMessage(message, type) {
        statusMessage.textContent = message;
        statusMessage.className = 'status-message';

        if (type) {
            statusMessage.classList.add(type);
        }

        statusMessage.style.display = 'block';

        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                statusMessage.style.display = 'none';
            }, 5000);
        }
    }
});
