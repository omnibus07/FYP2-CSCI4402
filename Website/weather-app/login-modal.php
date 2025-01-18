<!-- Login/Register Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="loginModalLabel">Welcome Back!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <!-- Login Form -->
                <form id="loginForm" action="process_login.php" method="POST" class="auth-form">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="password" name="password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none small" id="showRegisterForm">Create Account</a>
                    </div>
                </form>

                <!-- Register Form (Initially Hidden) -->
                <form id="registerForm" action="process_register.php" method="POST" class="auth-form d-none">
                    <div class="mb-3">
                        <label for="reg_username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="reg_username" name="username" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reg_email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="reg_email" name="email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reg_password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="reg_password" name="password" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reg_confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="reg_confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none small" id="showLoginForm">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 1.5rem 1.5rem 1rem;
}

.modal-body {
    padding: 1rem 1.5rem 2rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.form-control {
    border-left: none;
}

.form-control:focus {
    box-shadow: none;
    border-color: #ced4da;
}

.input-group-text i {
    width: 1rem;
    text-align: center;
}

.btn-primary {
    padding: 0.6rem;
    font-weight: 500;
    border-radius: 8px;
    background-color: #0f0558;
}

.btn-primary:hover {
    background-color: #0a043d !important;
}

.alert {
    margin-bottom: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 8px;
}

.auth-form {
    transition: all 0.3s ease;
}

.auth-form.slide-out {
    transform: translateX(-100%);
    opacity: 0;
}

.auth-form.slide-in {
    transform: translateX(0);
    opacity: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const showRegisterLink = document.getElementById('showRegisterForm');
    const showLoginLink = document.getElementById('showLoginForm');
    const modalTitle = document.getElementById('loginModalLabel');

    // Toggle between login and register forms
    function toggleForms(showRegister) {
        if (showRegister) {
            loginForm.classList.add('d-none');
            registerForm.classList.remove('d-none');
            modalTitle.textContent = 'Create Account';
        } else {
            registerForm.classList.add('d-none');
            loginForm.classList.remove('d-none');
            modalTitle.textContent = 'Welcome Back!';
        }
    }

    showRegisterLink.addEventListener('click', function(e) {
        e.preventDefault();
        toggleForms(true);
    });

    showLoginLink.addEventListener('click', function(e) {
        e.preventDefault();
        toggleForms(false);
    });

    // Login form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('process_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                showError(loginForm, data.message || 'Login failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(loginForm, 'An error occurred. Please try again.');
        });
    });

    // Register form submission
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Password validation
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm_password');
        
        if (password !== confirmPassword) {
            showError(registerForm, 'Passwords do not match.');
            return;
        }

        fetch('process_register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(registerForm, 'Registration successful! Please login.');
                setTimeout(() => toggleForms(false), 2000);
            } else {
                showError(registerForm, data.message || 'Registration failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(registerForm, 'An error occurred. Please try again.');
        });
    });

    // Helper function to show error messages
    function showError(form, message) {
        clearMessages(form);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger';
        errorDiv.textContent = message;
        form.insertBefore(errorDiv, form.firstChild);
    }

    // Helper function to show success messages
    function showSuccess(form, message) {
        clearMessages(form);
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success';
        successDiv.textContent = message;
        form.insertBefore(successDiv, form.firstChild);
    }

    // Helper function to clear messages
    function clearMessages(form) {
        const alerts = form.getElementsByClassName('alert');
        Array.from(alerts).forEach(alert => alert.remove());
    }

    // Clear messages when modal is hidden
    const loginModal = document.getElementById('loginModal');
    loginModal.addEventListener('hidden.bs.modal', function() {
        clearMessages(loginForm);
        clearMessages(registerForm);
        loginForm.reset();
        registerForm.reset();
        toggleForms(false);
    });
});
</script>