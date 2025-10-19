<?php
include 'db_connect.php';

// Fetch all schedules grouped by day (1–5)
$schedules = $conn->query("
    SELECT 
        gs.*,
        s.sport_name,
        s.category,
        a.code AS team_a,
        b.code AS team_b,
        w.code AS winner
    FROM game_schedule gs
    JOIN sports s ON gs.sport_id = s.id
    JOIN departments a ON gs.team_a_id = a.id
    JOIN departments b ON gs.team_b_id = b.id
    LEFT JOIN departments w ON gs.winner_id = w.id
    ORDER BY gs.game_day ASC, gs.game_date ASC, gs.game_time ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🗓️ Intramurals 2025 | Game Schedule</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background-color: #f8f9fa;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  scroll-behavior: smooth;
}
.navbar {
  background-color: #212529;
}
.navbar-brand, .nav-link {
  color: #fff !important;
}
.section-title {
  font-weight: 700;
  color: #343a40;
}
.card {
  border-radius: 15px;
}
th {
  background-color: #212529 !important;
  color: white !important;
}
.table td, .table th {
  vertical-align: middle;
}
.day-header {
  background-color: #343a40;
  color: white;
  padding: 10px;
  font-weight: bold;
  border-radius: 5px;
  margin-bottom: 10px;
}
.footer {
  background-color: #212529;
  color: white;
  text-align: center;
  padding: 10px;
  margin-top: 40px;
}
</style>
</head>
<body>

<!-- 🧭 NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">🏆 Intramurals 2025</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php#medals">🥇 Medal Standings</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#events">⚔️ Event Results</a></li>
        <li class="nav-item"><a class="nav-link active" href="game_schedule_public.php">🗓️ Game Schedule</a></li>
        <li class="nav-item"><a class="nav-link" href="signin.php">Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- 🗓️ GAME SCHEDULE SECTION -->
<section class="container mt-5 mb-5">
  <div class="card shadow p-4 mb-5">
    <h2 class="section-title text-center mb-4">🗓️ Intramurals Game Schedule</h2>

    <?php 
    $currentDay = null;
    $hasData = false;

    if ($schedules->num_rows > 0):
      while ($row = $schedules->fetch_assoc()):
        $day = intval($row['game_day']);
        if ($currentDay !== $day):
          if ($currentDay !== null) echo "</tbody></table></div></div>";
          echo "<div class='mb-4'><div class='day-header'>📅 Day {$day}</div>";
          echo "<div class='table-responsive'><table class='table table-bordered text-center align-middle'>";
          echo "<thead><tr>
                  <th>Sport</th>
                  <th>Category</th>
                  <th>Team A</th>
                  <th>Team B</th>
                  <th>Date / Time</th>
                  <th>Location</th>
                  <th>Winner</th>
                </tr></thead><tbody>";
          $currentDay = $day;
          $hasData = true;
        endif;
    ?>
        <tr>
          <td><strong><?= htmlspecialchars($row['sport_name']); ?></strong></td>
          <td><?= htmlspecialchars($row['category']); ?></td>
          <td><?= htmlspecialchars($row['team_a']); ?></td>
          <td><?= htmlspecialchars($row['team_b']); ?></td>
          <td>
            <?= $row['game_date'] ? htmlspecialchars($row['game_date']) : ''; ?>
            <?= $row['game_time'] ? ' / ' . htmlspecialchars(substr($row['game_time'], 0, 5)) : ''; ?>
          </td>
          <td><?= $row['location'] ? htmlspecialchars($row['location']) : '<span class="text-muted">—</span>'; ?></td>
          <td>
            <?= $row['winner'] 
                ? '<span class="badge bg-success px-3 py-2">🏆 ' . htmlspecialchars($row['winner']) . '</span>' 
                : '<span class="text-muted">Pending</span>'; ?>
          </td>
        </tr>
    <?php endwhile; ?>

    </tbody></table></div></div>

    <?php else: ?>
      <p class="text-center mt-3 mb-3">No games scheduled yet.</p>
    <?php endif; ?>
  </div>
</section>

<!-- FOOTER -->
<div class="footer">
  <p>© <?= date("Y") ?> Intramurals | Real-time Game Schedule</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
