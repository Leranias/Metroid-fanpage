<!DOCTYPE php>
<php lang="de">
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

    <?php include 'header.php'; ?>

    
        <main class="main-content">
            <section id="mission" class="content-box">
                <h3>Missions-Briefing: Notruf von der Orpheon</h3>
                <p>Unsere Mission beginnt mit dem Abfangen eines Notrufs der Raumfregatte 'Orpheon'. An Bord entdecken wir die grausamen Experimente der Weltraumpiraten mit genmanipulierten Lebensformen. Ein Notfall zwingt uns zur Flucht, bei der wir auf dem nahegelegenen Planeten Tallon IV bruchlanden und dabei einen Großteil unserer Ausrüstung verlieren. Von hier an sind wir auf uns allein gestellt.</p>
            </section>

            <section id="kernelemente" class="content-box">
                <h3>Kernelemente: Isolation & Erkundung</h3>
                <ul>
                    <li><b>Atmosphäre:</b> Die dichte, isolierte Stimmung auf Tallon IV ist das Markenzeichen des Spiels.</li>
                    <li><b>Scan-Visor:</b> Anstatt nur zu kämpfen, scannen wir die Umgebung, um die Geschichte der Chozo und die Schwächen unserer Gegner aufzudecken.</li>
                    <li><b>Verbundene Welt:</b> Die Areale sind nahtlos miteinander verbunden und werden durch neue Fähigkeiten nach und nach zugänglich (Backtracking).</li>
                </ul>
            </section>
        </main>

    </div> 
    <?php include 'footer.php'; ?>

    <map name="covermap">
        <area shape="rect" coords="76,159,215,400" href="images/MetroidPrimeLogo.jpg" target="_blank" alt="Metroid logo">
        <area shape="rect" coords="32,27,268,145" href="images/samus.jpg" target="_blank" alt="Bild von Samus Aran, einer Frau im Anzug">
    </map>

</body>
</php>