<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: index.php");
    exit();
}

include 'db_connect.php';

// ----------------------------
// 🥇 MEDAL CRUD
// ----------------------------

// Handle Add Medal Record
if (isset($_POST['add_medal'])) {
    $department_id = $_POST['department_id'];
    $gold = $_POST['gold'];
    $silver = $_POST['silver'];
    $bronze = $_POST['bronze'];
    $total = (int)$gold + (int)$silver + (int)$bronze;

    $check = $conn->prepare("SELECT id FROM medals WHERE department_id=? AND category='Overall'");
    $check->bind_param("i", $department_id);
    $check->execute();
    $result_check = $check->get_result();

    if ($result_check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE medals SET gold=?, silver=?, bronze=?, total=? WHERE department_id=? AND category='Overall'");
        $stmt->bind_param("iiiii", $gold, $silver, $bronze, $total, $department_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO medals (department_id, category, gold, silver, bronze, total) VALUES (?, 'Overall', ?, ?, ?, ?)");
        $stmt->bind_param("iiiii", $department_id, $gold, $silver, $bronze, $total);
    }
    $stmt->execute();
    header("Location: dashboard.php#medals");
    exit();
}

// Handle Update Medal
if (isset($_POST['update_medal'])) {
    $id = $_POST['id'];
    $gold = $_POST['gold'];
    $silver = $_POST['silver'];
    $bronze = $_POST['bronze'];
    $total = (int)$gold + (int)$silver + (int)$bronze;

    $stmt = $conn->prepare("UPDATE medals SET gold=?, silver=?, bronze=?, total=? WHERE id=?");
    $stmt->bind_param("iiiii", $gold, $silver, $bronze, $total, $id);
    $stmt->execute();
    header("Location: dashboard.php#medals");
    exit();
}

// Handle Delete Medal
if (isset($_GET['delete_medal'])) {
    $id = $_GET['delete_medal'];
    $stmt = $conn->prepare("DELETE FROM medals WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php#medals");
    exit();
}

// ✅ Fetch all departments with overall totals (aggregated across all categories)
$departments = $conn->query("
    SELECT 
        d.id,
        d.code,
        d.mascot,
        IFNULL(SUM(m.gold), 0) AS gold,
        IFNULL(SUM(m.silver), 0) AS silver,
        IFNULL(SUM(m.bronze), 0) AS bronze,
        IFNULL(SUM(m.total), 0) AS total
    FROM departments d
    LEFT JOIN medals m ON d.id = m.department_id
    GROUP BY d.id, d.code, d.mascot
    ORDER BY total DESC, gold DESC, silver DESC, bronze DESC
");

// ----------------------------
// 🏐 SPORTS CRUD WITH CATEGORY
// ----------------------------

// Ensure sports table exists
$conn->query("
CREATE TABLE IF NOT EXISTS sports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sport_name VARCHAR(100) NOT NULL,
    category ENUM('Men','Women','Mixed') NOT NULL DEFAULT 'Men',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

// Preload sports if empty
$count = $conn->query("SELECT COUNT(*) AS cnt FROM sports")->fetch_assoc()['cnt'];
if ($count == 0) {
    $conn->multi_query("
        INSERT INTO sports (sport_name, category) VALUES
        ('Arnis', 'Men'),
        ('Arnis', 'Women'),
        ('Sepak Takraw', 'Men'),
        ('Taekwondo', 'Men'),
        ('Taekwondo', 'Women'),
        ('Futsal', 'Women'),
        ('Chess', 'Men'),
        ('Chess', 'Women'),
        ('Basketball', 'Men'),
        ('Basketball', 'Women'),
        ('Badminton', 'Men'),
        ('Badminton', 'Women'),
        ('Athletics - Runs, Throws, and Jumps', 'Men'),
        ('Athletics - Runs, Throws, and Jumps', 'Women'),
        ('Volleyball', 'Men'),
        ('Volleyball', 'Women'),
        ('Beach Volleyball', 'Men'),
        ('Beach Volleyball', 'Women'),
        ('Softball', 'Women'),
        ('Baseball', 'Men'),
        ('Football', 'Men'),
        ('Esports - MLBB and CODM', 'Mixed'),
        ('Archery', 'Men'),
        ('Archery', 'Women'),
        ('Swimming', 'Men'),
        ('Swimming', 'Women');
    ");
    while ($conn->more_results() && $conn->next_result()) {;}
}

// Add Sport
if (isset($_POST['add_sport'])) {
    $sport_name = trim($_POST['sport_name']);
    $category = $_POST['category'];
    if ($sport_name !== "" && $category !== "") {
        $stmt = $conn->prepare("INSERT INTO sports (sport_name, category) VALUES (?, ?)");
        $stmt->bind_param("ss", $sport_name, $category);
        $stmt->execute();
    }
    header("Location: dashboard.php#sports");
    exit();
}

// Update Sport
if (isset($_POST['update_sport'])) {
    $id = $_POST['id'];
    $sport_name = trim($_POST['sport_name']);
    $category = $_POST['category'];
    $stmt = $conn->prepare("UPDATE sports SET sport_name=?, category=? WHERE id=?");
    $stmt->bind_param("ssi", $sport_name, $category, $id);
    $stmt->execute();
    header("Location: dashboard.php#sports");
    exit();
}

// Delete Sport
if (isset($_GET['delete_sport'])) {
    $id = $_GET['delete_sport'];
    $stmt = $conn->prepare("DELETE FROM sports WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php#sports");
    exit();
}

// Fetch Sports
$sports = $conn->query("SELECT * FROM sports ORDER BY sport_name ASC, category ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Intramural System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.navbar { background-color: #212529; }
.navbar-brand, .nav-link { color: #fff !important; }
.card { border-radius: 15px; }
.table { border-radius: 10px; overflow: hidden; }
th { background-color: #212529 !important; color: white !important; }
.rank-1 { background-color: #fff3cd !important; }
.rank-2 { background-color: #e2e3e5 !important; }
.rank-3 { background-color: #f8d7da !important; }
.btn-sm { border-radius: 8px; transition: transform 0.1s ease-in-out; }
.btn-sm:hover { transform: scale(1.05); }
.section-title { font-weight: 700; color: #343a40; }
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

  <!-- 🥇 MEDAL TABULATION SECTION -->
  <div id="medals" class="card shadow p-4 mb-5">
    <h3 class="section-title mb-4">🥇 Overall Medal Tabulation</h3>

    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>🏅 Rank</th>
            <th>Department</th>
            <th>Mascot</th>
            <th>Gold 🥇</th>
            <th>Silver 🥈</th>
            <th>Bronze 🥉</th>
            <th>Total 🧮</th>
          </tr>
        </thead>
        <tbody>
          <?php $rank = 1; while ($row = $departments->fetch_assoc()):
            $rankClass = ($rank == 1) ? "rank-1" : (($rank == 2) ? "rank-2" : (($rank == 3) ? "rank-3" : ""));
          ?>
          <tr class="<?= $rankClass ?>">
            <td><strong><?= $rank++; ?></strong></td>
            <td><strong><?= htmlspecialchars($row['code']); ?></strong></td>
            <td><?= htmlspecialchars($row['mascot']); ?></td>
            <td><strong><?= $row['gold']; ?></strong></td>
            <td><strong><?= $row['silver']; ?></strong></td>
            <td><strong><?= $row['bronze']; ?></strong></td>
            <td><strong><?= $row['total']; ?></strong></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- 🏐 SPORTS MANAGEMENT SECTION -->
  <div id="sports" class="card shadow p-4 mb-5">
    <h3 class="section-title mb-4">🏐 Sports Management</h3>
    <form method="POST" class="row g-3 mb-4 border rounded p-3 bg-light">
      <div class="col-md-5">
        <input type="text" name="sport_name" class="form-control" placeholder="Enter Sport Name" required>
      </div>
      <div class="col-md-3">
        <select name="category" class="form-select" required>
          <option value="">Select Category</option>
          <option value="Men">Men</option>
          <option value="Women">Women</option>
          <option value="Mixed">Mixed</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" name="add_sport" class="btn btn-success w-100">➕ Add Sport</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Sport Name</th>
            <th>Category</th>
            <th>Actions ⚙️</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($sport = $sports->fetch_assoc()): ?>
          <tr>
            <form method="POST">
              <td><?= $no++; ?></td>
              <td><input type="text" name="sport_name" value="<?= htmlspecialchars($sport['sport_name']); ?>" class="form-control text-center"></td>
              <td>
                <select name="category" class="form-select text-center">
                  <option value="Men" <?= $sport['category'] == 'Men' ? 'selected' : '' ?>>Men</option>
                  <option value="Women" <?= $sport['category'] == 'Women' ? 'selected' : '' ?>>Women</option>
                  <option value="Mixed" <?= $sport['category'] == 'Mixed' ? 'selected' : '' ?>>Mixed</option>
                </select>
              </td>
              <td>
                <div class="d-flex justify-content-center gap-2">
                  <input type="hidden" name="id" value="<?= $sport['id']; ?>">
                  <button type="submit" name="update_sport" class="btn btn-primary btn-sm px-3">💾 Update</button>
                  <a href="?delete_sport=<?= $sport['id']; ?>" class="btn btn-danger btn-sm px-3" onclick="return confirm('Delete this sport?');">🗑 Delete</a>
                </div>
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
