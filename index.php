<?php
include 'db_connect.php';

// 🥇 Fetch only OVERALL medal standings (default 0 if no medals)
$medals = $conn->query("
    SELECT 
        d.code, 
        d.mascot, 
        COALESCE(m.gold, 0) AS gold, 
        COALESCE(m.silver, 0) AS silver, 
        COALESCE(m.bronze, 0) AS bronze, 
        COALESCE(m.total, 0) AS total
    FROM departments d
    LEFT JOIN medals m 
        ON d.id = m.department_id 
        AND m.category = 'Overall'
    ORDER BY total DESC, gold DESC, silver DESC, bronze DESC, d.code ASC
");

// ⚔️ Fetch all sports, with event results if any (unchanged)
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
<link href="assets/css/style.css" rel="stylesheet">
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
  <h2 class="section-title text-center mb-4">🥇 Overall Medal Standings</h2>
  <div class="row">
    <?php
    $rank = 1;
    while ($row = $medals->fetch_assoc()):
      $rankClass = ($rank == 1) ? "rank-1" : (($rank == 2) ? "rank-2" : (($rank == 3) ? "rank-3" : ""));
    ?>
    <div class="col-lg-4 col-md-6 mb-4">
      <div class="card medal-card h-100 position-relative">
        <div class="rank-badge <?= $rankClass ?>">
          <?= $rank++ ?>
        </div>
        <div class="card-body text-center">
          <h5 class="department-name"><?= htmlspecialchars($row['code']); ?></h5>
          <p class="mascot-name"><?= htmlspecialchars($row['mascot']); ?></p>

          <div class="medal-counts">
            <div class="medal-item">
              <div class="medal-icon gold">🥇</div>
              <span class="medal-number"><?= $row['gold']; ?></span>
            </div>
            <div class="medal-item">
              <div class="medal-icon silver">🥈</div>
              <span class="medal-number"><?= $row['silver']; ?></span>
            </div>
            <div class="medal-item">
              <div class="medal-icon bronze">🥉</div>
              <span class="medal-number"><?= $row['bronze']; ?></span>
            </div>
          </div>

          <div class="total-medals mt-3">
            <strong>Total: <?= $row['total']; ?></strong>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- ⚔️ EVENT RESULTS -->
<section id="events" class="container mb-5">
  <h2 class="section-title text-center mb-4">⚔️ Event Medal Results</h2>
  <div class="row">
    <?php while ($r = $results->fetch_assoc()): ?>
    <div class="col-lg-6 col-md-6 mb-4">
      <div class="card event-card h-100">
        <div class="event-header">
          <h5 class="event-title">⚔️ <?= htmlspecialchars($r['sport_name']); ?></h5>
          <p class="event-category"><?= htmlspecialchars($r['category']); ?></p>
        </div>
        <div class="card-body">
          <div class="winner-section">
            <div class="winner-item">
              <div class="winner-label">🥇 Gold</div>
              <div class="winner-name">
                <?= $r['gold_code'] ? htmlspecialchars($r['gold_code']) : '<span class="no-winner">--</span>'; ?>
              </div>
            </div>
            <div class="winner-item">
              <div class="winner-label">🥈 Silver</div>
              <div class="winner-name">
                <?= $r['silver_code'] ? htmlspecialchars($r['silver_code']) : '<span class="no-winner">--</span>'; ?>
              </div>
            </div>
            <div class="winner-item">
              <div class="winner-label">🥉 Bronze</div>
              <div class="winner-name">
                <?= $r['bronze_code'] ? htmlspecialchars($r['bronze_code']) : '<span class="no-winner">--</span>'; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- FOOTER -->
<div class="footer">
  <p>© <?= date("Y") ?> Intramurals | Real-time Medal Standings System</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
