<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: index.html");
    exit();
}

include 'db_connect.php';

// 🏆 Save or Update Event Result
if (isset($_POST['save_result']) || isset($_POST['update_result']) || isset($_POST['quick_update'])) {
    $sport_id = intval($_POST['sport_id']);
    $gold = !empty($_POST['gold_winner']) ? intval($_POST['gold_winner']) : 0;
    $silver = !empty($_POST['silver_winner']) ? intval($_POST['silver_winner']) : 0;
    $bronze = !empty($_POST['bronze_winner']) ? intval($_POST['bronze_winner']) : 0;

    // 🔍 Get the sport's category (Men/Women/Mix)
    $cat_stmt = $conn->prepare("SELECT category FROM sports WHERE id = ?");
    $cat_stmt->bind_param("i", $sport_id);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();
    $category = $cat_result->fetch_assoc()['category'] ?? 'Mix';
    $cat_stmt->close();

    // 🧩 UPDATE EXISTING RESULT
    if (isset($_POST['update_result']) || isset($_POST['quick_update'])) {
        $id = intval($_POST['result_id']);

        // Get previous winners (for recalculation)
        $prev = $conn->query("SELECT gold_winner, silver_winner, bronze_winner FROM event_results WHERE id=$id")->fetch_assoc();

        // Update event result safely (NULL for empty)
        $stmt = $conn->prepare("
            UPDATE event_results 
            SET gold_winner = NULLIF(?, 0), 
                silver_winner = NULLIF(?, 0), 
                bronze_winner = NULLIF(?, 0)
            WHERE id = ?
        ");
        $stmt->bind_param("iiii", $gold, $silver, $bronze, $id);
        $stmt->execute();

        // Reset old medal counts by category
        if ($prev) {
            foreach (['gold_winner' => 'gold', 'silver_winner' => 'silver', 'bronze_winner' => 'bronze'] as $winner => $type) {
                if (!empty($prev[$winner])) {
                    $conn->query("
                        UPDATE medals 
                        SET $type = GREATEST($type - 1, 0), 
                            total = gold + silver + bronze 
                        WHERE department_id = {$prev[$winner]} 
                        AND category = '$category'
                    ");
                }
            }
        }
    } else {
        // 🧩 INSERT NEW EVENT RESULT
        $stmt = $conn->prepare("
            INSERT INTO event_results (sport_id, gold_winner, silver_winner, bronze_winner)
            VALUES (?, NULLIF(?, 0), NULLIF(?, 0), NULLIF(?, 0))
        ");
        $stmt->bind_param("iiii", $sport_id, $gold, $silver, $bronze);
        $stmt->execute();
    }

    // 🥇 FUNCTION TO UPDATE MEDALS PER CATEGORY
    function updateMedals($conn, $dept_id, $category, $gold, $silver, $bronze) {
        if (!$dept_id) return;

        // Check if this department/category combo exists
        $check = $conn->prepare("SELECT id FROM medals WHERE department_id=? AND category=?");
        $check->bind_param("is", $dept_id, $category);
        $check->execute();
        $res = $check->get_result();

        $total = $gold + $silver + $bronze;

        if ($res->num_rows > 0) {
            $stmt = $conn->prepare("
                UPDATE medals 
                SET gold = gold + ?, 
                    silver = silver + ?, 
                    bronze = bronze + ?, 
                    total = gold + silver + bronze 
                WHERE department_id = ? AND category = ?
            ");
            $stmt->bind_param("iiiis", $gold, $silver, $bronze, $dept_id, $category);
        } else {
            $stmt = $conn->prepare("
                INSERT INTO medals (department_id, category, gold, silver, bronze, total) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("isiiii", $dept_id, $category, $gold, $silver, $bronze, $total);
        }
        $stmt->execute();
    }

    // 🥇 Update medals for each winner
    updateMedals($conn, $gold, $category, 1, 0, 0);
    updateMedals($conn, $silver, $category, 0, 1, 0);
    updateMedals($conn, $bronze, $category, 0, 0, 1);

    header("Location: event_results.php?success=1");
    exit();
}

// ❌ Delete Event Result
if (isset($_GET['delete_result'])) {
    $id = $_GET['delete_result'];

    $get_winners = $conn->prepare("
        SELECT e.gold_winner, e.silver_winner, e.bronze_winner, s.category 
        FROM event_results e
        JOIN sports s ON e.sport_id = s.id
        WHERE e.id = ?
    ");
    $get_winners->bind_param("i", $id);
    $get_winners->execute();
    $result = $get_winners->get_result()->fetch_assoc();
    $get_winners->close();

    $stmt = $conn->prepare("DELETE FROM event_results WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($result) {
        $category = $result['category'];
        foreach (['gold_winner' => 'gold', 'silver_winner' => 'silver', 'bronze_winner' => 'bronze'] as $winner => $type) {
            if (!empty($result[$winner])) {
                $conn->query("
                    UPDATE medals 
                    SET $type = GREATEST($type - 1, 0),
                        total = gold + silver + bronze
                    WHERE department_id = {$result[$winner]} 
                    AND category = '$category'
                ");
            }
        }
    }

    header("Location: event_results.php");
    exit();
}

// ✏️ Edit Result
$editData = null;
if (isset($_GET['edit_result'])) {
    $id = $_GET['edit_result'];
    $res = $conn->query("SELECT * FROM event_results WHERE id=$id");
    $editData = $res->fetch_assoc();
}

$sports = $conn->query("SELECT id, sport_name, category FROM sports ORDER BY sport_name ASC");
$departments = $conn->query("SELECT id, code FROM departments ORDER BY code ASC");

// Fetch all sports with results
$results = $conn->query("
    SELECT 
        s.id AS sport_id,
        s.sport_name,
        s.category,
        e.id AS result_id,
        e.gold_winner,
        e.silver_winner,
        e.bronze_winner
    FROM sports s
    LEFT JOIN event_results e ON e.sport_id = s.id
    ORDER BY s.category, s.sport_name ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>⚔️ Event Medal Results</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.navbar { background-color: #212529; }
.navbar-brand, .nav-link { color: #fff !important; }
.card { border-radius: 15px; }
th { background-color: #212529 !important; color: white !important; }
.section-title { font-weight: 700; color: #343a40; }
.btn-space { margin-right: 5px; }
</style>
</head>
<body>

<!-- 🔝 NAVBAR -->
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
  <div class="card shadow p-4 mb-5">
    <h3 class="section-title text-center mb-4">⚔️ Event Medal Results</h3>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success text-center">✅ Results updated successfully!</div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>Sport</th>
            <th>Category</th>
            <th>🥇 Gold</th>
            <th>🥈 Silver</th>
            <th>🥉 Bronze</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $deptList = [];
          $departments->data_seek(0);
          while ($d = $departments->fetch_assoc()) {
              $deptList[$d['id']] = $d['code'];
          }

          while ($r = $results->fetch_assoc()): 
          ?>
          <tr>
            <form method="POST">
              <td><strong><?= htmlspecialchars($r['sport_name']); ?></strong></td>
              <td><?= htmlspecialchars($r['category']); ?></td>
              <input type="hidden" name="sport_id" value="<?= $r['sport_id']; ?>">
              <input type="hidden" name="result_id" value="<?= $r['result_id'] ?? 0; ?>">

              <td>
                <select name="gold_winner" class="form-select">
                  <option value="0">—</option>
                  <?php foreach ($deptList as $id => $code): ?>
                    <option value="<?= $id ?>" <?= ($r['gold_winner'] == $id) ? 'selected' : '' ?>><?= $code ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <select name="silver_winner" class="form-select">
                  <option value="0">—</option>
                  <?php foreach ($deptList as $id => $code): ?>
                    <option value="<?= $id ?>" <?= ($r['silver_winner'] == $id) ? 'selected' : '' ?>><?= $code ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <select name="bronze_winner" class="form-select">
                  <option value="0">—</option>
                  <?php foreach ($deptList as $id => $code): ?>
                    <option value="<?= $id ?>" <?= ($r['bronze_winner'] == $id) ? 'selected' : '' ?>><?= $code ?></option>
                  <?php endforeach; ?>
                </select>
              </td>

              <td>
                <?php if ($r['result_id']): ?>
                  <button type="submit" name="quick_update" class="btn btn-primary btn-sm">💾 Update</button>
                  <a href="?delete_result=<?= $r['result_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this event result?');">🗑</a>
                <?php else: ?>
                  <button type="submit" name="save_result" class="btn btn-success btn-sm">➕ Add</button>
                <?php endif; ?>
              </td>
            </form>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
