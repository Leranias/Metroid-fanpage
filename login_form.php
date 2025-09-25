<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <section class="content-box">
            <h3>Login</h3>
            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <p style="color: var(--text-scan-green);">Registrierung erfolgreich! Bitte einloggen.</p>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <p style="color: red;">E-Mail oder Passwort ist falsch.</p>
            <?php endif; ?>
            
            <form action="login.php" method="post">
                <label for="email">E-Mail:</label><br>
                <input type="email" id="email" name="email" required><br><br>
                
                <label for="password">Passwort:</label><br>
                <input type="password" id="password" name="password" required><br><br>
                
                <button type="submit" class="btn-game">Anmelden</button>
            </form>
        </section>
    </div>
</body>
</html>