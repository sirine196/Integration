<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['IdCurrentUser'])) {
    header("Location: ../view/Front-End/connexion.php?error=Veuillez vous connecter pour effectuer cette action.");
    exit;
}

require_once __DIR__ . '/../Model/ForumComment.php';
require_once __DIR__ . '/../Model/userModel.php';

$forumComment = new ForumComment();
$database = new Database();
$conn = $database->getConnection();
$userInfo = getUserById($conn, $_SESSION['IdCurrentUser']);
$fullName = $userInfo['Nom'] . ' ' . $userInfo['Prenom'];

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Créer un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $id_topic = (int)$_POST['id_topic'];
    $id_client = (string)$_SESSION['IdCurrentUser'];
    $comment_text = trim($_POST['comment_text']);

    // Validation des données
    if (strlen($comment_text) < 8) {
        header("Location: ../view/Front-End/forum.php?error=Le commentaire doit contenir au moins 8 caractères.");
        exit;
    }

    if ($forumComment->createComment($id_topic, $id_client, $fullName, $comment_text)) {
        header("Location: ../view/Front-End/forum.php?success=Commentaire ajouté.");
        exit;
    } else {
        header("Location: ../view/Front-End/forum.php?error=Erreur lors de la création du commentaire.");
        exit;
    }
}

// Supprimer un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_comment = (int)$_POST['id_comment'];

    if ($forumComment->deleteComment($id_comment)) {
        header("Location: ../view/Front-End/forum.php?success=Commentaire supprimé.");
        exit;
    } else {
        header("Location: ../view/Front-End/forum.php?error=Erreur lors de la suppression du commentaire.");
        exit;
    }
}

// Modifier un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id_comment = (int)$_POST['id_comment'];
    $new_comment_text = trim($_POST['comment_text']);

    // Validation
    if (strlen($new_comment_text) < 4) {
        header("Location: ../view/Front-End/forum.php?error=Le commentaire doit contenir au moins 4 caractères.");
        exit;
    }

    if ($forumComment->updateComment($id_comment, $new_comment_text)) {
        header("Location: ../view/Front-End/forum.php?success=Commentaire modifié avec succès.");
        exit;
    } else {
        header("Location: ../view/Front-End/forum.php?error=Erreur lors de la mise à jour du commentaire.");
        exit;
    }
}
?>