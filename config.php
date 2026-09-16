<?php

$host = 'localhost';
$dbname = 'reservation_rdv_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Horaires d'ouverture (jours fermés : 0 = dimanche, 6 = samedi).
define('HEURE_OUVERTURE', '09:00');
define('HEURE_FERMETURE', '18:00');
define('PAUSE_DEBUT', '12:00');
define('PAUSE_FIN', '14:00');
define('JOURS_FERMES', [0, 6]);
define('GRANULARITE_MINUTES', 15); // pas entre deux créneaux proposés
