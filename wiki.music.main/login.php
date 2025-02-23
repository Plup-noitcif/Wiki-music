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
  
  <?php
  include("./include/db.php");

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $identifiant = $_POST['identifiant'] ?? '';
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';

      if (!empty($identifiant) && !empty($username) && !empty($password)) {
          // Hash du mot de passe
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

          // Vérifier si l'identifiant existe déjà
          $sql = "SELECT id FROM users WHERE identifiant = :identifiant";
          $stmt = $bdd->prepare($sql);
          $stmt->execute([':identifiant' => $identifiant]);

          if ($stmt->fetch()) {
              echo "❌ Cet identifiant est déjà utilisé.";
          } else {
              // Insérer le nouvel utilisateur
              $sql = "INSERT INTO users (identifiant, username, password) VALUES (:identifiant, :username, :password)";
              $stmt = $bdd->prepare($sql);

              if ($stmt->execute([
                  ':identifiant' => $identifiant,
                  ':username' => $username,
                  ':password' => $hashedPassword
              ])) {
                  echo "✅ Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
              } else {
                  echo "❌ Une erreur est survenue lors de la création du compte.";
              }
          }
      } else {
          echo "⚠️ Tous les champs doivent être remplis.";
      }
  }
  ?>

  <!DOCTYPE html>
  <html lang="fr">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Créer un compte</title>
  </head>
  <body>
      <h2>Créer un compte</h2>
      <form method="post">
          <label for="identifiant">Identifiant :</label>
          <input type="text" name="identifiant" required>

          <label for="username">Nom d'utilisateur :</label>
          <input type="text" name="username" required>

          <label for="password">Mot de passe :</label>
          <input type="password" name="password" required>

          <button type="submit">S'inscrire</button>
      </form>

      <p>Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
    

  <!-- Pied de page -->
  <footer>
    <p>&copy; 2025 wiki Music.</p>
  </footer>

  <script src=""></script> <!-- JavaScript -->
</body>
</html>
