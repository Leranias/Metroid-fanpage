<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>METROID PRIME - The Tallon IV Archives</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <img src="images/Mp2gunship.jpg" class="floating-object type-1" alt="Samus Arans Raumschiff">
    <img src="images/asteroid1.jpg" class="floating-object type-2" alt="asteroid">
    <img src="images/asteroid2.jpg" class="floating-object type-3" alt="asteroid">
    <img src="images/asteroid3.jpg" class="floating-object type-4" alt="asteroid">
    <img src="images/schiff.jpg" class="floating-object type-5" alt="spaceship">

    <header class="header">
        <img src="images/samusbattle.gif" alt="Animierte Samus Aran" class="header-logo">
        <div>
            <h1>THE TALLON IV ARCHIVES</h1>
            <h2>Eine Metroid Prime Fan-Datenbank</h2>
        </div>
    </header>
    <nav class="nav-sticky">
        <div class="container">
            <ul>
                <li><a href="index.php">Missions-Briefing</a></li>
                <li><a href="index.php#kernelemente">Kernelemente</a></li>
                <li><a href="Youtube_videos.php">youtube</a></li>
                <li><a href="visoren.php">Visoren</a></li>
                <li><a href="waffen.php">Waffen</a></li>
                <li><a href="bosse.php">Gefahrenanalyse</a></li>
                <li><a href="spiel.php">Kristall-Jagd</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="login_form.php">Login</a></li>
                    <li><a href="register_form.php">Registrieren</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
        <div class="ticker">
        <p class="ticker-text">*** Willkommen, Kopfgeldjäger! +++ Scanvorgang abgeschlossen: Planet Tallon IV +++ WARNUNG: Hohe Phazon-Konzentration registriert! ***</p>
    </div>
    <div class="container">
    <main class="main-content">