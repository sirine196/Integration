<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['IdCurrentUser'])) {
    header("Location: ../view/Front-End/connexion.php?error=Veuillez vous connecter pour effectuer cette action.");
    exit;
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../Model/ForumTopic.php';
require_once __DIR__ . '/../Model/RatingModel.php';
require_once __DIR__ . '/../Model/userModel.php';

$forumTopic = new ForumTopic();
$database = new Database();
$conn = $database->getConnection();
$userInfo = getUserById($conn, $_SESSION['IdCurrentUser']);
$fullName = $userInfo['Nom'] . ' ' . $userInfo['Prenom'];

$topicId = isset($_GET['topic_id']) ? $_GET['topic_id'] : null;

// Vérification de l'action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Créer un topic
    if ($action === 'create') {
        $id_client = (string)$_POST['id_client']; // Convertir en chaîne pour VARCHAR
        $topic_text = trim($_POST['message-content']);
    
        // Validation
        if (strlen($topic_text) < 4) {
            header("Location: ../view/Front-End/forum.php?error=Le message doit contenir au moins 4 caractères.");
            exit;
        }
    
        if ($forumTopic->createTopic($id_client, $topic_text)) {
            header("Location: ../view/Front-End/forum.php?success=Message publié avec succès.");
            exit;
        } else {
            header("Location: ../view/Front-End/forum.php?error=Erreur lors de la création du message.");
            exit;
        }
    }

    // Modifier un topic
    if ($action === 'edit') {
        $id_topic = (int)$_POST['id_topic'];
        $new_topic_text = trim($_POST['topic_text']);

        // Validation
        if (strlen($new_topic_text) < 4) {
            header("Location: ../view/Front-End/forum.php?error=Le message doit contenir au moins 4 caractères.");
            exit;
        }

        if ($forumTopic->updateTopic($id_topic, $new_topic_text)) {
            header("Location: ../view/Front-End/forum.php?success=Message modifié avec succès.");
            exit;
        } else {
            header("Location: ../view/Front-End/forum.php?error=Erreur lors de la mise à jour du message.");
            exit;
        }
    }

    // Supprimer un topic
    if ($action === 'delete') {
        $id_topic = (int)$_POST['id_topic'];

        if ($forumTopic->deleteTopic($id_topic)) {
            header("Location: ../view/Front-End/forum.php?success=Message supprimé avec succès.");
            exit;
        } else {
            header("Location: ../view/Front-End/forum.php?error=Erreur lors de la suppression du message.");
            exit;
        }
    }

    // Ajouter une note
    if ($action === 'rate') {
        $rating = (int)$_POST['rating'];
        $userEmail = $_POST['user_email'];
    
        if ($userEmail !== $userInfo['Mail']) {
            header("Location: ../view/Front-End/forum.php?error=Email invalide.");
            exit;
        }
    
        if (filter_var($userEmail, FILTER_VALIDATE_EMAIL) && $rating >= 1 && $rating <= 5) {
            if ($forumTopic->hasRated($userEmail)) {
                header("Location: ../view/Front-End/forum.php?error=Vous avez déjà noté ce site.");
                exit;
            }
    
            if ($forumTopic->addRating($rating, $userEmail)) {
                $forumTopic->updateSiteRating();
                header("Location: ../view/Front-End/forum.php?success=Note ajoutée avec succès.");
                exit;
            } else {
                header("Location: ../view/Front-End/forum.php?error=Erreur lors de l'ajout de la note.");
                exit;
            }
        } else {
            header("Location: ../view/Front-End/forum.php?error=Données invalides.");
            exit;
        }
    }
}

class TopicController {
    public function showTopic($topicId) {
        if (!is_numeric($topicId) || $topicId <= 0) {
            echo "L'ID du sujet est invalide.";
            return;
        }

        $model = new ForumTopic();
        $topicData = $model->getTopicById($topicId);

        if ($topicData) {
            echo "Affichage du sujet avec l'ID : " . $topicId;
        } else {
            echo "Aucun sujet trouvé avec l'ID " . $topicId;
        }
    }
}
?>