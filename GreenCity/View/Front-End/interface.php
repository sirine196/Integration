<?php
session_start();
require "../../config_db.php";



$IdCurrentUser = $_SESSION['IdCurrentUser'] ?? null;
$userInfo = null;

if (isset($IdCurrentUser)) {
    try {
        $userInfo = $conn
            ->prepare("SELECT Nom, Prenom, Role, Img FROM user WHERE Id = :id");
        $userInfo->execute([':id' => $IdCurrentUser]);
        $userInfo = $userInfo->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des données utilisateur : " . $e->getMessage());
    }
}
if (isset($_POST['deconnexion'])) {
    session_destroy(); // Détruire la session pour la déconnexion
    header("Location: user.php"); // Redirection après déconnexion
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green city - Horizon Digital</title>
    <link rel="stylesheet" href="interface.css">
</head>
<body>
    <header>
        <img src="../../Ressources/logo_GreenCity_trans.png" width="20%" alt="" id="logo_header">
        <nav>
            <a href="#">Home</a>
            <a href="./event/events.php">Events</a>
            <a href="#">Forum</a>
            <a href="#">Challenges</a>
            <a href="./map/map.php">Map</a>
            <?php
            if (isset($userInfo['Role']) && $userInfo['Role'] == 'Admin') {
                echo '<a href="../Back-End/dashboard.php">Dashboard</a>';
            }
            ?>
        </nav>
        <a href="modify_profile.php" class="profile-link">
            <img src="../../<?= $userInfo['Img'] ?? 'Ressources/profile.png' ?>" alt="Profile Icon" class="profile-icon">
            <h2> <?= $userInfo['Prenom'] . " " . $userInfo['Nom'] ?></h2>
        </a>
        <form action="" method="post">
            <input type="submit" name="deconnexion" id="deconnexion" value="Deconnexion" style="float:right;color:#123A2D;background-color:#B5FFC1;width:110px ;padding:0.5%;border-radius:25px;border:none;margin-right:15px;font-size:15px">
        </form>
    </header>

    <section class="hero">
        <div class="hero-overlay">
            <div class="hero-content">
                <h2>Let’s Work Together for a Greener City!</h2>
                <p>Join our community to participate in eco-friendly events, exchange ideas, and take on challenges to protect our environment.</p>
                <a href="./event/events.php" class="cta-button">View Events</a>
            </div>
        </div>
    </section>

    <!-- Featured Events Section with Fade-in Animation -->
    <section class="featured-events">
        <h2>Featured Events</h2>
        <div class="event-cards">
            <div class="event-card">
                <img src="../../Ressources/Tunisia_culture.jpg" alt="Event Image" class="event-image">
                <h3 class="event-title">Green Future in Action</h3>
                <p class="event-description">Join us in planting trees for a sustainable tomorrow.</p>
                <a href="#" class="learn-more">Learn More</a>
            </div>
            <div class="event-card">
                <img src="../../Ressources/Sahara.jpg" alt="Event Image" class="event-image">
                <h3 class="event-title">Clean Earth Initiative</h3>
                <p class="event-description">See the impact of a park cleanup-before and after!</p>
                <a href="#" class="learn-more">Learn More</a>
            </div>
            <div class="event-card">
                <img src="../../Ressources/Medina.png" alt="Event Image" class="event-image">
                <h3 class="event-title">Recycle for a Better World</h3>
                <p class="event-description">Discover how small actions make a big difference.</p>
                <a href="#" class="learn-more">Learn More</a>
            </div>
        </div>
    </section>    

    <!-- Most Popular Event Section -->
    <section class="most-booked-event">
        <h2>Most Popular Event</h2>
        <div class="event-highlight">
            <h3>Central Park Cleanup</h3>
            <p>Join our flagship event organized by <strong>Green City</strong> to clean up Central Park and contribute to a healthier environment.</p>
            <a href="./reservation template/index.html" class="learn-more">Book Now</a>
        </div>
    </section>    
    <!-- Complaint Section -->
    <section class="complaint-section">
        <h2>Submit a Complaint</h2>
        <div class="complaint-container">
            <form class="complaint-form">
                <label for="complaint-title">Title</label>
                <input type="text" id="complaint-title" name="complaint-title" required>
    
                <label for="complaint-description">Description</label>
                <textarea id="complaint-description" name="complaint-description" rows="4" required></textarea>
    
                <label for="complaint-email">Your Email</label>
                <input type="email" id="complaint-email" name="complaint-email" required>
    
                <button type="submit" class="complaint-submit">Submit</button>
            </form>
        </div>
    </section>
    

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Green City - Horizon Digital | All Rights Reserved</p>
    </footer>
</body>
</html>
