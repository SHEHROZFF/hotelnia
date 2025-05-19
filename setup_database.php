<?php
require_once 'dbcon/Database.php';

// Create database connection
echo "Connecting to database...\n";
$db = new Database();
$pdo = $db->getConnection();
echo "Connected successfully.\n";

// Get SQL content
echo "Reading SQL file...\n";
$hotelsSql = file_get_contents('sql/hotels_tables.sql');
echo "SQL file read. Size: " . strlen($hotelsSql) . " bytes\n";

// Execute queries
try {
    // Split the SQL file into individual statements
    $queries = explode(';', $hotelsSql);
    echo "Found " . count($queries) . " SQL queries\n";
    
    foreach ($queries as $index => $query) {
        $query = trim($query);
        if (!empty($query)) {
            echo "Executing query #" . ($index + 1) . "...\n";
            $pdo->exec($query);
            echo "Query executed successfully.\n";
        }
    }
    
    echo "Database setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage() . "\n";
    echo "Query that failed: " . $query . "\n";
}

// Set up image directories
echo "Setting up image directories...\n";
require_once 'setup_image_dirs.php';
?> 