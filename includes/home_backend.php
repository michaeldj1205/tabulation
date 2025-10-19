<?php
// Include database connection for home page
include "db_connect.php";

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