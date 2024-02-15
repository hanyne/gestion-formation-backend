<?php

// Informations de connexion à la base de données
define('DB_HOST', 'localhost'); // Nom d'hôte de la base de données (dans la plupart des cas, localhost)
define('DB_USER', 'root'); // Nom d'utilisateur de la base de données
define('DB_PASS', ''); // Mot de passe de la base de données
define('DB_NAME', 'formation'); // Nom de la base de données

// Tentative de connexion à la base de données
$connexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Vérification de la connexion
if ($connexion->connect_error) {
    die("La connexion à la base de données a échoué : " . $connexion->connect_error);
}
?>
