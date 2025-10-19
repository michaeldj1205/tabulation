<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🏆 Intramural Medal Standings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Add Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="./assets/css/style.css" rel="stylesheet">
</head>

<body>

  <!-- 🧭 NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow" style="position: sticky; top: 0; z-index: 1030;">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <span>🏆 Intramurals 2025</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link <?= $page === 'home' ? 'active' : '' ?>" href="index.php?page=home">Home</a></li>
          <li class="nav-item"><a class="nav-link <?= $page === 'game_schedule' ? 'active' : '' ?>" href="index.php?page=game_schedule">Game Schedule</a></li>
          <li class="nav-item"><a class="nav-link <?= $page === 'login' ? 'active' : '' ?>" href="index.php?page=login">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>