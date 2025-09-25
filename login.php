<?php
// login.php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'db_connect.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        die("Bitte alle Felder ausfüllen.");
    }

    $sql = "SELECT id, username, password_hash FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Benutzer gefunden UND Passwort stimmt überein
    if ($user && password_verify($password, $user['password_hash'])) {
        // Login erfolgreich: Session-Variablen setzen
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Weiterleiten zur Hauptseite
        header("Location: index.php");
        exit();
    } else {
        // Login fehlgeschlagen
        header("Location: login_form.php?error=1");
        exit();
    }
}