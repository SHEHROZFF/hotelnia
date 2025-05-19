<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Output as HTML
echo "<!DOCTYPE html>
<html>
<head>
    <title>Hotelina Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
        .step { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        button { padding: 10px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 4px; }
        button:hover { background: #45a049; }
        .queries { max-height: 300px; overflow-y: auto; }
    </style>
</head>
<body>
    <h1>Hotelina Database Setup</h1>";

if (isset($_POST['run_setup'])) {
    // Step 1: Connect to database
    echo "<div class='step'><h2>Step 1: Database Connection</h2>";
    try {
        require_once 'dbcon/Database.php';
        $db = new Database();
        $pdo = $db->getConnection();
        echo "<p class='success'>✓ Connected to database successfully!</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error connecting to database: " . $e->getMessage() . "</p>";
        echo "<p>Please make sure your database is set up correctly in dbcon/Database.php</p>";
        exit("</div></body></html>");
    }
    echo "</div>";

    // Step 2: Run SQL queries
    echo "<div class='step'><h2>Step 2: Setting up hotel database tables</h2>";
    try {
        // Read SQL file
        $sqlFile = 'sql/hotels_tables.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL file not found: $sqlFile");
        }
        
        $hotelsSql = file_get_contents($sqlFile);
        echo "<p>SQL file read. Size: " . strlen($hotelsSql) . " bytes</p>";
        
        // Split the SQL file into individual statements
        $queries = explode(';', $hotelsSql);
        $validQueries = array_filter($queries, function($query) {
            return trim($query) !== '';
        });
        
        echo "<p>Found " . count($validQueries) . " SQL queries</p>";
        
        echo "<div class='queries'>";
        $executedQueries = 0;
        foreach ($validQueries as $index => $query) {
            $query = trim($query);
            if (!empty($query)) {
                try {
                    echo "<p>Executing query #" . ($index + 1) . "...</p>";
                    $pdo->exec($query);
                    echo "<p class='success'>✓ Query executed successfully.</p>";
                    $executedQueries++;
                } catch (PDOException $e) {
                    echo "<p class='error'>✗ Error executing query #" . ($index + 1) . ": " . $e->getMessage() . "</p>";
                    echo "<pre>" . htmlspecialchars($query) . "</pre>";
                }
            }
        }
        echo "</div>";
        
        echo "<p class='success'>✓ Successfully executed $executedQueries out of " . count($validQueries) . " queries.</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error setting up database tables: " . $e->getMessage() . "</p>";
    }
    echo "</div>";

    // Step 3: Create directories and copy images
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
            echo "<p class='success'>✓ Created directories:</p><ul>";
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
        
        // Create placeholder images
        include_once 'setup_image_dirs.php';
        echo "<p class='success'>✓ Image setup complete.</p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error setting up image directories: " . $e->getMessage() . "</p>";
    }
    echo "</div>";

    echo "<div class='step'><h2>Setup Complete</h2>
    <p class='success'>✓ The Hotelina hotel booking system has been successfully set up!</p>
    <p>You can now:</p>
    <ul>
        <li><a href='index.php'>Visit the homepage</a></li>
        <li><a href='Hotels.php'>Browse all hotels</a></li>
        <li><a href='Login.php'>Login to your account</a></li>
    </ul>
    </div>";
} else {
    // Display setup form
    echo "
    <p>This script will set up the database tables and image directories for the Hotelina hotel booking system.</p>
    <p><strong>Warning:</strong> If tables already exist, their data will be preserved. Only missing tables will be created.</p>
    
    <form method='post' action=''>
        <button type='submit' name='run_setup'>Run Database Setup</button>
    </form>
    ";
}

echo "</body></html>";
?> 