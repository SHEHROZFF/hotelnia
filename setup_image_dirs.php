<?php
// Set up directories for hotel and room images
$directories = [
    'assets/images/hotels',
    'assets/images/rooms'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir<br>";
    } else {
        echo "Directory already exists: $dir<br>";
    }
}

// Create placeholder images for hotels
$hotelPlaceholders = [
    'grand-plaza-1.jpg',
    'grand-plaza-2.jpg',
    'grand-plaza-3.jpg',
    'sunset-beach-1.jpg',
    'sunset-beach-2.jpg',
    'sunset-beach-3.jpg',
    'alpine-lodge-1.jpg',
    'alpine-lodge-2.jpg',
    'alpine-lodge-3.jpg',
    'city-apartments-1.jpg',
    'city-apartments-2.jpg',
    'city-apartments-3.jpg',
    'riverside-1.jpg',
    'riverside-2.jpg',
    'riverside-3.jpg',
    'desert-oasis-1.jpg',
    'desert-oasis-2.jpg',
    'desert-oasis-3.jpg'
];

// Create placeholder images for rooms
$roomPlaceholders = [
    'standard-room-1.jpg',
    'standard-room-2.jpg',
    'deluxe-room-1.jpg',
    'deluxe-room-2.jpg',
    'executive-suite-1.jpg',
    'executive-suite-2.jpg',
    'garden-view-1.jpg',
    'garden-view-2.jpg',
    'ocean-view-1.jpg',
    'ocean-view-2.jpg',
    'beachfront-suite-1.jpg',
    'beachfront-suite-2.jpg'
];

// Use existing image files for hotel placeholders
$sourceImages = [
    'assets/images/Hotel Booking/hotel-11_cms_1739791039.jpg',
    'assets/images/Hotel Booking/hotel-12_cms_1739791054.jpg',
    'assets/images/Hotel Booking/hotel-13_cms_1739791066.jpg',
    'assets/images/Hotel Booking/hotel-14_cms_1739791078.jpg',
    'assets/images/Hotel Booking/hotel-15_cms_1739791103.jpg',
    'assets/images/Hotel Booking/hotel-16_cms_1739791119.jpg'
];

// Create hotel placeholder images
foreach ($hotelPlaceholders as $index => $placeholder) {
    $sourceIndex = $index % count($sourceImages);
    $source = $sourceImages[$sourceIndex];
    $destination = 'assets/images/hotels/' . $placeholder;
    
    if (!file_exists($destination) && file_exists($source)) {
        copy($source, $destination);
        echo "Created placeholder image: $destination<br>";
    }
}

// Create room placeholder images
foreach ($roomPlaceholders as $index => $placeholder) {
    $sourceIndex = $index % count($sourceImages);
    $source = $sourceImages[$sourceIndex];
    $destination = 'assets/images/rooms/' . $placeholder;
    
    if (!file_exists($destination) && file_exists($source)) {
        copy($source, $destination);
        echo "Created placeholder image: $destination<br>";
    }
}

echo "Setup complete!";
?> 