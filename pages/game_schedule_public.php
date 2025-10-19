<?php
include './includes/game_sched_public.php';
?>

<!-- 🎯 Hero Section -->
<section class="schedule-hero">
  <div class="container text-center">
    <h1 class="display-4 fw-bold text-light">🗓️ Game Schedule</h1>
    <p class="lead text-light opacity-75">Stay updated with all the latest match schedules and results</p>
  </div>
</section>

<!-- 🏆 GAME SCHEDULE SECTION -->
<section class="container mt-5 mb-5">
  <?php
  $currentDay = null;
  $hasData = false;

  if ($schedules->num_rows > 0):
    while ($row = $schedules->fetch_assoc()):
      $day = intval($row['game_day']);
      if ($currentDay !== $day):
        if ($currentDay !== null) echo "</div>"; // Close previous day section
        echo "<div class='day-section'><div class='day-header'><h3>Day {$day}</h3></div>";
        $currentDay = $day;
        $hasData = true;
      endif;
  ?>

  <div class="schedule-card">
    <div class="schedule-header">
      <span class="category-badge"><?= htmlspecialchars($row['category']); ?></span>
      <h5 class="sport-title"><?= htmlspecialchars($row['sport_name']); ?></h5>
    </div>

    <div class="schedule-body">
      <div class="match-info">
        <div class="team-vs">
          <span class="team-badge"><?= htmlspecialchars($row['team_a']); ?></span>
          <span class="vs-badge">VS</span>
          <span class="team-badge"><?= htmlspecialchars($row['team_b']); ?></span>
        </div>

        <div class="match-details">
          <?php if ($row['game_date'] || $row['game_time']): ?>
            <div class="detail-item">
              <i class="fas fa-calendar-alt"></i>
              <span><?= htmlspecialchars($row['game_date']); ?><?= $row['game_time'] ? ' at ' . htmlspecialchars(substr($row['game_time'], 0, 5)) : ''; ?></span>
            </div>
          <?php endif; ?>

          <?php if ($row['location']): ?>
            <div class="detail-item">
              <i class="fas fa-map-marker-alt"></i>
              <span><?= htmlspecialchars($row['location']); ?></span>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="text-end">
        <?php if ($row['winner']): ?>
          <span class="winner-badge"><i class="fas fa-trophy"></i> <?= htmlspecialchars($row['winner']); ?> Wins!</span>
        <?php else: ?>
          <span class="pending-badge"><i class="fas fa-hourglass-half"></i> Match Pending</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php endwhile; ?>
  </div>
  <?php else: ?>
    <div class="no-schedules text-center">
      <i class="fas fa-calendar-times"></i>
      <h3>No Games Scheduled</h3>
      <p>Check back later for upcoming matches and schedules.</p>
    </div>
  <?php endif; ?>
</section>