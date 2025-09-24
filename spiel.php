<?php
session_start();

// POST-Redirect-GET Pattern: Nach POST immer Redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Spiel zurücksetzen
    if (isset($_POST['reset'])) {
        $_SESSION['score'] = 0;
        $_SESSION['level'] = 1;
        $_SESSION['crystals_found'] = 0;
        $_SESSION['crystals_needed'] = 5;
        $_SESSION['time_left'] = 30;
        $_SESSION['game_over'] = false;
        $_SESSION['game_won'] = false;
        $_SESSION['last_update'] = time();
        $_SESSION['game_initialized'] = true;
        
        // Kristall-Positionen zufällig generieren
        $_SESSION['crystals'] = [];
        for ($i = 0; $i < $_SESSION['crystals_needed']; $i++) {
            $_SESSION['crystals'][] = [
                'x' => rand(50, 750),
                'y' => rand(150, 400),
                'found' => false,
                'id' => $i
            ];
        }
        
        // Redirect nach Reset
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Kristall sammeln (AJAX Request)
    if (isset($_POST['crystal_id']) && !$_SESSION['game_over'] && !$_SESSION['game_won']) {
        $crystal_id = (int)$_POST['crystal_id'];
        
        if (isset($_SESSION['crystals'][$crystal_id]) && !$_SESSION['crystals'][$crystal_id]['found']) {
            $_SESSION['crystals'][$crystal_id]['found'] = true;
            $_SESSION['crystals_found']++;
            $_SESSION['score'] += $_SESSION['level'] * 100;
            
            // Level komplett?
            if ($_SESSION['crystals_found'] >= $_SESSION['crystals_needed']) {
                $_SESSION['level']++;
                $_SESSION['crystals_found'] = 0;
                $_SESSION['crystals_needed'] += 2;
                $_SESSION['time_left'] += 20;
                $_SESSION['score'] += $_SESSION['level'] * 500; // Bonus
                
                // Neue Kristalle generieren
                $_SESSION['crystals'] = [];
                for ($i = 0; $i < $_SESSION['crystals_needed']; $i++) {
                    $_SESSION['crystals'][] = [
                        'x' => rand(50, 750),
                        'y' => rand(150, 400),
                        'found' => false,
                        'id' => $i
                    ];
                }
                
                // Gewonnen bei Level 5
                if ($_SESSION['level'] > 5) {
                    $_SESSION['game_won'] = true;
                }
            }
        }
        
        // JSON Response für AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'score' => $_SESSION['score'],
                'level' => $_SESSION['level'],
                'crystals_found' => $_SESSION['crystals_found'],
                'crystals_needed' => $_SESSION['crystals_needed'],
                'time_left' => $_SESSION['time_left'],
                'game_over' => $_SESSION['game_over'],
                'game_won' => $_SESSION['game_won']
            ]);
            exit;
        }
        
        // Redirect bei normalem POST (Fallback)
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Spiel initialisieren (nur bei GET und wenn nicht initialisiert)
if (!isset($_SESSION['game_initialized'])) {
    $_SESSION['score'] = 0;
    $_SESSION['level'] = 1;
    $_SESSION['crystals_found'] = 0;
    $_SESSION['crystals_needed'] = 5;
    $_SESSION['time_left'] = 30;
    $_SESSION['game_over'] = false;
    $_SESSION['game_won'] = false;
    $_SESSION['last_update'] = time();
    $_SESSION['game_initialized'] = true;
    
    // Kristall-Positionen zufällig generieren
    $_SESSION['crystals'] = [];
    for ($i = 0; $i < $_SESSION['crystals_needed']; $i++) {
        $_SESSION['crystals'][] = [
            'x' => rand(50, 750),
            'y' => rand(150, 400),
            'found' => false,
            'id' => $i
        ];
    }
}

// Timer Update (nur bei GET)
if (!$_SESSION['game_over'] && !$_SESSION['game_won']) {
    $time_passed = time() - $_SESSION['last_update'];
    $_SESSION['time_left'] = max(0, $_SESSION['time_left'] - $time_passed);
    $_SESSION['last_update'] = time();
    
    if ($_SESSION['time_left'] <= 0) {
        $_SESSION['game_over'] = true;
    }
}
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
                
                <div class="game-container">
                    <div class="scanner-line"></div>
                    
                    <div class="game-hud">
                        <div>
                            <span style="color: var(--primary-orange);">PUNKTE:</span> 
                            <span style="color: var(--text-scan-green);"><?php echo number_format($_SESSION['score']); ?></span>
                        </div>
                        <div>
                            <span style="color: var(--primary-orange);">LEVEL:</span> 
                            <span style="color: var(--primary-cyan);"><?php echo $_SESSION['level']; ?></span>
                        </div>
                        <div>
                            <span style="color: var(--primary-orange);">KRISTALLE:</span> 
                            <span style="color: var(--text-scan-green);"><?php echo $_SESSION['crystals_found']; ?>/<?php echo $_SESSION['crystals_needed']; ?></span>
                            <div class="progress-bar" style="margin-top: 5px;">
                                <div class="progress-fill" style="width: <?php echo ($_SESSION['crystals_found'] / $_SESSION['crystals_needed']) * 100; ?>%;"></div>
                            </div>
                        </div>
                        <div>
                            <span style="color: var(--primary-orange);">ZEIT:</span> 
                            <span class="<?php echo $_SESSION['time_left'] <= 10 ? 'time-warning' : ''; ?>" style="color: var(--primary-cyan);">
                                <?php echo $_SESSION['time_left']; ?>s
                            </span>
                        </div>
                    </div>
                    
                    <?php if (!$_SESSION['game_over'] && !$_SESSION['game_won']): ?>
                        <?php foreach ($_SESSION['crystals'] as $crystal): ?>
                            <?php if (!$crystal['found']): ?>
                                <form method="post" style="position: absolute; left: <?php echo $crystal['x']; ?>px; top: <?php echo $crystal['y']; ?>px;">
                                    <input type="hidden" name="crystal_id" value="<?php echo $crystal['id']; ?>">
                                    <button type="submit" class="crystal" style="background: none; border: none; padding: 0;">
                                        <div style="width: 30px; height: 30px; background: radial-gradient(circle, var(--primary-cyan), var(--primary-orange)); border-radius: 50%; border: 2px solid var(--border-gold); box-shadow: 0 0 15px var(--shadow-glow);"></div>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['game_over']): ?>
                        <div class="game-message">
                            <h3>MISSION FEHLGESCHLAGEN</h3>
                            <p style="color: var(--text-light);">Die Zeit ist abgelaufen! Die Phazon-Signatur ist verschwunden.</p>
                            <p style="color: var(--text-scan-green);">Erreichte Punkte: <strong><?php echo number_format($_SESSION['score']); ?></strong></p>
                            <p style="color: var(--text-scan-green);">Höchstes Level: <strong><?php echo $_SESSION['level']; ?></strong></p>
                            <div class="game-controls">
                                <form method="post" style="display: inline;">
                                    <button type="submit" name="reset" class="btn-game">Neue Mission</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['game_won']): ?>
                        <div class="game-message">
                            <h3>MISSION ERFOLGREICH!</h3>
                            <p style="color: var(--text-light);">Alle Phazon-Kristalle wurden erfolgreich geborgen!</p>
                            <p style="color: var(--text-scan-green);">Endpunktzahl: <strong><?php echo number_format($_SESSION['score']); ?></strong></p>
                            <p style="color: var(--primary-orange);">Sie haben Tallon IV von der Phazon-Bedrohung befreit!</p>
                            <div class="game-controls">
                                <form method="post" style="display: inline;">
                                    <button type="submit" name="reset" class="btn-game">Neue Mission</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 30px; text-align: center;">
                    <form method="post" style="display: inline;">
                        <button type="submit" name="reset" class="btn-game">Mission Neustarten</button>
                    </form>
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

    <script>
        let gameRunning = <?php echo (!$_SESSION['game_over'] && !$_SESSION['game_won']) ? 'true' : 'false'; ?>;
        let currentScore = <?php echo $_SESSION['score']; ?>;
        let currentLevel = <?php echo $_SESSION['level']; ?>;
        let currentCrystals = <?php echo $_SESSION['crystals_found']; ?>;
        let neededCrystals = <?php echo $_SESSION['crystals_needed']; ?>;
        let timeLeft = <?php echo $_SESSION['time_left']; ?>;
        
        // Timer Update ohne Page Reload
        function updateTimer() {
            if (timeLeft > 0 && gameRunning) {
                timeLeft--;
                const timerElement = document.querySelector('.game-hud > div:last-child span:last-child');
                if (timerElement) {
                    timerElement.textContent = timeLeft + 's';
                    if (timeLeft <= 10) {
                        timerElement.classList.add('time-warning');
                    }
                }
                
                // Game Over Check
                if (timeLeft <= 0) {
                    gameRunning = false;
                    setTimeout(() => window.location.reload(), 1000);
                }
            }
        }
        
        // Timer starten
        if (gameRunning && timeLeft > 0) {
            setInterval(updateTimer, 1000);
        }
        
        // AJAX Kristall sammeln
        async function collectCrystal(crystalId, buttonElement) {
            if (!gameRunning) return;
            
            // Animation starten
            buttonElement.classList.add('collecting');
            
            try {
                // AJAX Request mit korrekten Headers
                const formData = new FormData();
                formData.append('crystal_id', crystalId);
                
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    // Kristall verschwinden lassen
                    setTimeout(() => {
                        buttonElement.parentElement.style.display = 'none';
                        
                        // HUD sofort aktualisieren
                        updateHUDWithData(data);
                        
                        // Prüfen ob Level gewonnen oder Game Over
                        if (data.game_won || data.game_over) {
                            setTimeout(() => {
                                window.location.href = window.location.href; // GET Request
                            }, 1500);
                        }
                    }, 500);
                }
            } catch (error) {
                console.log('Fehler beim Sammeln des Kristalls');
                // Fallback: sauberer GET Request
                setTimeout(() => {
                    window.location.href = window.location.href;
                }, 500);
            }
        }
        
        // HUD Update ohne Page Reload
        function updateHUDWithData(data) {
    // Prüfen, ob ein Level-Up stattgefunden hat
    const levelUp = data.level > currentLevel;

    // HUD-Elemente aktualisieren (wie bisher)
    const scoreElement = document.querySelector('.game-hud > div:first-child span:last-child');
    if (scoreElement) scoreElement.textContent = new Intl.NumberFormat().format(data.score);
    
    const levelElement = document.querySelector('.game-hud > div:nth-child(2) span:last-child');
    if (levelElement) levelElement.textContent = data.level;
    
    const crystalsElement = document.querySelector('.game-hud > div:nth-child(3) span:last-child');
    if (crystalsElement) crystalsElement.textContent = data.crystals_found + '/' + data.crystals_needed;
    
    const progressBar = document.querySelector('.progress-fill');
    if (progressBar) {
        const percentage = (data.crystals_found / data.crystals_needed) * 100;
        progressBar.style.width = percentage + '%';
    }
    
    // Globale JS-Variablen aktualisieren
    currentScore = data.score;
    currentLevel = data.level;
    currentCrystals = data.crystals_found;
    neededCrystals = data.crystals_needed;
    timeLeft = data.time_left;
    gameRunning = !data.game_over && !data.game_won;

    // Wenn ein Level-Up erkannt wurde, Seite neu laden, um neue Kristalle zu zeichnen
    if (levelUp) {
        // Kurze Verzögerung, damit der Spieler den Level-Bonus noch sieht
        setTimeout(() => {
            window.location.reload();
        }, 800); // 800ms Verzögerung
    }
}
        
        // Scroll-Position wiederherstellen (nur wenn nötig)
        window.addEventListener('load', function() {
            const scrollPos = sessionStorage.getItem('scrollPos');
            if (scrollPos) {
                setTimeout(() => {
                    window.scrollTo(0, parseInt(scrollPos));
                    sessionStorage.removeItem('scrollPos');
                }, 100);
            }
        });
        
        // Kristall-Klick Event Listener
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.crystal').forEach(function(crystal) {
                crystal.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!gameRunning) return;
                    
                    const form = this.closest('form');
                    const crystalId = form.querySelector('input[name="crystal_id"]').value;
                    collectCrystal(crystalId, this);
                });
            });
        });
        
        // Warnung bei wenig Zeit
        <?php if ($_SESSION['time_left'] <= 10 && $_SESSION['time_left'] > 0 && !$_SESSION['game_over'] && !$_SESSION['game_won']): ?>
        console.log('WARNUNG: Wenig Zeit verbleibt!');
        <?php endif; ?>
        
        // Verhindere Formular-Submit
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(function(form) {
                if (form.querySelector('input[name="crystal_id"]')) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                    });
                }
            });
        });
    </script>

</body>
</html>