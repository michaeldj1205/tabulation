<?php
include 'db_connect.php';

// 🥇 Fetch all departments with medal counts (default 0 if no medals)
$medals = $conn->query("
    SELECT 
        d.code, 
        d.mascot, 
        COALESCE(m.gold, 0) AS gold, 
        COALESCE(m.silver, 0) AS silver, 
        COALESCE(m.bronze, 0) AS bronze, 
        COALESCE(m.total, 0) AS total
    FROM departments d
    LEFT JOIN medals m ON d.id = m.department_id
    ORDER BY total DESC, gold DESC, silver DESC, bronze DESC, d.code ASC
");

// ⚔️ Fetch all sports, with event results if any
$results = $conn->query("
    SELECT 
        s.sport_name,
        s.category,
        d1.code AS gold_code,
        d2.code AS silver_code,
        d3.code AS bronze_code
    FROM sports s
    LEFT JOIN event_results e ON e.sport_id = s.id
    LEFT JOIN departments d1 ON e.gold_winner = d1.id
    LEFT JOIN departments d2 ON e.silver_winner = d2.id
    LEFT JOIN departments d3 ON e.bronze_winner = d3.id
    ORDER BY s.sport_name ASC, s.category ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🏆 Intramural Medal Standings</title>
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
.rank-1 { background-color: #fff3cd !important; }
.rank-2 { background-color: #e2e3e5 !important; }
.rank-3 { background-color: #f8d7da !important; }
.footer {
  background-color: #212529;
  color: white;
  text-align: center;
  padding: 10px;
  margin-top: 40px;
}
.table td, .table th {
  vertical-align: middle;
}
</style>
</head>
<body>

<!-- 🧭 NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">🏆 Intramurals 2025</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="#medals">🥇 Medal Standings</a></li>
        <li class="nav-item"><a class="nav-link" href="#events">⚔️ Event Results</a></li>
        <li class="nav-item"><a class="nav-link" href="game_schedule_public.php">Game Schedule</a></li>
        <li class="nav-item"><a class="nav-link" href="signin.php"> Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- 🥇 MEDAL TABULATION -->
<section id="medals" class="container mt-5">
  <div class="card shadow p-4 mb-5">
    <h2 class="section-title text-center mb-4">🥇 Overall Medal Standings</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center align-middle">
        <thead>
          <tr>
            <th>Rank</th>
            <th>Department</th>
            <th>Mascot</th>
            <th>Gold 🥇</th>
            <th>Silver 🥈</th>
            <th>Bronze 🥉</th>
            <th>Total 🧮</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $rank = 1;
          while ($row = $medals->fetch_assoc()):
            $rankClass = ($rank == 1) ? "rank-1" : (($rank == 2) ? "rank-2" : (($rank == 3) ? "rank-3" : ""));
          ?>
          <tr class="<?= $rankClass ?>">
            <td><strong><?= $rank++; ?></strong></td>
            <td><strong><?= htmlspecialchars($row['code']); ?></strong></td>
            <td><?= htmlspecialchars($row['mascot']); ?></td>
            <td><?= $row['gold']; ?></td>
            <td><?= $row['silver']; ?></td>
            <td><?= $row['bronze']; ?></td>
            <td><strong><?= $row['total']; ?></strong></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ⚔️ EVENT RESULTS -->
<section id="events" class="container mb-5">
  <div class="card shadow p-4">
    <h2 class="section-title text-center mb-4">⚔️ Event Medal Results</h2>
    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>Sport</th>
            <th>Category</th>
            <th>🥇 Gold</th>
            <th>🥈 Silver</th>
            <th>🥉 Bronze</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($r = $results->fetch_assoc()): ?>
          <tr>
            <td><strong><?= htmlspecialchars($r['sport_name']); ?></strong></td>
            <td><?= htmlspecialchars($r['category']); ?></td>
            <td><?= $r['gold_code'] ?? '--'; ?></td>
            <td><?= $r['silver_code'] ?? '--'; ?></td>
            <td><?= $r['bronze_code'] ?? '--'; ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- FOOTER -->
<div class="footer">
  <p>© <?= date("Y") ?> Intramurals | Real-time Medal Standings System</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
