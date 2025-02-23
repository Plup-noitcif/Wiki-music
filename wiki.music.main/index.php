<?php
session_start();

include("./include/functions.php");
$role = $_SESSION['role'] ?? 'user'; // Définit un rôle par défaut si non défini

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="wiki Music, votre répértoire musicale collaboratif.">
  <title> wiki Music </title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/nav-bar.css">
</head>
<body>
   <!-- Menu de navigation -->
   <header class="nav">
    <div class="container">
      <h1 class="logo"></h1>
      <nav>
        <ul>
          <li><a href="#">Accueil</a></li>
          <li><a href="recherche.php">Rechercher</a></li>
          <?php if (isset($_SESSION['username'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
              <li><a href="messagerie_admin.php">Messagerie Admin</a></li>
            <?php endif; ?>
            <li><a href="messagerie.php">Messagerie</a></li>
            <li><a href="add_music.php">Ajouter</a></li>
            <li><a href="#">Sessions</a></li>
            <li><a href="contributions.php">Contributions</a></li>
            <li><a href="logout.php">Se déconnecter</a></li>
           <?php else: ?>
            <li><a href="connexion.php">Se connecter</a></li>
         <?php endif; ?>
        </ul>
      </nav>
    </div>
    <?php
      if (isset($_SESSION['username'])) {
        echo "<p>Bienvenue, " . htmlspecialchars($_SESSION['username']) . " ! ";
      }
    ?>
  </header>
  <!-- Section principale -->
   <?php
  include("./include/db.php");
  ?>
  <div id="home">
    <header>
      <h1>Bienvenue sur wiki Music</h1>
      <p>Explorez l'univers musical, découvrez des artistes, musiques et albums à travers une plateforme interactive et collaborative.</p>
    </header>
    
    <section id="artists">
      <h2>Artistes populaires</h2>
      <div id="artist-list">
      </div>
    </section>

    <section id="albums">
      <h2>Albums récents</h2>
      <div id="album-list">
      </div>
    </section>

    <section id="sessions">
      <h2>Sessions collaboratives</h2>
      <p>Participez à des sessions collaboratives et contribuez à l'enrichissement de wiki Music.</p>
    </section>
  </div>

  <!-- Pied de page -->
  <footer>
    <p>&copy; 2025 wiki Music.</p>
  </footer>

  <script src=""></script> <!-- JavaScript -->
</body>
</html>
