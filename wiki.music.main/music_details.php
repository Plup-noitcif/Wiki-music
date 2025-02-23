<?php
session_start();
include("./include/db.php");

if (isset($_GET['id'])) {
    $musicId = intval($_GET['id']);

    $sql = "SELECT musics.*, users.username AS added_by_username 
            FROM musics 
            JOIN users ON musics.added_by = users.id 
            WHERE musics.id = :id";

    $stmt = $bdd->prepare($sql);
    $stmt->execute([':id' => $musicId]);
    $music = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($music) {
        // Récupérer les musiques du même album
        $albumSql = "SELECT * FROM musics WHERE album = :album AND id != :id";
        $albumStmt = $bdd->prepare($albumSql);
        $albumStmt->execute([':album' => $music['album'], ':id' => $musicId]);
        $sameAlbumMusics = $albumStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les musiques du même artiste
        $artistSql = "SELECT * FROM musics WHERE artist = :artist AND id != :id";
        $artistStmt = $bdd->prepare($artistSql);
        $artistStmt->execute([':artist' => $music['artist'], ':id' => $musicId]);
        $sameArtistMusics = $artistStmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "<p>❌ Musique non trouvée.</p>";
        exit;
    }
} else {
    echo "<p>⚠️ Aucun identifiant de musique fourni.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Wiki Music, votre répertoire musical collaboratif.">
  <title>Détails de la musique</title>
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
    <h2>Détails de la Musique</h2>

    <h3><?= htmlspecialchars($music['title']) ?></h3>
    <p><strong>Artiste :</strong> <?= htmlspecialchars($music['artist']) ?></p>
    <p><strong>Album :</strong> <?= htmlspecialchars($music['album']) ?></p>
    <p><strong>Date de sortie :</strong> <?= htmlspecialchars($music['release_year']) ?></p>
    <p><strong>Paroles :</strong> <br> <?= nl2br(htmlspecialchars($music['lyrics'])) ?></p>

    <p><strong>Ajouté par :</strong> 
        <a href="profil.php?username=<?= urlencode($music['added_by_username']) ?>">
            <?= htmlspecialchars($music['added_by_username']) ?>
        </a>
    </p>

    <h4>Musiques du même album</h4>
    <ul>
        <?php foreach ($sameAlbumMusics as $sameAlbumMusic): ?>
            <li><a href="music_details.php?id=<?= $sameAlbumMusic['id'] ?>">
                <?= htmlspecialchars($sameAlbumMusic['title']) ?> - <?= htmlspecialchars($sameAlbumMusic['artist']) ?>
            </a></li>
        <?php endforeach; ?>
    </ul>

    <h4>Musiques du même artiste</h4>
    <ul>
        <?php foreach ($sameArtistMusics as $sameArtistMusic): ?>
            <li><a href="music_details.php?id=<?= $sameArtistMusic['id'] ?>">
                <?= htmlspecialchars($sameArtistMusic['title']) ?> - <?= htmlspecialchars($sameArtistMusic['artist']) ?>
            </a></li>
        <?php endforeach; ?>
    </ul>
</main>

</body>
</html>