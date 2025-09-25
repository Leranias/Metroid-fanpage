<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrierung</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <section class="content-box">
            <h3>Neuen Account anlegen</h3>
            <form action="register.php" method="post">
                <label for="username">Benutzername:</label><br>
                <input type="text" id="username" name="username" required><br><br>
                
                <label for="email">E-Mail:</label><br>
                <input type="email" id="email" name="email" required><br><br>
                
                <label for="password">Passwort:</label><br>
                <input type="password" id="password" name="password" required><br><br>
                
                <button type="submit" class="btn-game">Registrieren</button>
            </form>
        </section>
    </div>
</body>
</html>