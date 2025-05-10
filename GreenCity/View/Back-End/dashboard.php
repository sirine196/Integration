<?php
require "../../Controller/userController.php";
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../model/ForumTopic.php';
require_once '../../model/ForumComment.php';

// Initialisation Topic et Comment
$forumTopic = new ForumTopic();
$forumComment = new ForumComment();

// Connexion à la base de données
$database = new Database();
$pdo = $database->getConnection();

// Récupérer la recherche et le tri pour les utilisateurs
$userSearch = isset($_GET['user_search']) ? $_GET['user_search'] : '';
$userSortBy = isset($_GET['user_sort_by']) ? $_GET['user_sort_by'] : 'Id';

// Récupérer tous les utilisateurs avec recherche et tri
$userQuery = "SELECT * FROM user";
if ($userSearch) {
    $userQuery .= " WHERE Id LIKE :user_search OR Nom LIKE :user_search OR Prenom LIKE :user_search";
}
$userQuery .= " ORDER BY $userSortBy ASC";
$userStmt = $pdo->prepare($userQuery);
if ($userSearch) {
    $userStmt->bindValue(':user_search', '%' . $userSearch . '%');
}
$userStmt->execute();
$allUsers = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la recherche et le tri pour les topics
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at';
$selectedTopic = isset($_GET['selected_topic']) ? (int)$_GET['selected_topic'] : null;

// Statistiques : Nombre de topics et commentaires par client
$statsQuery = "
    SELECT 
        t.id_client,
        CONCAT(u.Nom, ' ', u.Prenom) AS client_name,
        COUNT(DISTINCT t.id_topic) AS topic_count,
        COUNT(c.id_comment) AS comment_count
    FROM topics t
    LEFT JOIN comments c ON t.id_topic = c.id_topic
    LEFT JOIN user u ON CAST(t.id_client AS UNSIGNED) = u.Id
    GROUP BY t.id_client
    HAVING topic_count > 0
    ORDER BY topic_count DESC, comment_count DESC
";
$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute();
$clientStats = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer la somme maximale des topics et commentaires par client
$maxSomme = 0;
foreach ($clientStats as $stat) {
    $somme = $stat['topic_count'] + $stat['comment_count'];
    $maxSomme = max($maxSomme, $somme);
}
$maxCount = $maxSomme + 1; // Ajouter 1 à la somme maximale pour l'axe Y
$maxCount = $maxCount ?: 1; // Éviter la division par zéro

// Gérer les actions de confirmation, suppression et épinglage pour topics et commentaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_topic'])) {
        $id_topic = (int)$_POST['id_topic'];
        $forumTopic->confirmTopic($id_topic);
    } elseif (isset($_POST['delete_topic'])) {
        $id_topic = (int)$_POST['id_topic'];
        $forumTopic->deleteTopic($id_topic);
    } elseif (isset($_POST['delete_comment'])) {
        $id_comment = (int)$_POST['id_comment'];
        $forumComment->deleteComment($id_comment);
    } elseif (isset($_POST['toggle_pin'])) {
        $id_topic = (int)$_POST['id_topic'];
        $forumTopic->togglePin($id_topic);
    }
    // Rafraîchir la page après action
    header("Location: dashboard.php?search=" . urlencode($search) . "&sort_by=" . urlencode($sortBy) . (isset($_GET['selected_topic']) ? "&selected_topic=" . urlencode($_GET['selected_topic']) : "") . "&user_search=" . urlencode($userSearch) . "&user_sort_by=" . urlencode($userSortBy));
    exit;
}

// Récupérer les topics avec leurs commentaires associés via une jointure
$query = "
    SELECT 
        t.id_topic, t.id_client AS topic_client, CONCAT(u.Nom, ' ', u.Prenom) AS client_name, 
        t.topic_text, t.created_at AS topic_created_at, t.status AS topic_status, t.is_pinned,
        c.id_comment, c.client_name AS comment_client_name, c.comment_text, 
        c.created_at AS comment_created_at, c.status AS comment_status, c.sentiment
    FROM topics t
    LEFT JOIN user u ON CAST(t.id_client AS UNSIGNED) = u.Id
    LEFT JOIN comments c ON t.id_topic = c.id_topic
";

if ($search) {
    $query .= " WHERE (u.Nom LIKE :search OR u.Prenom LIKE :search OR t.id_client LIKE :search)";
}

if ($sortBy === 'status') {
    $query .= " ORDER BY t.is_pinned DESC, CASE t.status WHEN 'En Attente' THEN 0 ELSE 1 END, t.status ASC, c.created_at DESC";
} else {
    $query .= " ORDER BY t.is_pinned DESC, t.$sortBy DESC, c.created_at DESC";
}
$stmt = $pdo->prepare($query);

if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction simple pour analyser le sentiment (à remplacer par une API en production)
function analyzeSentiment($text) {
    $text = strtolower(trim($text));
    $positiveWords = ['bon', 'super', 'bien', 'good', 'salutt', 'hello', 'yes', 'up', 'nice'];
    $negativeWords = ['mauvais', 'nul', 'pire', 'mal'];
    
    if (strpos($text, 'delete') !== false) return 'négatif';
    foreach ($positiveWords as $word) {
        if (strpos($text, $word) !== false) return 'positif';
    }
    foreach ($negativeWords as $word) {
        if (strpos($text, $word) !== false) return 'négatif';
    }
    return 'neutre';
}

// Regrouper les données pour faciliter l'affichage (topics avec leurs commentaires)
$topicsWithComments = [];
$sentimentCounts = ['positif' => 0, 'négatif' => 0, 'neutre' => 0];
foreach ($results as $row) {
    $topicId = $row['id_topic'];
    if (!isset($topicsWithComments[$topicId])) {
        $topicsWithComments[$topicId] = [
            'id_topic' => $row['id_topic'],
            'id_client' => $row['topic_client'],
            'client_name' => $row['client_name'],
            'topic_text' => $row['topic_text'],
            'created_at' => $row['topic_created_at'],
            'status' => $row['topic_status'],
            'is_pinned' => isset($row['is_pinned']) ? $row['is_pinned'] : false,
            'comments' => []
        ];
    }
    if ($row['id_comment']) {
        $sentiment = $row['sentiment'] ?? analyzeSentiment($row['comment_text']);
        $topicsWithComments[$topicId]['comments'][] = [
            'id_comment' => $row['id_comment'],
            'client_name' => $row['comment_client_name'],
            'comment_text' => $row['comment_text'],
            'created_at' => $row['comment_created_at'],
            'status' => $row['comment_status'],
            'sentiment' => $sentiment
        ];
        if ($selectedTopic === null || $selectedTopic == $topicId) {
            $sentimentCounts[$sentiment]++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green City - Dashboard</title>
    <link rel="stylesheet" href="back.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="../../Ressources/logo_GreenCity_trans.png" width="80%" alt="GreenCity Logo">
        </div>
        <nav class="sidebar-nav">
            <a href="../Front-End/interface.php" class="nav-item">Home</a>
            <a href="../event/events.php" class="nav-item">Events</a>
            <a href="../Front-End/forum.php" class="nav-item">Forum</a>
            <a href="#" class="nav-item">Challenges</a>
            <a href="../map/map.php" class="nav-item">Map</a>
            <a href="dashboard.php" class="nav-item active">Dashboard</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="main-header">
            <h1>Dashboard</h1>
            <div class="header-actions">
                <a href="../Front-End/modify_profile.php" class="profile-link">
                    <img src="../../<?= $userInfo['Img'] ?? 'Ressources/profile.png' ?>" alt="Profile Icon" class="profile-icon">
                    <?php
                        echo '<h2>'. htmlspecialchars($userInfo['Prenom'] . " " . $userInfo['Nom']) .'</h2>';
                    ?>
                </a>
            </div>
        </header>

        <section class="dashboard-hero">
            <div class="dashboard-overlay">
                <div class="dashboard-content">
                    <h2>Welcome to Your GreenCity Dashboard!</h2>
                    <p>Track your eco-friendly activities, events, and contributions to a greener future.</p>
                </div>
            </div>
        </section>

        <!-- USERS -->
        <div class="dashboard-panels">
            <div class="panel forum-admin">
                <h3>Utilisateurs GreenCity</h3>
                <!-- Formulaire de recherche et tri pour les utilisateurs -->
                <form method="GET" action="dashboard.php" class="search-form">
                    <input type="text" name="user_search" class="search-input" placeholder="Rechercher un utilisateur (Id, Nom, Prenom)" value="<?php echo htmlspecialchars($userSearch); ?>" />
                    <button type="submit" class="search-button">Rechercher</button>
                    <!-- Sélecteur de tri -->
                    <select name="user_sort_by" class="sort-select">
                        <option value="Id" <?php echo ($userSortBy == 'Id') ? 'selected' : ''; ?>>Par ID</option>
                        <option value="Nom" <?php echo ($userSortBy == 'Nom') ? 'selected' : ''; ?>>Par Nom</option>
                        <option value="Prenom" <?php echo ($userSortBy == 'Prenom') ? 'selected' : ''; ?>>Par Prénom</option>
                        <option value="Mail" <?php echo ($userSortBy == 'Mail') ? 'selected' : ''; ?>>Par Mail</option>
                        <option value="Role" <?php echo ($userSortBy == 'Role') ? 'selected' : ''; ?>>Par Rôle</option>
                    </select>
                    <button type="submit" class="sort-button">Trier</button>
                    <!-- Champs cachés pour conserver les paramètres des topics -->
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="sort_by" value="<?php echo htmlspecialchars($sortBy); ?>">
                    <input type="hidden" name="selected_topic" value="<?php echo htmlspecialchars($selectedTopic ?? ''); ?>">
                </form>
                <table class="forum-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Mail</th>
                            <th>Rôle</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($allUsers as $row) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['Id']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Nom']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Prenom']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Mail']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Role']) . '</td>';
                            echo '<td>';
                            
                            if ($row['Role'] != "Admin") {
                                if ($row['Status'] == "En Attente") { 
                                    echo '<form method="post" style="display:inline;">
                                        <input type="hidden" name="confirm" value="' . $row['Id'] . '">
                                        <input type="submit" style="background-color:green;border:white;color:white;padding:5%;border-radius:20px;" value="Confirmer">
                                    </form>';
                                } else {
                                    echo '<form method="post" style="display:inline;">
                                        <input type="hidden" name="defin_admin" value="' . $row['Id'] . '">
                                        <input type="submit" style="background-color:rgb(199, 163, 84);border:white;color:white;padding:5%;border-radius:20px;" value="Définir Admin">
                                    </form>';
                                }
                            } else {
                                echo '<form method="post" style="display:inline;">
                                    <input type="hidden" name="remove_admin" value="' . $row['Id'] . '">
                                    <input type="submit" style="background-color:grey;border:white;color:white;padding:5%;border-radius:20px;" value="Supprimer des Admins">
                                </form>';
                            }

                            echo '<form method="post" style="display:inline;">
                                <input type="hidden" name="delete_id" value="' . $row['Id'] . '">
                                <input type="submit" style="background-color:red;border:white;color:white;padding:5%;border-radius:20px;" value="Supprimer">
                            </form>';

                            echo '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FORUMS -->
        <h1 style="color: white;">Management of topics and comments</h1>
        <form method="GET" action="dashboard.php" class="search-form">
            <input type="text" name="search" class="search-input" placeholder="Rechercher par nom de client" value="<?php echo htmlspecialchars($search); ?>" />
            <button type="submit" class="search-button">Rechercher</button>
            <!-- Sélecteur de tri -->
            <select name="sort_by" class="sort-select">
                <option value="created_at" <?php echo ($sortBy == 'created_at') ? 'selected' : ''; ?>>Par Date</option>
                <option value="status" <?php echo ($sortBy == 'status') ? 'selected' : ''; ?>>Par Statut</option>
                <option value="id_topic" <?php echo ($sortBy == 'id_topic') ? 'selected' : ''; ?>>Par ID</option>
            </select>
            <button type="submit" class="sort-button">Trier</button>
            <!-- Champ caché pour le topic sélectionné -->
            <input type="hidden" name="selected_topic" id="selected_topic_input" value="<?php echo htmlspecialchars($selectedTopic ?? ''); ?>">
            <!-- Champs cachés pour conserver les paramètres des utilisateurs -->
            <input type="hidden" name="user_search" value="<?php echo htmlspecialchars($userSearch); ?>">
            <input type="hidden" name="user_sort_by" value="<?php echo htmlspecialchars($userSortBy); ?>">
        </form>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID Topic</th>
                    <th>Nom Client</th>
                    <th>Message</th>
                    <th>Créé le</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topicsWithComments as $topic): ?>
                    <!-- Ligne pour le topic -->
                    <tr class="topic-row <?php echo $topic['is_pinned'] ? 'pinned' : ''; ?> <?php echo ($selectedTopic == $topic['id_topic']) ? 'selected' : ''; ?>" data-topic-id="<?php echo htmlspecialchars($topic['id_topic']); ?>">
                        <td><?= htmlspecialchars($topic['id_topic']); ?></td>
                        <td><?= htmlspecialchars($topic['client_name'] ?? $topic['id_client']); ?></td>
                        <td><?= htmlspecialchars($topic['topic_text']); ?></td>
                        <td><?= htmlspecialchars($topic['created_at']); ?></td>
                        <td>
                            <?= htmlspecialchars($topic['status']); ?>
                            <?php if ($topic['is_pinned']): ?>
                                <span style="color: #28a745; margin-left: 5px;">(Épinglé 📍)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($topic['status'] === 'En Attente'): ?>
                                <form action="dashboard.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_topic" value="<?= $topic['id_topic']; ?>">
                                    <button type="submit" name="confirm_topic" class="confirm">Confirmer</button>
                                </form>
                            <?php endif; ?>
                            <form action="dashboard.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id_topic" value="<?= $topic['id_topic']; ?>">
                                <button type="submit" name="delete_topic" class="delete">Supprimer</button>
                            </form>
                            <!-- Bouton pour épingler/désépingler -->
                            <form action="dashboard.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id_topic" value="<?= $topic['id_topic']; ?>">
                                <button type="submit" name="toggle_pin" class="pin-button" title="<?php echo $topic['is_pinned'] ? 'Désépingler' : 'Épingler'; ?>">
                                    <?php echo $topic['is_pinned'] ? '📍' : '📌'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <!-- Lignes pour les commentaires associés -->
                    <?php if (!empty($topic['comments'])): ?>
                        <?php foreach ($topic['comments'] as $comment): ?>
                            <tr class="comment-row">
                                <td class="comment-icon"><?= htmlspecialchars($comment['id_comment']); ?> (Commentaire)</td>
                                <td><?= htmlspecialchars($comment['client_name']); ?></td>
                                <td><?= htmlspecialchars($comment['comment_text']); ?></td>
                                <td><?= htmlspecialchars($comment['created_at']); ?></td>
                                <td><?= htmlspecialchars($comment['status']); ?> (Sentiment: <?= htmlspecialchars($comment['sentiment']); ?>)</td>
                                <td>
                                    <form action="dashboard.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_comment" value="<?= $comment['id_comment']; ?>">
                                        <button type="submit" name="delete_comment" class="delete">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="no-comment-row">
                            <td colspan="6">Aucun commentaire pour ce topic.</td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Statistiques sous forme de graphique en barres -->
        <section class="stats-section">
            <h2>Statistiques des Clients</h2>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color legend-topics"></span>
                    <span>Topics</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-comments"></span>
                    <span>Commentaires</span>
                </div>
            </div>
            <div class="stats-chart-wrapper">
                <div class="chart-y-axis">
                    <?php for ($i = ceil($maxCount); $i >= 0; $i--): ?>
                        <div class="y-tick"><?php echo $i; ?></div>
                    <?php endfor; ?>
                </div>
                <div class="stats-chart">
                    <?php foreach ($clientStats as $stat): ?>
                        <div class="chart-bar-group">
                            <div class="bars-container">
                                <!-- Barre pour le nombre de topics (bleu) -->
                                <div class="chart-bar chart-bar-topics" 
                                     style="height: <?php echo ($stat['topic_count'] / $maxCount) * 150; ?>px;"
                                     title="Topics: <?php echo htmlspecialchars($stat['topic_count']); ?>">
                                    <span class="bar-value"><?php echo htmlspecialchars($stat['topic_count']); ?></span>
                                </div>
                                <!-- Barre pour le nombre de commentaires (vert) -->
                                <div class="chart-bar chart-bar-comments" 
                                     style="height: <?php echo ($stat['comment_count'] / $maxCount) * 150; ?>px;"
                                     title="Commentaires: <?php echo htmlspecialchars($stat['comment_count']); ?>">
                                    <span class="bar-value"><?php echo htmlspecialchars($stat['comment_count']); ?></span>
                                </div>
                            </div>
                            <div class="chart-label"><?php echo htmlspecialchars($stat['client_name'] ?? $stat['id_client']); ?></div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($clientStats)): ?>
                        <p class="no-data">Aucune activité pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Sentiment des Commentaires -->
        <section class="sentiment-section">
            <h2>Sentiment des Commentaires <?php echo $selectedTopic ? 'du Topic ' . htmlspecialchars($selectedTopic) : '(Sélectionnez un topic)'; ?></h2>
            <?php
            $total = $sentimentCounts['positif'] + $sentimentCounts['négatif'] + $sentimentCounts['neutre'];
            $positivePercent = $total > 0 ? ($sentimentCounts['positif'] / $total) * 100 : 0;
            $negativePercent = $total > 0 ? ($sentimentCounts['négatif'] / $total) * 100 : 0;
            $neutralPercent = $total > 0 ? ($sentimentCounts['neutre'] / $total) * 100 : 0;
            ?>
            <div class="bar-chart">
                <div class="bar-chart-container">
                    <div class="bar-segment negative-segment" style="width: <?php echo $negativePercent; ?>%;">
                        <?php if ($negativePercent > 0): ?>😞<?php endif; ?>
                    </div>
                    <div class="bar-segment neutral-segment" style="width: <?php echo $neutralPercent; ?>%;">
                        <?php if ($neutralPercent > 0): ?>😐<?php endif; ?>
                    </div>
                    <div class="bar-segment positive-segment" style="width: <?php echo $positivePercent; ?>%;">
                        <?php if ($positivePercent > 0): ?>😊<?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="sentiment-legend">
                <div class="legend-item">
                    <span class="legend-color sentiment-positive"></span>
                    <span>Positif (<?php echo $sentimentCounts['positif']; ?> - <?php echo number_format($positivePercent, 1); ?>%)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color sentiment-negative"></span>
                    <span>Négatif (<?php echo $sentimentCounts['négatif']; ?> - <?php echo number_format($negativePercent, 1); ?>%)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color sentiment-neutral"></span>
                    <span>Neutre (<?php echo $sentimentCounts['neutre']; ?> - <?php echo number_format($neutralPercent, 1); ?>%)</span>
                </div>
            </div>
        </section>
    </div>

    <!-- Script pour gérer la sélection de topic -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const topicRows = document.querySelectorAll('.topic-row');
            topicRows.forEach(row => {
                row.addEventListener('click', function() {
                    const topicId = this.getAttribute('data-topic-id');
                    const url = new URL(window.location.href);
                    url.searchParams.set('selected_topic', topicId);
                    window.location.href = url.toString();
                });
            });
        });
    </script>
</body>
</html>