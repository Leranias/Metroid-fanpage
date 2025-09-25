<?php
// register.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'db_connect.php';

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validierung (in einer echten App wäre hier mehr zu tun)
    if (empty($username) || empty($email) || empty($password)) {
        die("Bitte alle Felder ausfüllen.");
    }

    // Passwort sicher hashen
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepared Statement gegen SQL-Injection
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $email, $password_hash]);

        // Erfolg: Weiterleiten zur Login-Seite
        header("Location: login_form.php?status=success");
        exit();

    } catch (PDOException $e) {
        // prüfung auf existierenden benutzerr
        if ($e->errorInfo[1] == 1062) {
            die("Fehler: Benutzername oder E-Mail bereits vergeben.");
        } else {
            die("Ein Datenbankfehler ist aufgetreten: " . $e->getMessage());
        }
    }
}