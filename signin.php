<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 3600");

require_once 'config.php';

// Récupérer les données du formulaire de login
$email = $_POST['email'] ?? '';
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

// Vérifier si les données ont été correctement reçues
if (empty($email) || empty($mot_de_passe)) {
    http_response_code(400); // Bad request
    echo json_encode(array("success" => false, "message" => "Email et mot de passe requis"));
    exit;
}

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Vérification de la connexion
if ($conn->connect_error) {
    http_response_code(500); // Internal server error
    echo json_encode(array("success" => false, "message" => "Erreur de connexion à la base de données"));
    exit;
}

// Requête pour vérifier les informations d'identification et récupérer le rôle de l'utilisateur
$sql = "SELECT id, role FROM utilisateurs WHERE email='$email' AND mot_de_passe='$mot_de_passe'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // L'utilisateur est authentifié avec succès
    $row = $result->fetch_assoc();
    session_start();
    $_SESSION['utilisateur_id'] = $row['id'];
    $_SESSION['role'] = $row['role'];
    echo json_encode(array("success" => true, "message" => "Connexion réussie", "role" => $row['role']));
} else {
    // Échec de l'authentification
    http_response_code(401); // Unauthorized
    echo json_encode(array("success" => false, "message" => "Email ou mot de passe incorrect"));
}

$conn->close();
?>
