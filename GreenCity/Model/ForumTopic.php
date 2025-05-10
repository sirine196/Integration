<?php
require_once "C:/xampp/htdocs/GreenCity_V1/config_db.php";

class ForumTopic {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Créer un nouveau message
    public function createTopic($id_client, $topic_text) {
        $query = "INSERT INTO topics (id_client, topic_text, created_at, status) 
                  VALUES (:id_client, :topic_text, NOW(), 'En Attente')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_client', $id_client, PDO::PARAM_STR);
        $stmt->bindParam(':topic_text', $topic_text, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Récupérer tous les topics ou messages
    public function getAllTopics() {
        $query = "SELECT t.*, CONCAT(u.Nom, ' ', u.Prenom) AS client_name 
                  FROM topics t 
                  LEFT JOIN user u ON CAST(t.id_client AS UNSIGNED) = u.Id 
                  WHERE t.status = 'Confirmé' OR t.status = 'En Attente' 
                  ORDER BY t.is_pinned DESC, t.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les topics ou messages avec leur statut
    public function getAllTopicsWithStatus() {
        $query = "SELECT t.*, CONCAT(u.Nom, ' ', u.Prenom) AS client_name 
                  FROM topics t 
                  LEFT JOIN user u ON t.id_client = u.Id 
                  ORDER BY t.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un message par ID
    public function getTopicById($id_topic) {
        $query = "SELECT t.*, CONCAT(u.Nom, ' ', u.Prenom) AS client_name 
                  FROM topics t 
                  LEFT JOIN user u ON t.id_client = u.Id 
                  WHERE t.id_topic = :id_topic";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_topic', $id_topic, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour un message
    public function updateTopic($id_topic, $topic_text) {
        $query = "UPDATE topics SET topic_text = :topic_text WHERE id_topic = :id_topic";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':topic_text', $topic_text, PDO::PARAM_STR);
        $stmt->bindParam(':id_topic', $id_topic, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Supprimer un message
    public function deleteTopic($id_topic) {
        $query = "DELETE FROM topics WHERE id_topic = :id_topic";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_topic', $id_topic, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Confirmer un topic
    public function confirmTopic($id_topic) {
        $query = "UPDATE topics SET status = 'Confirmé' WHERE id_topic = :id_topic";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_topic', $id_topic, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTopicsSorted($search = '', $sortBy = 'created_at', $sortOrder = 'ASC') {
        $query = "SELECT t.*, CONCAT(u.Nom, ' ', u.Prenom) AS client_name 
                  FROM topics t 
                  LEFT JOIN user u ON t.id_client = u.Id 
                  WHERE 1";
        if ($search) {
            $query .= " AND t.id_client LIKE :search";
        }
        $query .= " ORDER BY $sortBy $sortOrder";
        $stmt = $this->conn->prepare($query);
        if ($search) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifier si l'utilisateur a déjà noté le site
    public function hasRated($userEmail) {
        $query = "SELECT COUNT(*) FROM site_ratings WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_email', $userEmail, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        error_log("hasRated - Email: " . $userEmail . ", Count: " . $count);
        return $count > 0;
    }

    // Récupérer les détails de la note existante
    public function getRatingByEmail($userEmail) {
        $query = "SELECT rating, user_email, created_at FROM site_ratings WHERE user_email = :user_email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_email', $userEmail, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("getRatingByEmail - Email: " . $userEmail . ", Result: " . json_encode($result));
        return $result;
    }

    // Ajouter une note pour l'utilisateur
    public function addRating($rating, $user_email) {
        try {
            if ($this->hasRated($user_email)) {
                error_log("addRating - Échec: L'utilisateur a déjà noté. Email: " . $user_email);
                return false; // On bloque la soumission si une note existe déjà
            }
            $query = "INSERT INTO site_ratings (user_email, rating, created_at) VALUES (:user_email, :rating, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result) {
                error_log("addRating - Succès: Note ajoutée pour Email: " . $user_email . ", Rating: " . $rating);
                $this->updateSiteRating(); // Mettre à jour la note moyenne après ajout
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout de la note : " . $e->getMessage());
            return false;
        }
    }

    // Mettre à jour la note moyenne du site
    public function updateSiteRating() {
        try {
            $query = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS rating_count FROM site_ratings";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $avgRating = $result['avg_rating'];
            $ratingCount = $result['rating_count'];

            $checkQuery = "SELECT COUNT(*) FROM site_settings WHERE id = 1";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();

            if ($count == 0) {
                $insertQuery = "INSERT INTO site_settings (id, site_rating, rating_count) VALUES (1, :site_rating, :rating_count)";
                $stmtInsert = $this->conn->prepare($insertQuery);
                $stmtInsert->execute([
                    'site_rating' => $avgRating,
                    'rating_count' => $ratingCount
                ]);
            } else {
                $updateQuery = "UPDATE site_settings SET site_rating = :site_rating, rating_count = :rating_count WHERE id = 1";
                $stmtUpdate = $this->conn->prepare($updateQuery);
                $stmtUpdate->execute([
                    'site_rating' => $avgRating,
                    'rating_count' => $ratingCount
                ]);
            }
        } catch (PDOException $e) {
            error_log("Erreur dans updateSiteRating : " . $e->getMessage());
            header("Location: ../view/Front-End/forum.php?error=Erreur lors de la mise à jour de la note moyenne.");
            exit;
        }
    }

    public function getSiteRating() {
        $query = "SELECT site_rating FROM site_settings WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_null($result['site_rating']) ? 'Non noté' : round($result['site_rating'], 2);
    }

    // Récupérer la moyenne des notes
    public function getAverageRating() {
        try {
            $query = "SELECT AVG(rating) AS average_rating, COUNT(*) AS number_of_ratings FROM site_ratings WHERE rating > 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors du calcul de la moyenne : " . $e->getMessage());
            return null;
        }
    }

    // Mettre à jour le nombre de commentaires
    public function updateCommentCount($id_topic) {
        try {
            $query = "SELECT COUNT(*) FROM comments WHERE id_topic = :id_topic";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_topic', $id_topic, PDO::PARAM_INT);
            $stmt->execute();
            $comment_count = $stmt->fetchColumn();

            $update_query = "UPDATE topics SET comment_count = :comment_count WHERE id_topic = :id_topic";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':comment_count', $comment_count, PDO::PARAM_INT);
            $update_stmt->bindParam(':id_topic', $id_topic, PDO::PARAM_INT);
            return $update_stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du nombre de commentaires : " . $e->getMessage());
            return false;
        }
    }

    // Épingler ou désépingler un topic
    public function togglePin($id_topic) {
        try {
            $topic = $this->getTopicById($id_topic);
            if (!$topic) {
                return false;
            }
            $new_status = $topic['is_pinned'] ? 0 : 1;

            $query = "UPDATE topics SET is_pinned = :is_pinned WHERE id_topic = :id_topic";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':is_pinned', $new_status, PDO::PARAM_BOOL);
            $stmt->bindParam(':id_topic', $id_topic, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'état épinglé : " . $e->getMessage());
            return false;
        }
    }
}
?>