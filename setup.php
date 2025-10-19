<?php
$servername = "localhost";
$username = "root";  // default XAMPP user
$password = "";      // default XAMPP password is empty
$dbname = "tabulation_system_db";

// Enable error reporting (for debugging; remove or comment out in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Connect without database to create it
    $conn_temp = new mysqli($servername, $username, $password);
    $conn_temp->set_charset("utf8mb4");

    // Create database if it doesn't exist
    $conn_temp->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $conn_temp->select_db($dbname);

    // Read and execute the SQL dump
    $sql = file_get_contents('sql/admin_portal.sql');

    // Execute the entire SQL dump using multi_query
    if ($conn_temp->multi_query($sql)) {
        do {
            // Store first result set
            if ($result = $conn_temp->store_result()) {
                $result->free();
            }
        } while ($conn_temp->more_results() && $conn_temp->next_result());
    }

    // Now create the default admin user
    $defaultUsername = "admin";
    $defaultPassword = "12345";

    // Check if admin already exists
    $check = $conn_temp->prepare("SELECT id FROM admin_users WHERE username = ?");
    $check->bind_param("s", $defaultUsername);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        // No admin found — create one
        $check->close();

        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $conn_temp->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $defaultUsername, $hashedPassword);
        $stmt->execute();
        $stmt->close();
    } else {
        $check->close();
    }

    // Don't close here - let db_connect.php handle it

} catch (mysqli_sql_exception $e) {
    // Log error for debugging
    error_log("Database setup error: " . $e->getMessage());
    die("Database setup failed. Please contact the administrator. Error: " . $e->getMessage());
}
?>