document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const messageContainer = document.getElementById('message');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous messages
            messageContainer.innerHTML = '';
            messageContainer.className = '';
            
            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            
            // Show loading message
            messageContainer.className = 'alert alert-info';
            messageContainer.textContent = 'Processing...';
            messageContainer.style.display = 'block'; // Ensure message is visible
            
            // Collect form data
            const formData = new FormData(loginForm);
            
            // Send AJAX request
            fetch('ajax/process_login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // First check if the response is ok (status in the range 200-299)
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status} ${response.statusText}`);
                }
                
                // Check if the response is empty
                if (!response.headers.get('content-length') || response.headers.get('content-length') === '0') {
                    throw new Error('Server returned an empty response');
                }
                
                // Get the text response first
                return response.text().then(text => {
                    try {
                        // Try to parse the text as JSON
                        const data = JSON.parse(text);
                        return data;
                    } catch (e) {
                        // If parsing fails, log the error and the actual response
                        console.error('JSON parse error:', e);
                        console.log('Actual response text:', text);
                        throw new Error('Invalid server response: ' + e.message);
                    }
                });
            })
            .then(data => {
                // Process the response data
                if (data.success) {
                    // Login successful
                    messageContainer.className = 'alert alert-success';
                    messageContainer.textContent = data.message || 'Login successful!';
                    messageContainer.style.display = 'block'; // Ensure message is visible
                    
                    // Redirect if specified
                    if (data.redirect) {
                        setTimeout(function() {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } else {
                    // Login failed
                    messageContainer.className = 'alert alert-danger';
                    messageContainer.style.display = 'block'; // Ensure message is visible
                    
                    // Check for verification required
                    if (data.verification_required) {
                        // Show verification message with more emphasis
                        messageContainer.innerHTML = '<strong>' + (data.message || 'Please verify your email before logging in.') + '</strong><br>' +
                            'Check your inbox for the verification email or use the form below to request a new one.';
                        
                        // Scroll to the resend verification form
                        const resendForm = document.querySelector('.mt-4.pt-3.border-top');
                        if (resendForm) {
                            resendForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    } else if (data.errors) {
                        // Display field-specific errors
                        Object.keys(data.errors).forEach(field => {
                            const inputField = document.querySelector(`[name="${field}"]`);
                            if (inputField) {
                                // Mark field as invalid
                                inputField.classList.add('is-invalid');
                                
                                // Add error message
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback error-message';
                                errorDiv.textContent = data.errors[field];
                                inputField.parentNode.appendChild(errorDiv);
                            }
                        });
                        
                        messageContainer.textContent = 'Please correct the errors in the form.';
                    } else {
                        // Display general error message
                        messageContainer.textContent = data.message || 'Login failed. Please try again.';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageContainer.className = 'alert alert-danger';
                messageContainer.textContent = 'An error occurred: ' + error.message;
                messageContainer.style.display = 'block'; // Ensure message is visible
            });
        });
    }
}); 