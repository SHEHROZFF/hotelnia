$(document).ready(function() {
    // Load all settings when page loads
    loadSettings();

    // Handle general settings form submission
    $('#generalSettingsForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('general', $(this).serialize());
    });

    // Handle email settings form submission
    $('#emailSettingsForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('email', $(this).serialize());
    });

    // Handle payment settings form submission
    $('#paymentSettingsForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('payment', $(this).serialize());
    });
});

function loadSettings() {
    // Load general settings
    $.ajax({
        url: '../ajax/settings/get_settings.php',
        type: 'GET',
        data: { group: 'general' },
        success: function(response) {
            if (response.success) {
                $('#siteName').val(response.data.site_name);
                $('#siteEmail').val(response.data.site_email);
                $('#currency').val(response.data.currency);
                $('#timezone').val(response.data.timezone);
            }
        }
    });

    // Load email settings
    $.ajax({
        url: '../ajax/settings/get_settings.php',
        type: 'GET',
        data: { group: 'email' },
        success: function(response) {
            if (response.success) {
                $('#smtpHost').val(response.data.smtp_host);
                $('#smtpPort').val(response.data.smtp_port);
                $('#smtpUsername').val(response.data.smtp_username);
                $('#smtpEncryption').val(response.data.smtp_encryption);
            }
        }
    });

    // Load payment settings
    $.ajax({
        url: '../ajax/settings/get_settings.php',
        type: 'GET',
        data: { group: 'payment' },
        success: function(response) {
            if (response.success) {
                $('#paypalEnabled').prop('checked', response.data.paypal_enabled === '1');
                $('#paypalClientId').val(response.data.paypal_client_id);
                $('#stripeEnabled').prop('checked', response.data.stripe_enabled === '1');
                $('#stripeKey').val(response.data.stripe_key);
            }
        }
    });
}

function saveSettings(group, formData) {
    $.ajax({
        url: '../ajax/settings/save_settings.php',
        type: 'POST',
        data: formData + '&group=' + group,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Settings saved successfully!'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to save settings'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while saving settings'
            });
        }
    });
} 
 