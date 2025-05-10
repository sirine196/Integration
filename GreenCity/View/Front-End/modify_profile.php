<?php
session_start();
require "../../config_db.php";

$database = new Database();
$conn = $database->getConnection();

$IdCurrentUser = $_SESSION['IdCurrentUser'] ?? null;
$userInfo = null;

if (isset($IdCurrentUser)) {
    $query = $conn->query("SELECT Nom, Prenom, Mail, Mdp, Role, Img FROM user WHERE Id = $IdCurrentUser");
    $userInfo = $query->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mail = $_POST['mail'];
    $old_mdp = $_POST['mdp'];
    $new_mdp = $_POST['new_mdp'];
    $cmdp = $_POST['cmdp'];

    $img_path = $userInfo['Img'] ?? null;
    if (isset($_FILES['img_profil']) && $_FILES['img_profil']['error'] == 0) {
        $img_name = basename($_FILES['img_profil']['name']);
        $img_tmp = $_FILES['img_profil']['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($img_ext, $allowed_exts) && $_FILES['img_profil']['size'] < 1000000) {
            $new_filename = uniqid() . '.' . $img_ext;
            $upload_path = "../../Ressources/profils/" . $new_filename;
            if (move_uploaded_file($img_tmp, $upload_path)) {
                $img_path = "Ressources/profils/" . $new_filename;
                $conn->query("UPDATE user SET Img = '$img_path' WHERE Id = $IdCurrentUser");
            }
        } else {
            echo "<script>alert('Format d’image non valide ou taille trop grande.');</script>";
        }
    }

    if (!empty($new_mdp) && !empty($cmdp)) {
        if ($new_mdp !== $cmdp) {
            echo "<script>alert('Les mots de passe ne correspondent pas.')</script>";
        } elseif (!password_verify($old_mdp, $userInfo['Mdp'])) {
            echo "<script>alert('Mot de passe incorrect.')</script>";
        } else {
            $mdp_hash = password_hash($new_mdp, PASSWORD_DEFAULT);
            $query = "UPDATE user SET Nom = '$nom', Prenom = '$prenom', Mail = '$mail', Mdp = '$mdp_hash' WHERE Id = $IdCurrentUser";
            if ($conn->query($query)) {
                header("Location: modify_profile.php");
                exit();
            } else {
                echo "<p style='color:red;'>Erreur : " . $conn->errorInfo()[2] . "</p>";
            }
        }
    } else {
        $query = "UPDATE user SET Nom = '$nom', Prenom = '$prenom', Mail = '$mail' WHERE Id = $IdCurrentUser";
        if ($conn->query($query)) {
            header("Location: modify_profile.php");
            exit();
        } else {
            echo "<p style='color:red;'>Erreur : " . $conn->errorInfo()[2] . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="interface.css">
    <link rel="stylesheet" href="user.css">
</head>
<body>
    <header>
        <img src="../../Ressources/logo_GreenCity_trans.png" width="20%" alt="" id="logo_header">
        <nav>
            <a href="interface.php">Home</a>
            <a href="./event/events.php">Events</a>
            <a href="forum.php">Forum</a>
            <a href="#">Challenges</a>
            <a href="./map/map.php">Map</a>
            <?php if ($userInfo['Role'] == 'Admin') echo '<a href="../Back-End/dashboard.php">Dashboard</a>'; ?>
        </nav>
        <a href="user.php" class="profile-link">
            <img src="../../<?= $userInfo['Img'] ?? 'Ressources/profile.png' ?>" alt="Profile Icon" class="profile-icon">
            <h2><?= $userInfo['Prenom'] . " " . $userInfo['Nom'] ?></h2>
        </a>
    </header>

    <main class="profile">
        <div class="profile-highlight">
            <form action="" method="POST" enctype="multipart/form-data">
                <h2>Profile Settings</h2>
                <img src="../../<?= $userInfo['Img'] ?? 'Ressources/profile.png' ?>" alt="Profile Image" class="profile-icon" style="width:100px; border-radius:50%"><br>
                <input type="file" name="img_profil" id="img_profil" accept="image/*"><br><br><hr><br>

                <table>
                    <tr>
                        <td width="60%"><label for="nom">Nom</label></td>
                        <td><input type="text" name="nom" id="nom" value="<?= $userInfo['Nom'] ?? '' ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="prenom">Prenom</label></td>
                        <td><input type="text" name="prenom" id="prenom" value="<?= $userInfo['Prenom'] ?? '' ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="mail">Mail</label></td>
                        <td><input type="text" name="mail" id="mail" value="<?= $userInfo['Mail'] ?? '' ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="mdp">Mot De Passe</label></td>
                        <td><input type="password" name="mdp" id="mdp"></td>
                    </tr>
                    <tr>
                        <td><label for="new_mdp">Nouveau Mot De Passe</label></td>
                        <td><input type="password" name="new_mdp" id="new_mdp"></td>
                    </tr>
                    <tr>
                        <td><label for="cmdp">Confirmer Mot De Passe</label></td>
                        <td><input type="password" name="cmdp" id="cmdp"></td>
                    </tr>
                </table><br>
                <input type="submit" value="Save Settings" class="connect">
            </form>
        </div>
    </main>
</body>
</html>
