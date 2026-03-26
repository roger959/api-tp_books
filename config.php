<?php
// Configuration de la base de données
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_NAME', getenv('DB_NAME') ?: 'tp_librairie');

// Tentative de connexion à la base de données MySQL
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Vérification de la connexion
if ($mysqli->connect_error) {
    die("Échec de la connexion : " . $mysqli->connect_error);
}

?>