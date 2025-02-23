<?php
session_start();
include("./include/db.php");

// Vérification si l'utilisateur est admin (ex : une colonne `role = 'admin'`)
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("❌ Accès refusé.");
}

// Récupérer toutes les demandes
$sql = "SELECT sr.id, sr.reason, sr.status, sr.created_at, u.username, m.title 
        FROM suppression_requests sr
        JOIN users u ON sr.user_id = u.id
        JOIN musics m ON sr.music_id = m.id
        ORDER BY sr.created_at DESC";
$stmt = $bdd->prepare($sql);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traiter une réponse admin
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["request_id"], $_POST["action"])) {
    $requestId = $_POST["request_id"];
    $action = ($_POST["action"] === "accept") ? "acceptée" : "refusée";

    // Mettre à jour la demande
    $updateSql = "UPDATE suppression_requests SET status = :status WHERE id = :id";
    $updateStmt = $bdd->prepare($updateSql);
    $updateStmt->execute([':status' => $action, ':id' => $requestId]);

    // Supprimer la musique si acceptée
    if ($action === "acceptée") {
        $deleteSql = "DELETE FROM musics WHERE id = (SELECT music_id FROM suppression_requests WHERE id = :id)";
        $deleteStmt = $bdd->prepare($deleteSql);
        $deleteStmt->execute([':id' => $requestId]);
    }

    header("Location: admin_messagerie.php");
    exit;
}
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
    <header>
        <h1>📬 Messagerie Admin</h1>
    </header>
      
    <h2>Demandes de suppression</h2>

    <?php
    $sql = "SELECT r.id, m.title, m.artist, u.username, r.reason 
            FROM suppression_requests r
            JOIN musics m ON r.music_id = m.id
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'pending'";
    $stmt = $bdd->prepare($sql);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($requests) > 0): ?>
        <ul>
            <?php foreach ($requests as $request): ?>
                <li>
                    <strong><?php echo htmlspecialchars($request['title']); ?></strong> - 
                    <?php echo htmlspecialchars($request['artist']); ?> 
                    (Demande par <?php echo htmlspecialchars($request['username']); ?>)

                    <p><strong>Raison :</strong> <?php echo htmlspecialchars($request['reason']); ?></p>

                    <!-- Formulaire pour accepter ou refuser -->
                    <form action="traiter_suppression.php" method="POST" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <button type="submit" name="action" value="accept" style="background-color:green; color:white; padding:5px 10px; cursor:pointer;">
                            ✅ Accepter
                        </button>
                        <button type="submit" name="action" value="reject" style="background-color:red; color:white; padding:5px 10px; cursor:pointer;">
                            ❌ Refuser
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucune demande en attente.</p>
    <?php endif; ?>
</body>
</html>