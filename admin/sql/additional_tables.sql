-- Settings table
CREATE TABLE settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_group VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin profiles table
CREATE TABLE admin_profiles (
    profile_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    profile_image VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Email templates table
CREATE TABLE email_templates (
    template_id INT PRIMARY KEY AUTO_INCREMENT,
    template_name VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Payment settings table
CREATE TABLE payment_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    gateway_name VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT false,
    configuration JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- System logs table
CREATE TABLE system_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name', 'Hotelina', 'general'),
('site_email', 'info@hotelina.com', 'general'),
('currency', 'USD', 'general'),
('timezone', 'UTC', 'general'),
('smtp_host', '', 'email'),
('smtp_port', '', 'email'),
('smtp_username', '', 'email'),
('smtp_password', '', 'email'),
('smtp_encryption', 'tls', 'email');

-- Insert default email templates
INSERT INTO email_templates (template_name, subject, body, variables) VALUES
('booking_confirmation', 'Booking Confirmation - {booking_id}', 'Dear {guest_name},\n\nYour booking has been confirmed.\nBooking ID: {booking_id}\nCheck-in: {check_in}\nCheck-out: {check_out}\n\nThank you for choosing us!', '["booking_id", "guest_name", "check_in", "check_out"]'),
('booking_cancellation', 'Booking Cancellation - {booking_id}', 'Dear {guest_name},\n\nYour booking has been cancelled.\nBooking ID: {booking_id}\n\nWe hope to serve you in the future!', '["booking_id", "guest_name"]'); 
 