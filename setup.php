<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Output as HTML
echo "<!DOCTYPE html>
<html>
<head>
    <title>Hotelina Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
        .step { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Hotelina Setup</h1>";

echo "<div class='step'><h2>Step 1: Database Connection</h2>";
try {
    require_once 'dbcon/Database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    echo "<p class='success'>Connected to database successfully!</p>";
} catch (Exception $e) {
    echo "<p class='error'>Error connecting to database: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure your database is set up correctly in dbcon/Database.php</p>";
    exit("</div></body></html>");
}
echo "</div>";

echo "<div class='step'><h2>Step 2: Setting up hotel database tables</h2>";
try {
    // Check if hotels table already exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'hotels'");
    $stmt->execute();
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p class='success'>Hotel tables already exist. Skipping table creation.</p>";
    } else {
        // Read SQL file
        $sqlFile = 'sql/hotels_tables.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL file not found: $sqlFile");
        }
        
        $hotelsSql = file_get_contents($sqlFile);
        echo "<p>SQL file read. Size: " . strlen($hotelsSql) . " bytes</p>";
        
        // Split the SQL file into individual statements
        $queries = explode(';', $hotelsSql);
        echo "<p>Found " . count($queries) . " SQL queries</p>";
        
        $executedQueries = 0;
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
                $executedQueries++;
            }
        }
        
        echo "<p class='success'>Successfully executed $executedQueries queries.</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>Error setting up database tables: " . $e->getMessage() . "</p>";
    echo "<pre>" . (isset($query) ? htmlspecialchars($query) : '') . "</pre>";
}
echo "</div>";

echo "<div class='step'><h2>Step 3: Setting up image directories</h2>";
try {
    $directories = [
        'assets/images/hotels',
        'assets/images/rooms'
    ];
    
    $createdDirs = [];
    $existingDirs = [];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            if (mkdir($dir, 0755, true)) {
                $createdDirs[] = $dir;
            } else {
                throw new Exception("Failed to create directory: $dir");
            }
        } else {
            $existingDirs[] = $dir;
        }
    }
    
    if (!empty($createdDirs)) {
        echo "<p class='success'>Created directories:</p><ul>";
        foreach ($createdDirs as $dir) {
            echo "<li>$dir</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($existingDirs)) {
        echo "<p>Directories already exist:</p><ul>";
        foreach ($existingDirs as $dir) {
            echo "<li>$dir</li>";
        }
        echo "</ul>";
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
    
    $copiedHotelImages = 0;
    $copiedRoomImages = 0;
    
    // Create hotel placeholder images
    foreach ($hotelPlaceholders as $index => $placeholder) {
        $sourceIndex = $index % count($sourceImages);
        $source = $sourceImages[$sourceIndex];
        $destination = 'assets/images/hotels/' . $placeholder;
        
        if (!file_exists($destination) && file_exists($source)) {
            if (copy($source, $destination)) {
                $copiedHotelImages++;
            }
        }
    }
    
    // Create room placeholder images
    foreach ($roomPlaceholders as $index => $placeholder) {
        $sourceIndex = $index % count($sourceImages);
        $source = $sourceImages[$sourceIndex];
        $destination = 'assets/images/rooms/' . $placeholder;
        
        if (!file_exists($destination) && file_exists($source)) {
            if (copy($source, $destination)) {
                $copiedRoomImages++;
            }
        }
    }
    
    echo "<p class='success'>Copied $copiedHotelImages hotel images and $copiedRoomImages room images.</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>Error setting up image directories: " . $e->getMessage() . "</p>";
}
echo "</div>";

echo "<div class='step'><h2>Setup Complete</h2>
<p class='success'>The Hotelina hotel booking system has been successfully set up!</p>
<p>You can now:</p>
<ul>
    <li><a href='index.php'>Visit the homepage</a></li>
    <li><a href='Hotels.php'>Browse all hotels</a></li>
    <li><a href='Login.php'>Login to your account</a></li>
</ul>
</div>";

echo "</body></html>";
?> 