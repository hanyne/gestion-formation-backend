<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 3600");

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $data = json_decode(file_get_contents("php://input"));

    // Vérifier si toutes les données nécessaires sont présentes
    if (!empty($data->nom_formation) && !empty($data->description) && !empty($data->date_debut) && !empty($data->date_fin) && !empty($data->status)) {
        $nom_formation = $data->nom_formation;
        $description = $data->description;
        $date_debut = $data->date_debut;
        $date_fin = $data->date_fin;
        $status = $data->status;

        // Préparer et exécuter la requête SQL d'insertion
        $stmt = $connexion->prepare("INSERT INTO formations (nom_formation, description, date_debut, date_fin, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nom_formation, $description, $date_debut, $date_fin, $status);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array("message" => "Formation ajoutée avec succès"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Erreur lors de l'ajout de la formation"));
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Toutes les données sont requises"));
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Récupérer toutes les formations
    $sql = "SELECT * FROM formations";
    $result = $connexion->query($sql);

    if ($result->num_rows > 0) {
        $formations = array();
        while ($row = $result->fetch_assoc()) {
            $formations[] = $row;
        }
        echo json_encode($formations);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Aucune formation trouvée"));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Récupérer les données envoyées en JSON
    $data = json_decode(file_get_contents('php://input'));

    // Vérifier si toutes les données nécessaires sont présentes
    if (!empty($data->id) && !empty($data->nom_formation) && !empty($data->description) && !empty($data->date_debut) && !empty($data->date_fin) && !empty($data->status)) {
        $id = $data->id;
        $nom_formation = $data->nom_formation;
        $description = $data->description;
        $date_debut = $data->date_debut;
        $date_fin = $data->date_fin;
        $status = $data->status;

        // Préparer et exécuter la requête SQL de mise à jour
        $stmt = $connexion->prepare("UPDATE formations SET nom_formation=?, description=?, date_debut=?, date_fin=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $nom_formation, $description, $date_debut, $date_fin, $status, $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "Formation mise à jour avec succès"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Erreur lors de la mise à jour de la formation"));
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Toutes les données sont requises"));
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Récupérer l'ID de la formation à supprimer
    $id = $_GET['id'];

    if (!empty($id)) {
        // Préparer et exécuter la requête SQL de suppression
        $stmt = $connexion->prepare("DELETE FROM formations WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "Formation supprimée avec succès"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Erreur lors de la suppression de la formation"));
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "L'ID de la formation est requis"));
    }
}

$connexion->close();
?>
