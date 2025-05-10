<?php
require_once "C:/xampp/htdocs/GreenCity_V1/config_db.php";

class RatingModel {
    private $conn;

    public function __construct() {
        $database = new Database(); // Create an instance of the Database class
        $this->conn = $database->getConnection(); // Get the connection object
    }

    public function getAverageRating() {
        // Assurez-vous que $conn est bien initialisé avant d'exécuter la requête
        if ($this->conn !== null) {
            $stmt = $this->conn->query("SELECT AVG(rating) AS average_rating, COUNT(*) AS number_of_ratings FROM site_ratings");
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne la moyenne des notes et le nombre de notes
        } else {
            echo "Erreur : Connexion à la base de données non établie.";
        }
    }
}
?>