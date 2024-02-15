<?php
header("Access-Control-Allow-Origin: *");

// Autoriser les méthodes de requête HTTP spécifiques
header("Access-Control-Allow-Methods: GET, POST, DELETE");

// Autoriser certains en-têtes HTTP
header("Access-Control-Allow-Headers: Content-Type");

// Spécifier la durée de validité des en-têtes CORS pré-flush
header("Access-Control-Max-Age: 3600");


// Inclure le fichier de configuration
require_once 'config.php';

// Exécuter une requête SQL de test
$resultat = $connexion->query("SELECT 'Connexion à la base de données réussie!' AS message");

// Vérifier si la requête a réussi
if ($resultat) {
    // Récupérer le résultat de la requête
    $row = $resultat->fetch_assoc();
    echo $row['message'];
} else {
    echo "Une erreur s'est produite lors de l'exécution de la requête : " . $connexion->error;
}

// Fermer la connexion à la base de données
$connexion->close();
?>
