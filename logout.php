<?php
// logout.php

session_start();

// Alle Session-Variablen löschen
$_SESSION = array();

// Das Session-Cookie löschen
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Die Session zerstören
session_destroy();

// Zurück zur Hauptseite weiterleiten
header("Location: index.php");
exit;