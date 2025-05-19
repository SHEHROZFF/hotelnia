-- Add email verification fields to users table
ALTER TABLE users 
ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN verification_token VARCHAR(64) NULL,
ADD COLUMN token_expires_at DATETIME NULL;
 
-- Update existing users to mark them as verified
UPDATE users SET email_verified = 1 WHERE id > 0; 