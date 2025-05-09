<?php
require "../../Controller/userController.php";

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
            <a href="#" class="nav-item">Forum</a>
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
                <span class="date-range">March 10, 2025 - March 31, 2025</span>
                <a href="../Front-End/modify_profile.php" class="profile-link">
                    <img src="../../<?= $userInfo['Img'] ?? 'Ressources/profile.png' ?>" alt="Profile Icon" class="profile-icon">
                    <?php
                        echo'<h2>'. $userInfo['Prenom'] ." ". $userInfo['Nom'] .'</h2>';
                    ?>
                </a>
            </div>
        </header>
       

        <section class="dashboard-section">
            <div class="dashboard-grid">
                <!-- Cards -->
                <div class="dashboard-card">
                    <h3>Earnings (Monthly)</h3>
                    <p class="dashboard-value">$40,000</p>
                    <a href="#" class="cta-button">View Report</a>
                </div>
                <div class="dashboard-card">
                    <h3>Earnings (Annual)</h3>
                    <p class="dashboard-value">$215,000</p>
                    <a href="#" class="cta-button">View Report</a>
                </div>
                <div class="dashboard-card">
                    <h3>Task Completion</h3>
                    <p class="dashboard-value">24</p>
                    <a href="#" class="cta-button">View Tasks</a>
                </div>
                <div class="dashboard-card">
                    <h3>Pending Requests</h3>
                    <p class="dashboard-value">17</p>
                    <a href="#" class="cta-button">View Requests</a>
                </div>
            </div>

            <!-- Forum Admin Dashboard and Progress Tracker -->
            <div class="dashboard-panels">
                <div class="panel forum-admin">
                <h3>Utilisateurs GreenCity</h3>
                <form action="" method="POST">
                    <input type="search" name="recherche" id="recherche" style="padding:0.8%;border-radius:25px;width:100%;" placeholder="Rechercher un utilisateur (Id, nom ou prenom)...">
                </form>
                    <table class="forum-table">
                        <thead>
                            <tr>
                                <th><a href="?order_by=Id" class="tri">Id</a></th>
                                <th><a href="?order_by=Nom" class="tri">Nom</a></th>
                                <th><a href="?order_by=Prenom"class="tri">Prenom</a></th>
                                <th>Mail</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($allUsers as $row) {
                                    echo '<tr>';
                                    echo '<td>' . $row['Id'] . '</td>';
                                    echo '<td>' . $row['Nom'] . '</td>';
                                    echo '<td>' . $row['Prenom'] . '</td>';
                                    echo '<td>' . $row['Mail'] . '</td>';
                                    echo '<td>' . $row['Role'] . '</td>
                                    <td>';
                                    
                                    if($row['Role'] != "Admin") {
                                        if($row['Status'] == "En Attente") { 
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

            <div class="dashboard-charts">
                <div class="chart-card">
                    <h3>Earnings Breakdown</h3>
                    <div class="chart-placeholder">Chart Placeholder</div>
                </div>
                <div class="chart-card">
                    <h3>Monthly Revenue</h3>
                    <div class="chart-placeholder">Chart Placeholder</div>
                </div>
            </div>
        </section>
    </div>

    <footer>
        <p>© 2025 Green City - Horizon Digital | All Rights Reserved</p>
    </footer>
</body>
</html>
