<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: index.html");
    exit();
}

include 'db_connect.php'; // assumes $conn (mysqli)

// ---------- Handle Create Match ----------
if (isset($_POST['create_match'])) {
    $sport_id = intval($_POST['sport_id']);
    $team_a = intval($_POST['team_a']);
    $team_b = intval($_POST['team_b']);
    $game_day = intval($_POST['game_day']);
    $game_date = !empty($_POST['game_date']) ? $_POST['game_date'] : null;
    $game_time = !empty($_POST['game_time']) ? $_POST['game_time'] : null;
    $location = !empty($_POST['location']) ? $_POST['location'] : null;

    $stmt = $conn->prepare("
        INSERT INTO game_schedule (sport_id, team_a_id, team_b_id, game_day, game_date, game_time, location)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiisss", $sport_id, $team_a, $team_b, $game_day, $game_date, $game_time, $location);
    $stmt->execute();
    $stmt->close();

    header("Location: game_schedule.php?created=1");
    exit();
}

// ---------- Handle Update Match ----------
if (isset($_POST['update_match'])) {
    $id = intval($_POST['match_id']);
    $sport_id = intval($_POST['sport_id']);
    $team_a = intval($_POST['team_a']);
    $team_b = intval($_POST['team_b']);
    $game_day = intval($_POST['game_day']);
    $game_date = !empty($_POST['game_date']) ? $_POST['game_date'] : null;
    $game_time = !empty($_POST['game_time']) ? $_POST['game_time'] : null;
    $location = !empty($_POST['location']) ? $_POST['location'] : null;

    $stmt = $conn->prepare("
        UPDATE game_schedule
        SET sport_id = ?, team_a_id = ?, team_b_id = ?, game_day = ?, game_date = ?, game_time = ?, location = ?
        WHERE id = ?
    ");
    $stmt->bind_param("iiiisssi", $sport_id, $team_a, $team_b, $game_day, $game_date, $game_time, $location, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: game_schedule.php?updated=1");
    exit();
}

// ---------- Handle Set Winner ----------
if (isset($_POST['set_winner'])) {
    $match_id = intval($_POST['match_id']);
    $winner_id = intval($_POST['winner_id']);

    $stmt = $conn->prepare("UPDATE game_schedule SET winner_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $winner_id, $match_id);
    $stmt->execute();
    $stmt->close();

    header("Location: game_schedule.php?winner_set=1");
    exit();
}

// ---------- Handle Delete ----------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM game_schedule WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: game_schedule.php?deleted=1");
    exit();
}

// ---------- Edit Mode ----------
$editMatch = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $q = $conn->prepare("SELECT * FROM game_schedule WHERE id = ?");
    $q->bind_param("i", $id);
    $q->execute();
    $res = $q->get_result();
    $editMatch = $res->fetch_assoc();
    $q->close();
}

// ---------- Fetch Data ----------
$sports = $conn->query("SELECT id, sport_name, category FROM sports ORDER BY sport_name ASC, category ASC");
$departments = $conn->query("SELECT id, code FROM departments ORDER BY code ASC");

$schedule_sql = "
    SELECT gs.*, s.sport_name, s.category,
           a.code AS team_a_code, b.code AS team_b_code, w.code AS winner_code
    FROM game_schedule gs
    JOIN sports s ON gs.sport_id = s.id
    JOIN departments a ON gs.team_a_id = a.id
    JOIN departments b ON gs.team_b_id = b.id
    LEFT JOIN departments w ON gs.winner_id = w.id
    ORDER BY gs.game_day ASC, gs.game_date ASC, gs.game_time ASC
";
$schedule = $conn->query($schedule_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🗓️ Game Schedule — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.navbar { background-color: #212529; }
.navbar-brand, .nav-link { color: #fff !important; }
.card { border-radius: 12px; }
.section-title { font-weight: 700; color: #343a40; }
.small-muted { font-size: 0.85rem; color: #6c757d; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">🏆 Admin Dashboard</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php#medals">🥇 Medal Tabulation</a></li>
        <li class="nav-item"><a class="nav-link" href="event_results.php">⚔️ Event Results</a></li>
        <li class="nav-item"><a class="nav-link active" href="game_schedule.php">🗓️ Game Schedule</a></li>
        <li class="nav-item"><a class="nav-link btn btn-danger text-white px-3 ms-2" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <div class="card shadow p-4 mb-4">
    <h3 class="section-title mb-3">🗓️ Game Schedule (Day 1 → Day 5)</h3>

    <?php if (isset($_GET['created'])): ?><div class="alert alert-success">✅ Match created.</div><?php endif; ?>
    <?php if (isset($_GET['updated'])): ?><div class="alert alert-success">✅ Match updated.</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">✅ Match deleted.</div><?php endif; ?>
    <?php if (isset($_GET['winner_set'])): ?><div class="alert alert-success">✅ Winner set.</div><?php endif; ?>

    <!-- Create/Edit Form -->
    <div class="mb-4">
      <form method="POST" class="row g-3">
        <input type="hidden" name="match_id" value="<?= $editMatch ? intval($editMatch['id']) : 0; ?>">
        
        <div class="col-md-4">
          <label class="form-label">Sport</label>
          <select name="sport_id" class="form-select" required>
            <option value="">-- Select Sport --</option>
            <?php while ($s = $sports->fetch_assoc()): ?>
              <?php $sel = ($editMatch && $editMatch['sport_id'] == $s['id']) ? 'selected' : ''; ?>
              <option value="<?= $s['id'] ?>" <?= $sel ?>>
                <?= htmlspecialchars($s['sport_name']) ?> (<?= htmlspecialchars($s['category']) ?>)
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Day (1-5)</label>
          <select name="game_day" class="form-select" required>
            <?php for ($d=1;$d<=5;$d++): ?>
              <option value="<?= $d ?>" <?= ($editMatch && $editMatch['game_day']==$d) ? 'selected' : '' ?>>Day <?= $d ?></option>
            <?php endfor; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Team A</label>
          <select name="team_a" class="form-select" required>
            <option value="">-- Select Team A --</option>
            <?php 
              $departments->data_seek(0);
              while ($d = $departments->fetch_assoc()): ?>
              <option value="<?= $d['id'] ?>" <?= ($editMatch && $editMatch['team_a_id'] == $d['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['code']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Team B</label>
          <select name="team_b" class="form-select" required>
            <option value="">-- Select Team B --</option>
            <?php 
              $departments->data_seek(0);
              while ($d = $departments->fetch_assoc()): ?>
              <option value="<?= $d['id'] ?>" <?= ($editMatch && $editMatch['team_b_id'] == $d['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['code']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Date</label>
          <input type="date" name="game_date" class="form-control" value="<?= $editMatch ? htmlspecialchars($editMatch['game_date']) : '' ?>">
        </div>

        <div class="col-md-2">
          <label class="form-label">Time</label>
          <input type="time" name="game_time" class="form-control" value="<?= $editMatch ? htmlspecialchars($editMatch['game_time']) : '' ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Location</label>
          <input type="text" name="location" class="form-control" value="<?= $editMatch ? htmlspecialchars($editMatch['location']) : '' ?>" placeholder="Optional">
        </div>

        <div class="col-12">
          <?php if ($editMatch): ?>
            <button type="submit" name="update_match" class="btn btn-primary">💾 Update Match</button>
            <a href="game_schedule.php" class="btn btn-secondary">Cancel</a>
          <?php else: ?>
            <button type="submit" name="create_match" class="btn btn-success">➕ Create Match</button>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Schedule Table -->
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>Day</th>
            <th>Sport (Category)</th>
            <th>Team A</th>
            <th>Team B</th>
            <th>Date / Time</th>
            <th>Location</th>
            <th>Winner</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $schedule->fetch_assoc()): ?>
            <tr>
              <td>Day <?= intval($row['game_day']); ?></td>
              <td><?= htmlspecialchars($row['sport_name']) ?> (<?= htmlspecialchars($row['category']) ?>)</td>
              <td><?= htmlspecialchars($row['team_a_code']); ?></td>
              <td><?= htmlspecialchars($row['team_b_code']); ?></td>
              <td>
                <?= $row['game_date'] ? htmlspecialchars($row['game_date']) : '' ?>
                <?= $row['game_time'] ? ' / ' . htmlspecialchars($row['game_time']) : '' ?>
              </td>
              <td><?= $row['location'] ? htmlspecialchars($row['location']) : '<span class="small-muted">—</span>'; ?></td>
              <td><?= $row['winner_code'] ? '<strong>' . htmlspecialchars($row['winner_code']) . '</strong>' : '<span class="small-muted">—</span>'; ?></td>
              <td>
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">
                  <a href="?edit=<?= intval($row['id']); ?>" class="btn btn-sm btn-outline-primary">✏️ Edit</a>

                  <form method="POST" class="d-flex align-items-center gap-2" style="margin:0;">
                    <input type="hidden" name="match_id" value="<?= intval($row['id']); ?>">
                    <select name="winner_id" class="form-select form-select-sm" style="width:140px;">
                      <option value="0">— Set Winner —</option>
                      <?php 
                        $departments->data_seek(0);
                        while ($d = $departments->fetch_assoc()):
                      ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['code']) ?></option>
                      <?php endwhile; ?>
                    </select>
                    <button type="submit" name="set_winner" class="btn btn-sm btn-primary">🏁 Set</button>
                  </form>

                  <a href="?delete=<?= intval($row['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this match?');">🗑</a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>

          <?php if ($schedule->num_rows === 0): ?>
            <tr><td colspan="8">No matches scheduled yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>
</body>
</html>
