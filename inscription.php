<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

// Inclure la configuration de la base de données
include_once 'config.php';

// Instancier la base de données
$database = new Database();
$db = $database->getConnection();

// Obtenir les données postées
$data = json_decode(file_get_contents("php://input"));

// Vérifier si les données nécessaires sont fournies
if (!empty($data->id_utilisateur) && !empty($data->id_formation) && !empty($data->date_inscription)) {
    try {
        // Préparer la requête d'insertion
        $query = "INSERT INTO inscriptions (id_utilisateur, id_formation, date_inscription) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);

        // Liaison des paramètres et exécution de la requête
        $stmt->execute([$data->id_utilisateur, $data->id_formation, $data->date_inscription]);

        http_response_code(201); // Réponse : création réussie
        echo json_encode(array("message" => "Inscription réussie."));
    } catch (Exception $e) {
        http_response_code(503); // Réponse : erreur serveur
        echo json_encode(array("message" => "Impossible de créer l'inscription. Erreur : " . $e->getMessage()));
    }
} else {
    http_response_code(400); // Réponse : mauvaise requête
    echo json_encode(array("message" => "Impossible de créer l'inscription. Données incomplètes."));
}
?>
