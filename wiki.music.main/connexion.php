<?php

include("./include/functions.php");

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
  <!-- Section principale -->
    <h2>Connexion</h2>
    <form action="connect.php" method="POST">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="login.php">Créer un compte</a></p>
</body>
</html>