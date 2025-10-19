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