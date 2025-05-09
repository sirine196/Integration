<?php
session_start();
require_once "../../Model/userModel.php";
require_once "../../config_db.php";

try {

    $IdCurrentUser = $_SESSION['IdCurrentUser'] ?? null;
    $userInfo = null;

    if ($IdCurrentUser) {
        $userInfo = getUserById($conn, $IdCurrentUser);
    }

    if (isset($_POST['confirm'])) {
        $idUser = $_POST['confirm'];
        if (confirmUser($conn, $idUser)) {
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<p style='color: red;'>Erreur lors de la confirmation.</p>";
        }
    }

    if (isset($_POST['defin_admin'])) {
        $idUser = $_POST['defin_admin'];
        if (setAdmin($conn, $idUser)) {
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<p style='color: red;'>Erreur lors de la mise à jour du rôle.</p>";
        }
    }

    if (isset($_POST['remove_admin'])) {
        $idUser = $_POST['remove_admin'];
        if (removeAdmin($conn, $idUser)) {
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<p style='color: red;'>Erreur lors de la mise à jour du rôle.</p>";
        }
    }

    if (isset($_POST['delete_id'])) {
        $idUser = $_POST['delete_id'];
        if (deleteUser($conn, $idUser)) {
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<p style='color: red;'>Erreur lors de la suppression.</p>";
        }
    }

    $allUsers = getAllUsers($conn);

    if (isset($_POST['recherche'])) {
        $recherche = $_POST['recherche'];
        $allUsers = recherche($conn, $recherche);
    }

    if (isset($_GET['order_by'])) {
        $order = $_GET['order_by'];
        if ($order == "Id") {
            $allUsers = order_id($conn);
        } elseif ($order == "Nom") {
            $allUsers = order_nom($conn);
        } elseif ($order == "Prenom") {
            $allUsers = order_prenom($conn);
        }
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>Erreur de connexion ou d'exécution : " . $e->getMessage() . "</p>";
}
?>
