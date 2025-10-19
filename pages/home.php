<?php
include './includes/home_backend.php';

?>

<!-- 🏆 HERO SECTION -->
<section class="hero-section d-flex align-items-center text-center text-white position-relative vh-100">
    <div class="container">
        <div class="hero-content mx-auto text-dark">
            <div class="hero-logos d-flex justify-content-center gap-2 mb-4">
                <img src="./assets/images/nwssu-logo.png" alt="NwSSU Logo" class="hero-logo mb-3">
                <img src="./assets/images/ccis-logo.png" alt="CCIS Logo" class="hero-logo mb-3">
                <img src="./assets/images/subLogoLight.png" alt="WDC Logo" class="hero-logo mb-3">
            </div>
            <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown">Intramurals 2025</h1>
            <p class="lead mb-4 animate__animated animate__fadeInUp">Celebrating sportsmanship, teamwork, and victory across every department!</p>
            <a href="#medals" class="btn btn-light btn-lg px-4 py-2 rounded-pill fw-semibold shadow-sm animate__animated animate__fadeInUp">View Medal Standings</a>
        </div>
    </div>
</section>

<!-- 🥇 MEDAL TABULATION -->
<section class="py-5" style="background: linear-gradient(180deg, #f0f9ff, #ffffff); border-radius: 30px 30px 0px 0px;">

    <div id="medals" class="container mt-5">
        <h2 class="section-title text-center mb-4">🥇 Overall Medal Standings</h2>
        <div class="row">
            <?php
            $rank = 1;
            while ($row = $medals->fetch_assoc()):
                $rankClass = ($rank == 1) ? "rank-1" : (($rank == 2) ? "rank-2" : (($rank == 3) ? "rank-3" : ""));
            ?>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-4">
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
    </div>
</section>


<!-- ⚔️ EVENT RESULTS -->
<section class="py-5" style="background: linear-gradient(180deg, #f0f9ff, #ffffff); border-radius: 0px 0px 0px 0px;">
    <div id="events" class="container mb-5">
        <h2 class="section-title text-center mb-4">⚔️ Event Medal Results</h2>
        <div class="row">
            <?php while ($r = $results->fetch_assoc()): ?>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
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
                                        <?php if ($r['gold_code']): ?>
                                            <?= htmlspecialchars($r['gold_code']); ?>
                                        <?php else: ?>
                                            <span class="no-winner">--</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="winner-item">
                                    <div class="winner-label">🥈 Silver</div>
                                    <div class="winner-name">
                                        <?php if ($r['silver_code']): ?>
                                            <?= htmlspecialchars($r['silver_code']); ?>
                                        <?php else: ?>
                                            <span class="no-winner">--</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="winner-item">
                                    <div class="winner-label">🥉 Bronze</div>
                                    <div class="winner-name">
                                        <?php if ($r['bronze_code']): ?>
                                            <?= htmlspecialchars($r['bronze_code']); ?>
                                        <?php else: ?>
                                            <span class="no-winner">--</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>