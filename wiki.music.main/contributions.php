<?php
session_start();
include("./include/db.php");

if (!isset($_SESSION['username'])) {
    die("❌ Vous devez être connecté pour voir vos contributions.");
}

$username = $_SESSION['username'];

// Récupérer l'ID de l'utilisateur connecté
$userSql = "SELECT id FROM users WHERE username = :username";
$userStmt = $bdd->prepare($userSql);
$userStmt->execute([':username' => $username]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("❌ Utilisateur non trouvé.");
}

$userId = $user['id'];

// Récupérer les musiques ajoutées par cet utilisateur
$sql = "SELECT id, title, artist, album, release_year FROM musics WHERE added_by = :userId";
$stmt = $bdd->prepare($sql);
$stmt->execute([':userId' => $userId]);
$musics = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="wiki Music, votre répertoire musical collaboratif.">
  <title>Mes Contributions - Wiki Music</title>
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

  <main>
    <h2>Mes Contributions</h2>
    <?php if (count($musics) > 0): ?>
        <ul>
            <?php foreach ($musics as $music): ?>
                <li>
                    <a href="music_details.php?id=<?php echo $music['id']; ?>">
                        <strong><?php echo htmlspecialchars($music['title']); ?></strong> - 
                        <?php echo htmlspecialchars($music['artist']); ?> (<?php echo htmlspecialchars($music['album']); ?>, <?php echo htmlspecialchars($music['release_year']); ?>)
                    </a>

                <!-- Formulaire pour demander la suppression -->
                <form action="demande_suppression.php" method="POST" style="display:inline;">
                    <input type="hidden" name="music_id" value="<?php echo $music['id']; ?>">
                    <button type="submit" style="margin-left:10px; background-color:red; color:white; border:none; padding:5px 10px; cursor:pointer;">
                        Demander suppression
                    </button>
                </form>
              </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Vous n'avez ajouté aucune musique.</p>
    <?php endif; ?>
  </main>
</body>
</html>