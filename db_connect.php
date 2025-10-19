<?php
$servername = "localhost";
$username = "root";  // default XAMPP user
$password = "";      // default XAMPP password is empty
$dbname = "tabulation_system_db";

// Enable error reporting (for debugging only; remove or comment out on production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

    // ✅ Check if table 'admin_users' exists
    $result = $conn->query("SHOW TABLES LIKE 'admin_users'");
    if ($result->num_rows === 0) {
        // Stop silently if table does not exist (avoid breaking index.php)
        return;
    }

    // 🔐 Define default admin accounts (strong passwords)
    $defaultAdmins = [
        [
            'username' => 'admin1',
            'password' => 'Adm1n!@#2025' // strong password with symbols, numbers, uppercase
        ],
        [
            'username' => 'admin2',
            'password' => 'S3cur3P@ss#2025' // another strong unique password
        ]
    ];

    foreach ($defaultAdmins as $admin) {
        $check = $conn->prepare("SELECT id FROM admin_users WHERE username = ?");
        $check->bind_param("s", $admin['username']);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            // Only insert if username doesn't exist
            $hashedPassword = password_hash($admin['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $admin['username'], $hashedPassword);
            $stmt->execute();
            $stmt->close();
        }

        $check->close();
    }

    $conn->close();

} catch (mysqli_sql_exception $e) {
    // Log silently instead of echoing
    error_log("Database error: " . $e->getMessage());
} catch (Exception $e) {
    error_log("Unexpected error: " . $e->getMessage());
}
?>
