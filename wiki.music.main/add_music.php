<?php
session_start();
include("./include/functions.php");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="wiki Music, votre répértoire musicale collaboratif.">
  <title> Ajouter </title>
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
          <li><a href="index.php">Accueil</a></li>
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
  <?php
    include("./include/db.php");
  ?>
  <h2>Ajouter une musique</h2>
    <form action="ajouter.php" method="post">
        <label for="title">Titre :</label>
        <input type="text" id="title" name="title" required>

        <label for="artist">Artiste :</label>
        <input type="text" id="artist" name="artist" required>

        <label for="album">Album :</label>
        <input type="text" id="album" name="album" required>

        <label for="release_year">Année de sortie :</label>
        <input type="number" id="release_year" name="release_year" required>

        <label for="lyrics">Paroles :</label>
        <textarea id="lyrics" name="lyrics"></textarea>

        <button type="submit">Ajouter la musique</button>
    </form>
  <script src=""></script> <!-- JavaScript -->
</body>
</html>
<?php

