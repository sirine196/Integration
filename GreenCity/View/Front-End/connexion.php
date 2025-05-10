<?php
session_start();
require "../../config_db.php";

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['connexion'])) {
        if (!isset($_POST['mail'], $_POST['mdp'])) {
            echo "<script>alert('Tous les champs sont requis.');</script>";
        } else {
            $mail = trim($_POST['mail']);
            $mdp = trim($_POST['mdp']);

            $sql = "SELECT Id, Nom, Prenom, Mail, Mdp, Status FROM user WHERE Mail = :mail";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user['Status'] == 'Confirmé') {
                    if (password_verify($mdp, $user['Mdp'])) {
                        $_SESSION['IdCurrentUser'] = $user['Id'];
                        $_SESSION['Nom'] = $user['Nom'];
                        $_SESSION['Prenom'] = $user['Prenom'];
                        $_SESSION['Mail'] = $user['Mail'];
                        header("Location: captcha.html");
                        exit();
                    } else {
                        echo "<script>alert('Mot de passe incorrect.');</script>";
                    }
                } else {
                    echo '<script>alert("Un admin doit approuver votre demande pour que vous puissiez vous connecter");</script>';
                }
            } else {
                echo "<script>alert('Utilisateur non trouvé.');</script>";
            }
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
    <title>Connexion</title>
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
            </form><br>
        </div>
    </main>
</body>
</html>