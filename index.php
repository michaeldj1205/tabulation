<?php
session_start();

require_once "includes/auth.php";

// Determine current page (default = home)
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$validPages = ['home', 'login', 'game_schedule', 'event_results'];
if (!in_array($page, $validPages)) {
    $page = 'home';
}

// User info (mock auth context from session)
$user = $_SESSION['user'] ?? null;
$isAuthenticated = isset($user);

// Redirect admin users to admin dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin/dashboard.php");
    exit();
}

// Simple navigation (React's switch-case equivalent)
function renderPage($page, $user, $isAuthenticated)
{
    switch ($page) {
        case 'home':
            include "pages/home.php";
            break;
        case 'login':
            include "login.php";
            break;
        case 'game_schedule':
            include "pages/game_schedule_public.php";
            break;
        case 'event_results':
            include "event_results.php";
            break;

    }
}
?>

<?php include "includes/header.php"; ?>
<main>
    <?php renderPage($page, $user, $isAuthenticated); ?>
</main>
<?php include "includes/footer.php"; ?>

