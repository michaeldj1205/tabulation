<?php
// Load environment variables
require_once 'includes/env_loader.php';

// Get database configuration from environment
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'intramurals_tabulation';

// Enable error reporting (for debugging; remove or comment out in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // First, connect without database to check if it exists
    $conn_temp = new mysqli($servername, $username, $password);
    $conn_temp->set_charset("utf8mb4");

    // Check if database exists
    $result = $conn_temp->query("SHOW DATABASES LIKE '$dbname'");
    if ($result->num_rows == 0) {
        // Database does not exist, run setup
        include 'setup.php';
    }

    // Close the temp connection after setup (or if database exists)
    $conn_temp->close();

    // Now connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

    // Get admin credentials from environment
    $defaultUsername = getenv('ADMIN_USERNAME') ?: 'admin';
    $defaultPassword = getenv('ADMIN_PASSWORD') ?: '12345';

    // Check if admin already exists
    $check = $conn->prepare("SELECT id FROM admin_users WHERE username = ?");
    $check->bind_param("s", $defaultUsername);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        // No admin found — create one
        $check->close();

        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $defaultUsername, $hashedPassword);
        $stmt->execute();
        $stmt->close();
    } else {
        $check->close();
    }

} catch (mysqli_sql_exception $e) {
    // Fail silently or log errors for debugging (optional)
    // error_log("Database error: " . $e->getMessage());
    die("Database connection failed. Please contact the administrator.");
}
?>
