<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['IdCurrentUser'])) {
    header("Location: connexion.php?error=Veuillez vous connecter pour accéder au forum.");
    exit;
}

// Récupérer les informations de l'utilisateur
require_once __DIR__ . '/../../Model/userModel.php';
$database = new Database();
$conn = $database->getConnection();
$userInfo = getUserById($conn, $_SESSION['IdCurrentUser']);
$fullName = $userInfo['Nom'] . ' ' . $userInfo['Prenom'];
$userEmail = $userInfo['Mail'];

// Inclusions existantes
require_once __DIR__ . '/../../model/ForumTopic.php';
require_once __DIR__ . '/../../model/ForumComment.php';
require_once __DIR__ . '/../../config_db.php';
require_once __DIR__ . '/../../controller/TopicController.php';
require_once __DIR__ . '/../../Model/RatingModel.php';

$forumTopic = new ForumTopic();
$forumComment = new ForumComment();
$controller = new TopicController();
$ratingModel = new RatingModel();


// Appeler showTopic pour afficher un sujet avec l'ID du sujet
if (isset($_GET['topic_id']) && is_numeric($_GET['topic_id'])) {
    $controller->showTopic($_GET['topic_id']);
}
$ratingData = $ratingModel->getAverageRating(); 
// Vérifiez si des évaluations existent
if ($ratingData['number_of_ratings'] > 0) {
    // Calcul de la moyenne arrondie à 1 décimale
    $average = round($ratingData['average_rating'], 1);
} else {
    $average = 'Non noté';
}

// Fetch all topics
$topics = $forumTopic->getAllTopics();

// Gérer un commentaire sans confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'], $_POST['id_topic'])) {
    $id_topic = (int)$_POST['id_topic'];
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text)) {
        $forumComment->createComment($id_topic, (string)$_SESSION['IdCurrentUser'], $fullName, $comment_text);
        header("Location: forum.php?success=Commentaire publié avec succès.");
        exit;
    } else {
        header("Location: forum.php?topic_id=" . $id_topic . "&error=Le commentaire ne peut pas être vide.");
        exit;
    }
}

// Supprimer un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $id_comment = (int)$_POST['delete_comment'];
    $forumComment->deleteComment($id_comment);
    header("Location: forum.php");
    exit;
}

// Éditer un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'], $_POST['id_comment'], $_POST['comment_text'])) {
    $id_comment = (int)$_POST['id_comment'];
    $comment_text = trim($_POST['comment_text']);
    $id_topic = (int)$_POST['id_topic'];

    if (!empty($comment_text)) {
        $forumComment->updateComment($id_comment, $comment_text);
        header("Location: forum.php");
        exit;
    } else {
        header("Location: forum.php?topic_id=" . $id_topic . "&error=Le commentaire ne peut pas être vide");
        exit;
    }
}

// Vérifier s'il y a un message d'erreur ou de succès dans l'URL
$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success_message = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green City - Forum</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="../../Ressources/logo_GreenCity_trans.png" width="80%" alt="GreenCity Logo">
        </div>
        <nav class="sidebar-nav">
            <a href="interface.php" class="nav-item">Home</a>
            <a href="../event/events.html" class="nav-item">Events</a>
            <a href="forum.php" class="nav-item active">Forum</a>
            <a href="#" class="nav-item">Challenges</a>
            <a href="../map/map.html" class="nav-item">Map</a>
        </nav>
    </aside>
    
    <!-- Barre de succès -->
    <?php if (!empty($success_message)): ?>
        <div class="success-bar" id="success-bar">
            <?= $success_message; ?>
        </div>
    <?php endif; ?>

    <!-- Barre d'erreur -->
    <?php if (!empty($error_message)): ?>
        <div class="error-bar" id="error-bar">
            <?= $error_message; ?>
        </div>
    <?php endif; ?>

    <section class="forum-section">
    <div class="forum-form">
    <h3>Post a Message</h3>
    <form id="post-topic-form" action="../../controller/TopicController.php" method="POST" onsubmit="return validateTopicForm();">
        <label for="message-content">Message</label>
        <textarea id="message-content" name="message-content" rows="4" placeholder="Écrire un message..."></textarea>
        <span id="message-content-error" style="color: red; font-size: 0.9em; display: none;"></span>
        
        <input type="hidden" name="id_client" value="<?= htmlspecialchars($_SESSION['IdCurrentUser']); ?>">
        <input type="hidden" name="action" value="create">
        <button type="submit" class="forum-submit">Post Message</button>
    </form>

            <h3>Discussion Forum</h3>
            <div class="forum-messages">
                <h3>Community Discussions</h3>
                <?php if (empty($topics)): ?>
                    <p>Aucun sujet disponible pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($topics as $topic): ?>
                        <div class="message <?php echo $topic['is_pinned'] ? 'pinned-topic' : ''; ?>">
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $topic['id_topic']): ?>
                                <!-- Edit Form -->
                                <form action="../../controller/TopicController.php" method="POST" onsubmit="return validateEditForm();">
                                    <input type="hidden" name="id_topic" value="<?= $topic['id_topic']; ?>">
                                    <input type="hidden" name="action" value="edit">
                                    <textarea name="topic_text" rows="4" id="edit-topic-text"><?= htmlspecialchars($topic['topic_text']); ?></textarea>
                                    <div id="edit-message-error" style="color: red; font-size: 0.9em; display: none;"></div>
                                    <button type="submit" name="edit_topic">Enregistrer les modifications</button>
                                    <a href="forum.php" class="btn">Annuler</a>
                                </form>
                            <?php else: ?>
                                <!-- Display Topic -->
                                <p>
                                    <strong>
                                        <?= htmlspecialchars($topic['client_name'] ?? 'Utilisateur inconnu'); ?>:
                                    </strong> 
                                    <?= htmlspecialchars($topic['topic_text']); ?>
                                    <?php if ($topic['is_pinned']): ?>
                                        <span class="pinned-label">📍 Épinglé</span>
                                    <?php endif; ?>
                                </p>
                                <p class="message-time">Posté le : <?= htmlspecialchars($topic['created_at']); ?></p>

                                <!-- Compteur de commentaires -->
                                <div class="comment-count">
                                    Commentaires : <?= $topic['comment_count']; ?>
                                </div>

                                <!-- Action Buttons -->
                                <form action="forum.php" method="GET" style="display:inline;">
                                    <input type="hidden" name="edit" value="<?= $topic['id_topic']; ?>">
                                    <button type="submit">Edit</button>
                                </form>
                                <form action="../../controller/TopicController.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_topic" value="<?= $topic['id_topic']; ?>">
                                    <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer ce message ?');">Delete</button>
                                </form>
                            <?php endif; ?>   

                            <!-- Comments Section -->
                            <div class="comments-section">
                                <h4>Commentaires</h4>
                                <?php
                                $comments = $forumComment->getCommentsByTopic($topic['id_topic']);
                                if (!empty($comments)): ?>
                                    <?php foreach ($comments as $comment): ?>
                                        <div class="comment">
                                            <?php if (isset($_GET['edit_comment']) && $_GET['edit_comment'] == $comment['id_comment']): ?>
                                                <!-- Edit Comment Form -->
                                                <form action="forum.php" method="POST" onsubmit="return validateEditCommentForm(<?= $comment['id_comment']; ?>);">
                                                    <input type="hidden" name="id_comment" value="<?= $comment['id_comment']; ?>">
                                                    <input type="hidden" name="id_topic" value="<?= $topic['id_topic']; ?>">
                                                    <textarea name="comment_text" id="edit-comment-text-<?= $comment['id_comment']; ?>" rows="2"><?= htmlspecialchars($comment['comment_text']); ?></textarea>
                                                    <div id="edit-comment-error-<?= $comment['id_comment']; ?>" style="color: red; font-size: 0.9em; display: none;"></div>
                                                    <button type="submit" name="edit_comment">Enregistrer</button>
                                                    <a href="forum.php" class="btn-cancel">Annuler</a>
                                                </form>
                                            <?php else: ?>
                                                <!-- Display Comment -->
                                                <p><strong><?= htmlspecialchars($comment['client_name']); ?>:</strong> <?= htmlspecialchars($comment['comment_text']); ?></p>
                                                <p>Posté le : <?= htmlspecialchars($comment['created_at']); ?></p>
                                                <!-- Action Buttons for Comment -->
                                                <form action="forum.php" method="GET" style="display:inline;">
                                                    <input type="hidden" name="edit_comment" value="<?= $comment['id_comment']; ?>">
                                                    <input type="hidden" name="topic_id" value="<?= $topic['id_topic']; ?>">
                                                    <button type="submit" class="edit-button">Éditer</button>
                                                </form>
                                                <form action="forum.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="delete_comment" value="<?= $comment['id_comment']; ?>">
                                                    <button type="submit" class="delete-button" onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire ?');">Supprimer</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>Aucun commentaire pour ce topic.</p>
                                <?php endif; ?>

                            <!-- Add Comment Form -->
                            <form action="forum.php" method="POST" onsubmit="return validateCommentForm(<?= $topic['id_topic']; ?>);">
                                <input type="hidden" name="id_topic" value="<?= htmlspecialchars($topic['id_topic']); ?>">
                                <textarea name="comment_text" id="comment-text-<?= $topic['id_topic']; ?>" placeholder="Écrire un commentaire..."></textarea>
                                <span id="comment-error-<?= $topic['id_topic']; ?>" style="color: red; font-size: 0.9em; display: none;"></span>
                                <button type="submit">Commenter</button>
                            </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($error)) : ?>
                <div class="error-message"><?= $error; ?></div>
            <?php endif; ?>

        <!-- Rating Section -->
<h2>Merci de laisser une note</h2>

<!-- Afficher la note moyenne -->
<p>Note moyenne : <?php echo $average; ?> / 5 (<?php echo $ratingData['number_of_ratings']; ?> avis)</p>

<?php
// Vérifier si l'utilisateur a déjà noté
$hasRated = $forumTopic->hasRated($userEmail);
if ($hasRated):
    // Récupérer les détails de la note existante
    $existingRating = $forumTopic->getRatingByEmail($userEmail);
?>
    <div class="existing-rating">
        <p>Vous avez déjà noté le site. Voici les détails de votre note :</p>
        <div class="rating-details">
            <span class="rating-label">Email :</span>
            <span class="rating-value"><?php echo htmlspecialchars($existingRating['user_email']); ?></span>
        </div>
        <div class="rating-details">
            <span class="rating-label">Note :</span>
            <span class="rating-value"><?php echo htmlspecialchars($existingRating['rating']); ?> / 5</span>
        </div>
        <div class="rating-details">
            <span class="rating-label">Date :</span>
            <span class="rating-value"><?php echo htmlspecialchars($existingRating['created_at']); ?></span>
        </div>
    </div>
<?php else: ?>
    <!-- Formulaire de notation -->
    <form action="../../controller/TopicController.php" method="POST">
        <input type="hidden" name="action" value="rate">
        <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($userEmail); ?>">

        <div class="site-rating">
            <div class="star-rating">
                <input type="radio" id="star1" name="rating" value="5" required>
                <label for="star1">★</label>
                <input type="radio" id="star2" name="rating" value="4">
                <label for="star2">★</label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">★</label>
                <input type="radio" id="star4" name="rating" value="2">
                <label for="star4">★</label>
                <input type="radio" id="star5" name="rating" value="1">
                <label for="star5">★</label>
            </div>
            <div class="rating-text">
                <span>Votre note : </span><span id="rating-output">Aucune</span> / 5
            </div>
            <button type="submit">Donner ma note</button>
        </div>
    </form>

    <script>
        // JavaScript to show the rating dynamically
        const stars = document.querySelectorAll('.star-rating input[type="radio"]');
        const ratingOutput = document.getElementById('rating-output');

        stars.forEach(star => {
            star.addEventListener('change', function() {
                const selectedRating = this.value;
                ratingOutput.textContent = selectedRating;
            });
        });
    </script>
<?php endif; ?>    

    </section>
    <footer>
        <p>© 2025 Green City | All Rights Reserved</p>
    </footer>

    <script>
        // Afficher et masquer automatiquement les barres de succès et d'erreur
        document.addEventListener('DOMContentLoaded', function() {
            const successBar = document.getElementById('success-bar');
            const errorBar = document.getElementById('error-bar');

            if (successBar) {
                successBar.style.display = 'block';
                setTimeout(() => {
                    successBar.style.opacity = '0';
                    setTimeout(() => {
                        successBar.style.display = 'none';
                    }, 500);
                }, 3000);
            }

            if (errorBar) {
                errorBar.style.display = 'block';
                setTimeout(() => {
                    errorBar.style.opacity = '0';
                    setTimeout(() => {
                        errorBar.style.display = 'none';
                    }, 500);
                }, 3000);
            }
        });

        // Validation pour le formulaire d'édition de topic (code existant)
        function validateEditForm() {
            const topicTextInput = document.getElementById('edit-topic-text');
            const editMessageError = document.getElementById('edit-message-error');
            const topicText = topicTextInput.value.trim();

            editMessageError.style.display = 'none';
            editMessageError.textContent = '';

            if (topicText.length < 4) {
                editMessageError.textContent = 'Le message doit contenir au moins 4 caractères.';
                editMessageError.style.display = 'block';
                topicTextInput.focus();
                return false;
            }
            return true;
        }

        // Validation en temps réel pour le formulaire d'édition de topic (code existant)
        document.addEventListener('DOMContentLoaded', function() {
            const topicTextInput = document.getElementById('edit-topic-text');
            if (topicTextInput) {
                topicTextInput.addEventListener('input', function() {
                    const topicText = this.value.trim();
                    const editMessageError = document.getElementById('edit-message-error');

                    if (topicText.length < 4) {
                        editMessageError.textContent = 'Le message doit contenir au moins 4 caractères.';
                        editMessageError.style.display = 'block';
                    } else {
                        editMessageError.style.display = 'none';
                    }
                });
            }
        });

        // Validation pour le formulaire de soumission de message (post-topic-form)
        function validateTopicForm() {
            const messageInput = document.getElementById('message-content');
            const messageError = document.getElementById('message-content-error');
            const messageText = messageInput.value.trim();

            messageError.style.display = 'none';
            messageError.textContent = '';

            let isValid = true;

            if (messageText === '') {
                messageError.textContent = 'Veuillez renseigner ce champ.';
                messageError.style.display = 'block';
                messageInput.focus();
                isValid = false;
            } else if (messageText.length < 4) {
                messageError.textContent = 'Le message doit contenir au moins 4 caractères.';
                messageError.style.display = 'block';
                messageInput.focus();
                isValid = false;
            }

            return isValid;
        }

        // Validation pour le formulaire de commentaire
        function validateCommentForm(topicId) {
            const commentInput = document.getElementById('comment-text-' + topicId);
            const commentError = document.getElementById('comment-error-' + topicId);
            const commentText = commentInput.value.trim();

            commentError.style.display = 'none';
            commentError.textContent = '';

            let isValid = true;

            if (commentText === '') {
                commentError.textContent = 'Veuillez renseigner ce champ.';
                commentError.style.display = 'block';
                commentInput.focus();
                isValid = false;
            }

            return isValid;
        }

        // Validation pour le formulaire d'édition de commentaire
        function validateEditCommentForm() {
            const commentTextInput = document.getElementById('edit-comment-text');
            const editCommentError = document.getElementById('edit-comment-error');
            const commentText = commentTextInput.value.trim();

            editCommentError.style.display = 'none';
            editCommentError.textContent = '';

            if (commentText === '') {
                editCommentError.textContent = 'Le commentaire ne peut pas être vide.';
                editCommentError.style.display = 'block';
                commentTextInput.focus();
                return false;
            }
            return true;
        }

        // Validation en temps réel pour tous les formulaires
        document.addEventListener('DOMContentLoaded', function() {
            const successBar = document.getElementById('success-bar');
            const errorBar = document.getElementById('error-bar');

            if (successBar) {
                successBar.style.display = 'block';
                setTimeout(() => {
                    successBar.style.opacity = '0';
                    setTimeout(() => {
                        successBar.style.display = 'none';
                    }, 500);
                }, 3000);
            }

            if (errorBar) {
                errorBar.style.display = 'block';
                setTimeout(() => {
                    errorBar.style.opacity = '0';
                    setTimeout(() => {
                        errorBar.style.display = 'none';
                    }, 500);
                }, 3000);
            }

            const messageInput = document.getElementById('message-content');
            const postTopicForm = document.getElementById('post-topic-form');

            if (messageInput) {
                messageInput.addEventListener('input', function() {
                    const messageText = this.value.trim();
                    const messageError = document.getElementById('message-content-error');

                    if (messageText === '') {
                        messageError.textContent = 'Veuillez renseigner ce champ.';
                        messageError.style.display = 'block';
                    } else if (messageText.length < 4) {
                        messageError.textContent = 'Le message doit contenir au moins 4 caractères.';
                        messageError.style.display = 'block';
                    } else {
                        messageError.style.display = 'none';
                    }
                });
            }

            if (postTopicForm) {
                postTopicForm.addEventListener('submit', function(event) {
                    if (!validateTopicForm()) {
                        event.preventDefault();
                    }
                });
            }

            const topicTextInput = document.getElementById('edit-topic-text');
            if (topicTextInput) {
                topicTextInput.addEventListener('input', function() {
                    const topicText = this.value.trim();
                    const editMessageError = document.getElementById('edit-message-error');

                    if (topicText.length < 4) {
                        editMessageError.textContent = 'Le message doit contenir au moins 4 caractères.';
                        editMessageError.style.display = 'block';
                    } else {
                        editMessageError.style.display = 'none';
                    }
                });
            }

            <?php foreach ($topics as $topic): ?>
                const commentInput_<?php echo $topic['id_topic']; ?> = document.getElementById('comment-text-<?php echo $topic['id_topic']; ?>');
    
                if (commentInput_<?php echo $topic['id_topic']; ?>) {
                    commentInput_<?php echo $topic['id_topic']; ?>.addEventListener('input', function() {
                        const commentText = this.value.trim();
                        const commentError = document.getElementById('comment-error-<?php echo $topic['id_topic']; ?>');

                        if (commentText === '') {
                            commentError.textContent = 'Veuillez renseigner ce champ.';
                            commentError.style.display = 'block';
                        } else {
                            commentError.style.display = 'none';
                        }
                    });
                }  
            <?php endforeach; ?>

            <?php foreach ($topics as $topic): ?>
                <?php $comments = $forumComment->getCommentsByTopic($topic['id_topic']); ?>
                <?php foreach ($comments as $comment): ?>
                    const editCommentInput_<?php echo $comment['id_comment']; ?> = document.getElementById('edit-comment-text-<?php echo $comment['id_comment']; ?>');
                    if (editCommentInput_<?php echo $comment['id_comment']; ?>) {
                        editCommentInput_<?php echo $comment['id_comment']; ?>.addEventListener('input', function() {
                            const commentText = this.value.trim();
                            const editCommentError = document.getElementById('edit-comment-error-<?php echo $comment['id_comment']; ?>');

                            if (commentText === '') {
                                editCommentError.textContent = 'Le commentaire ne peut pas être vide.';
                                editCommentError.style.display = 'block';
                            } else {
                                editCommentError.style.display = 'none';
                            }
                        });
                    }
                <?php endforeach; ?>
            <?php endforeach; ?>
        });
    </script>
</body>
</html>