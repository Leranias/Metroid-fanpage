<?php

// spiel.php - GESICHERTER BEREICH

session_start();

// Prüfen, ob der User eingeloggt ist. Wenn nicht, umleiten.
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php?redirect=spiel');
    exit;
}

// spiel.php
require_once 'game_logic.php';
$game_state = get_game_state();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>METROID PRIME - Phazon Crystal Hunt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <img src="images/Mp2gunship.jpg" class="floating-object type-1" alt="Samus Arans Raumschiff">
    <img src="images/asteroid1.jpg" class="floating-object type-2" alt="asteroid">
    <img src="images/asteroid2.jpg" class="floating-object type-3" alt="asteroid">
    <img src="images/asteroid3.jpg" class="floating-object type-4" alt="asteroid">
    <img src="images/schiff.jpg" class="floating-object type-5" alt="spaceship">

    <?php include 'header.php'; ?>

    <div class="container">
        <main class="main-content">
            <section class="content-box">
                <h3>Phazon Crystal Hunt - Missionsprotokoll</h3>
                <p>Kopfgeldjägerin, Ihre Mission ist es, versteckte Phazon-Kristalle auf Tallon IV zu lokalisieren und zu sammeln. Verwenden Sie Ihren Scan-Visor, um die Energiesignaturen aufzuspüren, bevor die Zeit abläuft!</p>
                
                <div id="game-container" class="game-container">
                    <div class="scanner-line"></div>
                    
                    <div class="game-hud">
                        <div>
                            <span style="color: var(--primary-orange);">PUNKTE:</span> 
                            <span id="score-display" style="color: var(--text-scan-green);"><?php echo number_format($game_state['score']); ?></span>
                        </div>
                        <div>
                            <span style="color: var(--primary-orange);">LEVEL:</span> 
                            <span id="level-display" style="color: var(--primary-cyan);"><?php echo $game_state['level']; ?></span>
                        </div>
                        <div>
                            <span style="color: var(--primary-orange);">KRISTALLE:</span> 
                            <span id="crystals-display" style="color: var(--text-scan-green);"><?php echo $game_state['crystals_found'] . '/' . $game_state['crystals_needed']; ?></span>
                            <div class="progress-bar" style="margin-top: 5px;">
                                <div id="progress-fill" class="progress-fill" style="width: <?php echo ($game_state['crystals_found'] / $game_state['crystals_needed']) * 100; ?>%;"></div>
                            </div>
                        </div>
                        <div>
                            <span style="color: var(--primary-orange);">ZEIT:</span> 
                            <span id="time-display" class="<?php echo $game_state['time_left'] <= 10 ? 'time-warning' : ''; ?>" style="color: var(--primary-cyan);">
                                <?php echo $game_state['time_left']; ?>s
                            </span>
                        </div>
                    </div>
                    
                    <div id="crystal-area">
                        <?php if (!$game_state['game_over'] && !$game_state['game_won']): ?>
                            <?php foreach ($game_state['crystals'] as $crystal): ?>
                                <?php if (!$crystal['found']): ?>
                                    <button class="crystal" data-crystal-id="<?php echo $crystal['id']; ?>" style="position: absolute; left: <?php echo $crystal['x']; ?>px; top: <?php echo $crystal['y']; ?>px; background: none; border: none; padding: 0;">
                                        <div style="width: 30px; height: 30px; background: radial-gradient(circle, var(--primary-cyan), var(--primary-orange)); border-radius: 50%; border: 2px solid var(--border-gold); box-shadow: 0 0 15px var(--shadow-glow);"></div>
                                    </button>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div id="game-over-message" class="game-message" style="display: <?php echo $game_state['game_over'] ? 'block' : 'none'; ?>;">
                        <h3>MISSION FEHLGESCHLAGEN</h3>
                        <p style="color: var(--text-light);">Die Zeit ist abgelaufen! Die Phazon-Signatur ist verschwunden.</p>
                        <p style="color: var(--text-scan-green);">Erreichte Punkte: <strong id="final-score-lost"><?php echo number_format($game_state['score']); ?></strong></p>
                        <p style="color: var(--text-scan-green);">Höchstes Level: <strong id="final-level-lost"><?php echo $game_state['level']; ?></strong></p>
                        <div class="game-controls">
                            <button id="reset-button-lost" class="btn-game">Neue Mission</button>
                        </div>
                    </div>
                    
                    <div id="game-won-message" class="game-message" style="display: <?php echo $game_state['game_won'] ? 'block' : 'none'; ?>;">
                        <h3>MISSION ERFOLGREICH!</h3>
                        <p style="color: var(--text-light);">Alle Phazon-Kristalle wurden erfolgreich geborgen!</p>
                        <p style="color: var(--text-scan-green);">Endpunktzahl: <strong id="final-score-won"><?php echo number_format($game_state['score']); ?></strong></p>
                        <p style="color: var(--primary-orange);">Sie haben Tallon IV von der Phazon-Bedrohung befreit!</p>
                        <div class="game-controls">
                            <button id="reset-button-won" class="btn-game">Neue Mission</button>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 30px; text-align: center;">
                    <button id="reset-button-main" class="btn-game">Mission Neustarten</button>
                </div>
                
                <div style="margin-top: 20px; padding: 20px; background: rgba(6, 6, 42, 0.8); border-radius: 5px;">
                    <h4 style="color: var(--primary-orange); text-align: center;">Missionsanweisungen</h4>
                    <ul style="color: var(--text-light); line-height: 1.6;">
                        <li><strong>Ziel:</strong> Sammeln Sie alle Phazon-Kristalle, bevor die Zeit abläuft</li>
                        <li><strong>Steuerung:</strong> Klicken Sie auf die leuchtenden Kristalle</li>
                        <li><strong>Progression:</strong> Jedes Level bringt mehr Kristalle und Bonuszeit</li>
                        <li><strong>Punkte:</strong> Kristalle = Level × 100, Level-Bonus = Level × 500</li>
                        <li><strong>Warnung:</strong> Hohe Phazon-Konzentration verkürzt die verfügbare Scanzeit!</li>
                    </ul>
                </div>
            </section>
        </main>
    </div>

    <?php include 'footer.php'; ?>

    <script src="game.js" defer></script>
</body>
</html>