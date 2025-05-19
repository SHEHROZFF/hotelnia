-- Create hotels table
CREATE TABLE IF NOT EXISTS hotels (
    hotel_id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_name VARCHAR(100) NOT NULL,
    hotel_description TEXT,
    hotel_address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    star_rating INT DEFAULT 3,
    price_range_start DECIMAL(10, 2) DEFAULT 0,
    price_range_end DECIMAL(10, 2) DEFAULT 0,
    hotel_type VARCHAR(50) DEFAULT 'Hotel', -- Hotel, Resort, Apartment, etc.
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create hotel_images table
CREATE TABLE IF NOT EXISTS hotel_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);

-- Create amenities table
CREATE TABLE IF NOT EXISTS amenities (
    amenity_id INT AUTO_INCREMENT PRIMARY KEY,
    amenity_name VARCHAR(100) NOT NULL,
    amenity_icon VARCHAR(100) DEFAULT NULL,
    amenity_description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create hotel_amenities junction table
CREATE TABLE IF NOT EXISTS hotel_amenities (
    hotel_id INT NOT NULL,
    amenity_id INT NOT NULL,
    PRIMARY KEY (hotel_id, amenity_id),
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE,
    FOREIGN KEY (amenity_id) REFERENCES amenities(amenity_id) ON DELETE CASCADE
);

-- Create rooms table
CREATE TABLE IF NOT EXISTS rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT NOT NULL,
    room_type VARCHAR(100) NOT NULL,
    room_description TEXT,
    price_per_night DECIMAL(10, 2) NOT NULL,
    capacity INT DEFAULT 2,
    bed_type VARCHAR(50) DEFAULT 'Queen',
    room_size VARCHAR(50) DEFAULT NULL,
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);

-- Create room_images table
CREATE TABLE IF NOT EXISTS room_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

-- Insert sample amenities
INSERT INTO amenities (amenity_name, amenity_icon, amenity_description) VALUES
('Free WiFi', 'fa-wifi', 'Free high-speed WiFi throughout the property'),
('Swimming Pool', 'fa-swimming-pool', 'Outdoor swimming pool'),
('Fitness Center', 'fa-dumbbell', 'Fully equipped fitness center'),
('Restaurant', 'fa-utensils', 'On-site restaurant'),
('Bar', 'fa-glass-martini-alt', 'Stylish bar with variety of drinks'),
('Parking', 'fa-parking', 'Free parking on premises'),
('Room Service', 'fa-concierge-bell', '24/7 room service'),
('Spa', 'fa-spa', 'Full-service spa'),
('Air Conditioning', 'fa-snowflake', 'Climate control in all rooms'),
('Pet Friendly', 'fa-paw', 'Pets allowed on request');

-- Insert sample hotels
INSERT INTO hotels (hotel_name, hotel_description, hotel_address, city, country, star_rating, price_range_start, price_range_end, hotel_type, is_featured) VALUES
('Grand Hotel Plaza', 'Luxury hotel in the heart of the city with stunning views and top-notch amenities.', '123 Downtown St', 'New York', 'USA', 5, 350, 1200, 'Hotel', 1),
('Sunset Beach Resort', 'Beautiful beachfront resort with private access to pristine beaches.', '456 Ocean Dr', 'Miami', 'USA', 4, 250, 800, 'Resort', 1),
('Alpine Lodge', 'Cozy mountain retreat with spectacular views of the Alps.', '789 Mountain Rd', 'Zurich', 'Switzerland', 4, 200, 600, 'Lodge', 1),
('City Center Apartments', 'Modern apartments in the heart of the city, perfect for business travelers.', '321 Business Ave', 'London', 'United Kingdom', 3, 150, 400, 'Apartment', 0),
('Riverside Boutique Hotel', 'Charming boutique hotel on the river with personalized service.', '654 River Ln', 'Paris', 'France', 4, 280, 900, 'Hotel', 1),
('Desert Oasis Resort', 'Luxurious desert resort with private pools and spa treatments.', '987 Sand Blvd', 'Dubai', 'UAE', 5, 400, 1500, 'Resort', 1);

-- Connect hotels with amenities
INSERT INTO hotel_amenities (hotel_id, amenity_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9),
(2, 1), (2, 2), (2, 4), (2, 5), (2, 6), (2, 7), (2, 8), (2, 9),
(3, 1), (3, 4), (3, 6), (3, 7), (3, 9), (3, 10),
(4, 1), (4, 6), (4, 9),
(5, 1), (5, 4), (5, 5), (5, 6), (5, 7), (5, 9),
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6), (6, 7), (6, 8), (6, 9);

-- Insert sample rooms
INSERT INTO rooms (hotel_id, room_type, room_description, price_per_night, capacity, bed_type, room_size) VALUES
(1, 'Standard Room', 'Comfortable room with all basic amenities.', 350, 2, 'Queen', '30 sqm'),
(1, 'Deluxe Room', 'Spacious room with additional amenities and city view.', 550, 2, 'King', '40 sqm'),
(1, 'Executive Suite', 'Luxurious suite with separate living area and stunning views.', 1200, 4, 'King', '80 sqm'),
(2, 'Garden View Room', 'Cozy room with garden views.', 250, 2, 'Queen', '35 sqm'),
(2, 'Ocean View Room', 'Beautiful room with ocean views.', 450, 2, 'King', '40 sqm'),
(2, 'Beach Front Suite', 'Luxury suite with direct beach access.', 800, 4, 'King', '90 sqm'),
(3, 'Standard Alpine Room', 'Cozy room with mountain views.', 200, 2, 'Twin', '25 sqm'),
(3, 'Deluxe Alpine Room', 'Spacious room with balcony and mountain views.', 350, 2, 'Queen', '35 sqm'),
(3, 'Family Suite', 'Large suite ideal for families with mountain views.', 600, 6, 'Multiple', '100 sqm'),
(4, 'Studio Apartment', 'Modern studio with kitchenette.', 150, 2, 'Queen', '40 sqm'),
(4, 'One Bedroom Apartment', 'Spacious apartment with separate bedroom.', 250, 2, 'King', '60 sqm'),
(4, 'Two Bedroom Apartment', 'Large apartment with two bedrooms, ideal for families.', 400, 4, 'Multiple', '90 sqm'),
(5, 'Classic Room', 'Elegant room with vintage decor.', 280, 2, 'Queen', '30 sqm'),
(5, 'River View Room', 'Charming room with scenic river views.', 420, 2, 'King', '35 sqm'),
(5, 'Luxury Suite', 'Opulent suite with river views and premium amenities.', 900, 2, 'King', '70 sqm'),
(6, 'Desert View Room', 'Room with stunning desert views.', 400, 2, 'King', '45 sqm'),
(6, 'Oasis Pool Room', 'Room with direct access to the pool.', 650, 2, 'King', '50 sqm'),
(6, 'Royal Desert Suite', 'Spectacular suite with private pool and desert views.', 1500, 4, 'King', '120 sqm');

-- Insert sample hotel images
INSERT INTO hotel_images (hotel_id, image_path, is_primary, sort_order) VALUES
(1, 'assets/images/hotels/grand-plaza-1.jpg', 1, 1),
(1, 'assets/images/hotels/grand-plaza-2.jpg', 0, 2),
(1, 'assets/images/hotels/grand-plaza-3.jpg', 0, 3),
(2, 'assets/images/hotels/sunset-beach-1.jpg', 1, 1),
(2, 'assets/images/hotels/sunset-beach-2.jpg', 0, 2),
(2, 'assets/images/hotels/sunset-beach-3.jpg', 0, 3),
(3, 'assets/images/hotels/alpine-lodge-1.jpg', 1, 1),
(3, 'assets/images/hotels/alpine-lodge-2.jpg', 0, 2),
(3, 'assets/images/hotels/alpine-lodge-3.jpg', 0, 3),
(4, 'assets/images/hotels/city-apartments-1.jpg', 1, 1),
(4, 'assets/images/hotels/city-apartments-2.jpg', 0, 2),
(4, 'assets/images/hotels/city-apartments-3.jpg', 0, 3),
(5, 'assets/images/hotels/riverside-1.jpg', 1, 1),
(5, 'assets/images/hotels/riverside-2.jpg', 0, 2),
(5, 'assets/images/hotels/riverside-3.jpg', 0, 3),
(6, 'assets/images/hotels/desert-oasis-1.jpg', 1, 1),
(6, 'assets/images/hotels/desert-oasis-2.jpg', 0, 2),
(6, 'assets/images/hotels/desert-oasis-3.jpg', 0, 3);

-- Insert sample room images
INSERT INTO room_images (room_id, image_path, is_primary, sort_order) VALUES
(1, 'assets/images/rooms/standard-room-1.jpg', 1, 1),
(1, 'assets/images/rooms/standard-room-2.jpg', 0, 2),
(2, 'assets/images/rooms/deluxe-room-1.jpg', 1, 1),
(2, 'assets/images/rooms/deluxe-room-2.jpg', 0, 2),
(3, 'assets/images/rooms/executive-suite-1.jpg', 1, 1),
(3, 'assets/images/rooms/executive-suite-2.jpg', 0, 2),
(4, 'assets/images/rooms/garden-view-1.jpg', 1, 1),
(4, 'assets/images/rooms/garden-view-2.jpg', 0, 2),
(5, 'assets/images/rooms/ocean-view-1.jpg', 1, 1),
(5, 'assets/images/rooms/ocean-view-2.jpg', 0, 2),
(6, 'assets/images/rooms/beachfront-suite-1.jpg', 1, 1),
(6, 'assets/images/rooms/beachfront-suite-2.jpg', 0, 2);

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    guests INT DEFAULT 1,
    total_price DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES rooms(room_id) ON DELETE CASCADE
); 