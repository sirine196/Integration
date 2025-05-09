<?php
session_start();
require "../../config_db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['connexion'])) {

        if (!isset($_POST['mail'], $_POST['mdp'])) {
            die("Tous les champs sont requis.");
        }

        $mail = trim($_POST['mail']);
        $mdp = trim($_POST['mdp']);

        $sql = "SELECT Id, Mail, Mdp, Status FROM user WHERE Mail = '$mail'";
        $result = $conn->query($sql);

        if ($result && $result->rowCount() > 0) {
            $user = $result->fetch(PDO::FETCH_ASSOC);
            if ($user['Status'] == 'Confirmé') {
                if (password_verify($mdp, $user['Mdp'])) {
                    $_SESSION['IdCurrentUser'] = $user['Id'];
                    header("Location: captcha.html");
                    exit();
                } else {
                    die("Mot de passe incorrect.");
                }
            } else {
                echo '<script>alert("Un admin doit approuver votre demande pour que vous puissiez vous connecter")</script>';
            }
        } else {
            die("Utilisateur non trouvé.");
        }
    }
}
$conn = null;
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
    <script src="connexion.js"></script>
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
            <form action="" onsubmit="return cnx()" method="POST">
                <h2>Connexion</h2>
                <table>
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
                            <label for="mdp">Mot De Passe</label>
                        </td>
                        <td>
                            <input type="password" name="mdp" id="mdp">
                        </td>
                    </tr>
                </table>
                <br>
                <input type="submit" name="connexion" value="Connexion" class="connect">
            </form><br></div>
    </main> 
</body>
</html>
