<?php
$servername = "localhost";
$username = "root";  // default XAMPP user
$password = "";      // default XAMPP password is empty
$dbname = "admin_portal";

// Enable error reporting (for debugging; remove or comment out in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

    // Default admin credentials
    $defaultUsername = "admin";
    $defaultPassword = "12345";

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
