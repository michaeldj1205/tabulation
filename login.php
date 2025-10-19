<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Use BINARY to make username comparison case-sensitive
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE BINARY username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $row["password"])) {
            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_username"] = $row["username"]; // keep exact stored case
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('No such user'); window.location.href='index.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
