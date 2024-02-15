<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 3600");

require_once 'config.php';

$check_specialite_column = $connexion->query("SHOW COLUMNS FROM utilisateurs LIKE 'specialite'");
if ($check_specialite_column->num_rows == 0) {
    $connexion->query("ALTER TABLE utilisateurs ADD COLUMN specialite VARCHAR(255) NOT NULL DEFAULT ''");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donnees_json = file_get_contents('php://input');
    $donnees = json_decode($donnees_json, true);

    if (isset($donnees['nom']) && isset($donnees['prenom']) && isset($donnees['email']) && isset($donnees['mot_de_passe']) && isset($donnees['specialite'])) {
        $nom = $donnees['nom'];
        $prenom = $donnees['prenom'];
        $email = $donnees['email'];
        $mot_de_passe = $donnees['mot_de_passe'];
        $specialite = $donnees['specialite'];

        $requete = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, specialite)
                    VALUES ('$nom', '$prenom', '$email', '$mot_de_passe', 'formateur', '$specialite')";
        if ($connexion->query($requete) === TRUE) {
            http_response_code(201);
        } else {
            http_response_code(500);
            echo "Erreur lors de l'ajout du formateur : " . $connexion->error;
        }
    } else {
        http_response_code(400);
        echo "Données manquantes pour l'ajout du formateur";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $formateur_id = $_GET['id'];
        $resultat = $connexion->query("SELECT * FROM utilisateurs WHERE id = $formateur_id");
        if ($resultat && $resultat->num_rows > 0) {
            echo json_encode($resultat->fetch_assoc());
        } else {
            http_response_code(404);
            echo "Aucun formateur trouvé pour l'ID spécifié";
        }
    } else {
        $resultat = $connexion->query("SELECT * FROM utilisateurs WHERE role = 'formateur'");
        if ($resultat && $resultat->num_rows > 0) {
            $formateurs = [];
            while ($row = $resultat->fetch_assoc()) {
                $formateurs[] = $row;
            }
            echo json_encode($formateurs);
        } else {
            echo json_encode([]);
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $donnees = json_decode(file_get_contents('php://input'), true);

    if (isset($donnees['id']) && isset($donnees['nom_formation']) && isset($donnees['description']) && isset($donnees['date_debut']) && isset($donnees['date_fin']) && isset($donnees['status'])) {
        $id = $donnees['id'];
        $nom_formation = $donnees['nom_formation'];
        $description = $donnees['description'];
        $date_debut = $donnees['date_debut'];
        $date_fin = $donnees['date_fin'];
        $status = $donnees['status'];

        $requete = "UPDATE formations SET nom_formation='$nom_formation', description='$description', date_debut='$date_debut', date_fin='$date_fin', status='$status' WHERE id=$id";

        if ($connexion->query($requete) === TRUE) {
            http_response_code(200);
        } else {
            http_response_code(500);
            echo "Erreur lors de la modification de la formation : " . $connexion->error;
        }
    } else {
        http_response_code(400);
        echo "Données manquantes pour la modification de la formation";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check if formateur ID is provided in the request URL
    if (isset($_GET['id'])) {
        // Extract the formateur ID from the request URL
        $formateur_id = $_GET['id'];

        // Construct the SQL query to delete the formateur with the specified ID
        $requete = "DELETE FROM utilisateurs WHERE id = $formateur_id";

        // Execute the delete query
        if ($connexion->query($requete) === TRUE) {
            // Set HTTP response code to indicate successful deletion
            http_response_code(200);
        } else {
            // Set HTTP response code to indicate server error
            http_response_code(500);
            // Provide error message in case of failure
            echo "Erreur lors de la suppression du formateur : " . $connexion->error;
        }
    } else {
        // Set HTTP response code to indicate missing formateur ID in request URL
        http_response_code(400);
        // Provide error message for missing formateur ID
        echo "ID du formateur manquant dans l'URL de la requête";
    }
}


$connexion->close();

?>