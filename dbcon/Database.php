
<?php
class Database {
//     private $host = 'localhost';
//     private $db = 'u885285659_fennce_db';
//     private $user = 'u885285659_admin';
//     private $pass = 'Izzi_123';
    private $host = 'localhost';
    private $db = 'hotelnia';
    private $user = 'root';
    private $pass = '';
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->db}", $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Database connection failed: " . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
    public function backup($outputFile) {
        $command = "mysqldump --opt -h {$this->host} -u {$this->user} -p{$this->pass} {$this->db} > {$outputFile}";
        
        // Capture output and result of command execution
        exec($command, $output, $result);
    
        // Log the output and result for debugging
        error_log("Command: {$command}");
        error_log("Result: {$result}");
        error_log("Output: " . implode("\n", $output));
    
        // Check if the backup was successful
        if ($result === 0) {
            return true;
        } else {
            return false;
        }
    }
}
?>
