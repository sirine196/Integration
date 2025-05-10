<?php
require_once __DIR__ . '/../config_db.php';

$database = new Database();
$conn = $database->getConnection();

function getAllUsers($conn) {
    return $conn->query("SELECT Id, Nom, Prenom, Mail, Role, Status FROM user")->fetchAll(PDO::FETCH_ASSOC);
}

function order_id($conn) {
    return $conn->query("SELECT Id, Nom, Prenom, Mail, Role, Status FROM user ORDER BY Id")->fetchAll(PDO::FETCH_ASSOC);
}

function order_nom($conn) {
    return $conn->query("SELECT Id, Nom, Prenom, Mail, Role, Status FROM user ORDER BY Nom")->fetchAll(PDO::FETCH_ASSOC);
}

function order_prenom($conn) {
    return $conn->query("SELECT Id, Nom, Prenom, Mail, Role, Status FROM user ORDER BY Prenom")->fetchAll(PDO::FETCH_ASSOC);
}

function confirmUser($conn, $idUser) {
    return $conn->prepare("UPDATE user SET Status = 'Confirmé' WHERE Id = :id")->execute([':id' => $idUser]);
}

function setAdmin($conn, $idUser) {
    return $conn->prepare("UPDATE user SET Role = 'Admin' WHERE Id = :id")->execute([':id' => $idUser]);
}

function removeAdmin($conn, $idUser) {
    return $conn->prepare("UPDATE user SET Role = 'User' WHERE Id = :id")->execute([':id' => $idUser]);
}

function deleteUser($conn, $idUser) {
    return $conn->prepare("DELETE FROM user WHERE Id = :id")->execute([':id' => $idUser]);
}

function getUserById($conn, $idUser) {
    return $conn->query("SELECT * FROM user WHERE Id = $idUser")->fetch(PDO::FETCH_ASSOC);
}

function recherche($conn, $recherche) {
    if (strlen($recherche) != 0) {
        $recherche = htmlspecialchars($recherche, ENT_QUOTES); // protection minimale
        $sql = "SELECT Id, Nom, Prenom, Mail, Role, Status 
                FROM user 
                WHERE Id LIKE '%$recherche%' OR Nom LIKE '%$recherche%' OR Prenom LIKE '%$recherche%'";
        return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return getAllUsers($conn);
    }
}
?>
