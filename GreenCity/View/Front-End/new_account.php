<?php
require "../../config_db.php";

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['nom'], $_POST['prenom'], $_POST['mail'], $_POST['mdp'])) {
        die("Tous les champs sont requis.");
    }

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $mail = trim($_POST['mail']);
    $tel = trim($_POST['tel']);
    $mdp = password_hash(trim($_POST['mdp']), PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    if($role=="Client"){
        $status="Confirmé";
    }else{
        $status="En Attente";
    }
    
    $sql = "INSERT INTO user (Nom, Prenom,Role, Status, Mail, Phone, Mdp) 
            VALUES ('$nom', '$prenom','$role','$status', '$mail','$tel' , '$mdp')";

    try {
        $conn->exec($sql);
        echo "Utilisateur enregistré avec succès.";
        header("Location: connexion.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }

    $conn = null;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="interface.css">
    <link rel="stylesheet" href="user.css">
    <script src="fonctions_communes.js"></script>
    <script src="new_account.js"></script>
</head>
<body>
    <header>
        <img src="../../Ressources/logo_GreenCity_trans.png" width="20%" alt="" id="logo_header">
        <a href="user.php" class="profile-link">
            <img src="../../Ressources/profile.png" alt="Profile Icon" class="profile-icon">
            Profile
        </a>
    </header>
    <main class="profile">
        <div class="profile-highlight">
            <form action="" method="POST" onsubmit="return create()">
                <h2>New Account</h2>
                <table>
                    <tr>
                        <td width="60%">
                            <label for="nom">Nom</label>
                        </td>
                        <td>
                            <input type="text" name="nom" id="nom">
                        </td>
                    </tr>
                    <tr>
                        <td width="60%">
                            <label for="prenom">Prenom</label>
                        </td>
                        <td>
                            <input type="text" name="prenom" id="prenom">
                        </td>
                    </tr>
                    <tr>
                        <td width="60%">
                            <label for="mail">Mail</label>
                        </td>
                        <td>
                            <input type="text" name="mail" id="mail">
                        </td>
                    </tr>
                    <tr>
                        <td width="60%">
                            <label for="tel">Tel</label>
                        </td>
                        <td>
                            <input type="text" name="tel" id="tel">
                        </td>
                    </tr>
                    <tr>
                        <td width="60%">
                            <label for="mdp">Mot De Passe</label>
                        </td>
                        <td>
                            <input type="password" name="mdp" id="mdp">
                        </td>
                    </tr>
                    <tr>
                        <td width="60%">
                            <label for="cmdp">Confirmer Mot De Passe</label>
                        </td>
                        <td>
                            <input type="password" name="cmdp" id="cmdp">
                        </td>
                    </tr>
                    <tr>
                        <td width="60%">
                            <label for="role">Role</label>
                        </td>
                        <td>
                            <select name="role" id="role">
                                <option value="Client">Client</option>
                                <option value="Agriculteur">Agriculteur</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <input type="submit" value="Create Account" class="connect">
            </form>

        </div>
    </main> 
</body>
</html>