<?php
$servername = "localhost";
$username = "root";  // default XAMPP user
$password = "";      // default XAMPP password is empty
$dbname = "tabulation_system_db";

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

} catch (mysqli_sql_exception $e) {
    // Fail silently or log errors for debugging (optional)
    // error_log("Database error: " . $e->getMessage());
    die("Database connection failed. Please contact the administrator.");
}
?>
