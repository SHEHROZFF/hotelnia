<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
        throw new Exception("Invalid user ID");
    }

    $user_id = intval($_POST['user_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Start transaction
    $db->beginTransaction();

    try {
        // Check if user exists and is a guest
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'user'");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Guest not found");
        }

        // Check for active bookings
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM bookings 
            WHERE user_id = ? AND status IN ('confirmed', 'checked_in')
        ");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            throw new Exception("Cannot delete guest with active bookings");
        }

        // Delete guest's bookings
        $stmt = $db->prepare("DELETE FROM bookings WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete guest
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        $stmt->execute([$user_id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Failed to delete guest");
        }

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Guest deleted successfully'
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error deleting guest: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting guest: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 