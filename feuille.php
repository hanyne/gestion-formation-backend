<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 3600");

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Répondre aux requêtes préflight OPTIONS
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajouter une feuille de présence
    $data = json_decode(file_get_contents('php://input'), true);

    // Extraire les données de la requête JSON
    $dateApplication = $data['dateApplication'];
    $numChecked = $data['numChecked'];
    $creditImpotChecked = $data['creditImpotChecked'];
    $droitTirageIndividuelChecked = $data['droitTirageIndividuelChecked'];
    $droitTirageCollectifChecked = $data['droitTirageCollectifChecked'];
    $periodeFrom = $data['periodeFrom'];
    $periodeTo = $data['periodeTo'];
    $horaireFrom = $data['horaireFrom'];
    $horraireTo = $data['horraireTo'];
    $theme = $data['theme'];
    $loiFormation = $data['loiFormation'];
    $modeFormation = $data['modeFormation'];
    $formateur = $data['formateur'];
    $specialite = $data['specialite'];
    $directeur = $data['directeur'];

    // Préparer et exécuter la requête SQL d'insertion
    $stmt = $conn->prepare("INSERT INTO feuille_presence (dateApplication, numChecked, creditImpotChecked, droitTirageIndividuelChecked, droitTirageCollectifChecked, periodeFrom, periodeTo, horaireFrom, horaireTo, theme, loiFormation, modeFormation, formateur, specialite, directeur) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiiiiisssssss", $dateApplication, $numChecked, $creditImpotChecked, $droitTirageIndividuelChecked, $droitTirageCollectifChecked, $periodeFrom, $periodeTo, $horaireFrom, $horraireTo, $theme, $loiFormation, $modeFormation, $formateur, $specialite, $directeur);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Feuille de présence ajoutée avec succès"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Erreur lors de l'ajout de la feuille de présence"));
    }

    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtenir toutes les feuilles de présence
    $sql = "SELECT * FROM feuille_presence";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $feuilles_presence = array();
        while ($row = $result->fetch_assoc()) {
            $feuilles_presence[] = $row;
        }
        echo json_encode($feuilles_presence);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Aucune feuille de présence trouvée"));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Mettre à jour une feuille de présence
    $data = json_decode(file_get_contents('php://input'), true);

    // Extraire les données de la requête JSON
    $id = $data['id'];
    $numChecked = $data['numChecked'];
    $creditImpotChecked = $data['creditImpotChecked'];
    $droitTirageIndividuelChecked = $data['droitTirageIndividuelChecked'];
    $droitTirageCollectifChecked = $data['droitTirageCollectifChecked'];
    $periodeFrom = $data['periodeFrom'];
    $periodeTo = $data['periodeTo'];
    $horaireFrom = $data['horaireFrom'];
    $horraireTo = $data['horraireTo'];
    $theme = $data['theme'];
    $loiFormation = $data['loiFormation'];
    $modeFormation = $data['modeFormation'];
    $formateur = $data['formateur'];
    $formateurId = $data['formateurId'];
    $specialite = $data['specialite'];
    $directeur = $data['directeur'];
    $lignesTableau = $data['lignesTableau']; // Ajout de id_formateur

    // Préparer et exécuter la requête SQL de mise à jour
    $stmt = $conn->prepare("UPDATE feuille_presence SET numChecked=?, creditImpotChecked=?, droitTirageIndividuelChecked=?, droitTirageCollectifChecked=?, periodeFrom=?, periodeTo=?, horaireFrom=?, horaireTo=?, theme=?, loiFormation=?, modeFormation=?, formateur=?, specialite=?, directeur=? WHERE id=?");
    $stmt->bind_param("iiiiiiiissssssi", $numChecked, $creditImpotChecked, $droitTirageIndividuelChecked, $droitTirageCollectifChecked, $periodeFrom, $periodeTo, $horaireFrom, $horraireTo, $theme, $loiFormation, $modeFormation, $formateur, $specialite, $directeur, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "Feuille de présence mise à jour avec succès"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Erreur lors de la mise à jour de la feuille de présence"));
    }

    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Supprimer une feuille de présence
    parse_str(file_get_contents("php://input"), $del_vars);

    if (isset($del_vars['id'])) {
        $id = $del_vars['id'];

        // Préparer et exécuter la requête SQL de suppression
        $stmt = $conn->prepare("DELETE FROM feuille_presence WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "Feuille de présence supprimée avec succès"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Erreur lors de la suppression de la feuille de présence"));
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID de la feuille de présence non fourni"));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Méthode non autorisée"));
}

$conn->close();
?>
