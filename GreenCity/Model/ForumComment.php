<?php
require_once __DIR__ . '/../config_db.php';
require_once __DIR__ . '/ForumTopic.php';

class ForumComment {
    private $conn;
    private $forumTopic;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->forumTopic = new ForumTopic();
    }

    // Create a comment
    public function createComment($id_topic, $id_client, $client_name, $comment_text) {
        try {
            $query = "INSERT INTO comments (id_topic, id_client, client_name, comment_text, status, created_at) 
                      VALUES (:id_topic, :id_client, :client_name, :comment_text, 'Confirmé', NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_topic', $id_topic, PDO::PARAM_INT);
            $stmt->bindParam(':id_client', $id_client, PDO::PARAM_STR);
            $stmt->bindParam(':client_name', $client_name, PDO::PARAM_STR);
            $stmt->bindParam(':comment_text', $comment_text, PDO::PARAM_STR);
            
            $result = $stmt->execute();
            
            $this->forumTopic->updateCommentCount($id_topic);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du commentaire : " . $e->getMessage());
            return false;
        }
    }

    // Get comments by topic ID with status
    public function getCommentsByTopic($id_topic) {
        try {
            $query = "SELECT id_comment, id_topic, id_client, client_name, comment_text, created_at, status 
                      FROM comments 
                      WHERE id_topic = :id_topic 
                      ORDER BY created_at ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_topic', $id_topic, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires : " . $e->getMessage());
            return [];
        }
    }

    // Get all comments with status
    public function getAllCommentsWithStatus() {
        try {
            $query = "SELECT id_comment, id_topic, id_client, client_name, comment_text, created_at, status 
                      FROM comments 
                      ORDER BY created_at DESC";
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires : " . $e->getMessage());
            return [];
        }
    }

    // Get a comment by its ID
    public function getCommentById($id_comment) {
        try {
            $query = "SELECT id_comment, id_topic, id_client, client_name, comment_text, created_at, status 
                      FROM comments 
                      WHERE id_comment = :id_comment";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_comment', $id_comment, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du commentaire : " . $e->getMessage());
            return null;
        }
    }

    // Delete a comment by ID
    public function deleteComment($id_comment) {
        try {
            $query = "SELECT id_topic FROM comments WHERE id_comment = :id_comment";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_comment', $id_comment, PDO::PARAM_INT);
            $stmt->execute();
            $id_topic = $stmt->fetchColumn();

            $query = "DELETE FROM comments WHERE id_comment = :id_comment";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_comment', $id_comment, PDO::PARAM_INT);
            $result = $stmt->execute();

            $this->forumTopic->updateCommentCount($id_topic);
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du commentaire : " . $e->getMessage());
            return false;
        }
    }

    // Update a comment by ID
    public function updateComment($id_comment, $comment_text) {
        try {
            $query = "UPDATE comments SET comment_text = :comment_text, updated_at = NOW() WHERE id_comment = :id_comment";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':comment_text', $comment_text, PDO::PARAM_STR);
            $stmt->bindParam(':id_comment', $id_comment, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du commentaire : " . $e->getMessage());
            return false;
        }
    }

    // Cancel a comment
    public function cancelComment($id_comment) {
        try {
            $query = "UPDATE comments SET status = 'Annulé' WHERE id_comment = :id_comment";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_comment', $id_comment, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de l'annulation du commentaire : " . $e->getMessage());
            return false;
        }
    }
}
?>