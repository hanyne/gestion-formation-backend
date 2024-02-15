<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 3600");

require_once 'config.php';

// Endpoint pour récupérer la liste des participants
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Récupérer tous les utilisateurs ayant le rôle "participant"
    $sql = "SELECT * FROM utilisateurs WHERE role = 'participant'";
    $result = $connexion->query($sql);

    if ($result->num_rows > 0) {
        $participants = array();
        while ($row = $result->fetch_assoc()) {
            $participants[] = $row;
        }
        echo json_encode($participants);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Aucun participant trouvé"));
    }
}

// Endpoint pour ajouter un participant
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->nom) && !empty($data->email) && !empty($data->formation)) {
        $nom = $data->nom;
        $email = $data->email;
        $formation = $data->formation;

        // Hasher le mot de passe par exemple
        $mot_de_passe = password_hash($data->mot_de_passe, PASSWORD_DEFAULT);

        // Insérer le nouvel utilisateur avec le rôle "participant"
        $stmt = $connexion->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, formation, role) VALUES (?, ?, ?, ?, 'participant')");
        $stmt->bind_param("ssss", $nom, $email, $mot_de_passe, $formation);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array("message" => "Participant ajouté avec succès"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Erreur lors de l'ajout du participant"));
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Toutes les données sont requises"));
    }
}

// Endpoint pour modifier un participant
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->id) && !empty($data->nom) && !empty($data->email) && !empty($data->formation)) {
        $id = $data->id;
        $nom = $data->nom;
        $email = $data->email;
        $formation = $data->formation;

        // Mettre à jour les données du participant
        $stmt = $connexion->prepare("UPDATE utilisateurs SET nom=?, email=?, formation=? WHERE id=? AND role='participant'");
        $stmt->bind_param("sssi", $nom, $email, $formation, $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "Participant mis à jour avec succès"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Erreur lors de la mise à jour du participant"));
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Toutes les données sont requises"));
    }
}

// Endpoint pour supprimer un participant
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->id)) {
        $id = $data->id;

        // Supprimer l'utilisateur avec l'ID spécifié
        $stmt = $connexion->prepare("DELETE FROM utilisateurs WHERE id=? AND role='participant'");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "Participant supprimé avec succès"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Erreur lors de la suppression du participant"));
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID du participant requis"));
    }
}

$connexion->close();
?>
