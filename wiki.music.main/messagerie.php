<?php
session_start();
include("./include/db.php");

if (!isset($_SESSION['username'])) {
    die("❌ Vous devez être connecté pour accéder à la messagerie.");
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

// Récupérer les messages de l'utilisateur
$messageSql = "SELECT m.id, m.message, m.sent_at, u1.username AS sender, u2.username AS receiver
               FROM messages m
               JOIN users u1 ON m.sender_id = u1.id
               JOIN users u2 ON m.receiver_id = u2.id
               WHERE m.sender_id = :userId OR m.receiver_id = :userId
               ORDER BY m.sent_at DESC";
$messageStmt = $bdd->prepare($messageSql);
$messageStmt->execute([':userId' => $userId]);
$messages = $messageStmt->fetchAll(PDO::FETCH_ASSOC);

// Envoi d'un message
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["receiver"], $_POST["message"])) {
    $receiver = $_POST["receiver"];
    $message = trim($_POST["message"]);

    // Vérifier si le destinataire existe
    $receiverSql = "SELECT id FROM users WHERE username = :receiver";
    $receiverStmt = $bdd->prepare($receiverSql);
    $receiverStmt->execute([':receiver' => $receiver]);
    $receiverUser = $receiverStmt->fetch(PDO::FETCH_ASSOC);

    if (!$receiverUser) {
        $error = "⚠️ Utilisateur non trouvé.";
    } elseif (empty($message)) {
        $error = "⚠️ Message vide.";
    } else {
        // Insérer le message dans la BDD
        $insertSql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (:sender, :receiver, :message)";
        $insertStmt = $bdd->prepare($insertSql);
        $insertStmt->execute([
            ':sender' => $userId,
            ':receiver' => $receiverUser['id'],
            ':message' => $message
        ]);
        header("Location: messagerie.php"); // Recharge la page
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Messagerie privée sur Wiki Music.">
    <title>Messagerie - Wiki Music</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/nav-bar.css">
</head>
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
<body>
    <header>
        <h1>Messagerie</h1>
    </header>

    <main>
        <h2>📨 Envoyer un message</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="receiver">Envoyer à :</label>
            <input type="text" id="receiver" name="receiver" required>
            <label for="message">Message :</label>
            <textarea id="message" name="message" required></textarea>
            <button type="submit">Envoyer</button>
        </form>

        <h2>📩 Mes Messages</h2>
        <ul>
            <?php foreach ($messages as $msg): ?>
                <li>
                    <strong><?php echo htmlspecialchars($msg['sender']); ?></strong> ➡️ 
                    <strong><?php echo htmlspecialchars($msg['receiver']); ?></strong> :
                    <em><?php echo htmlspecialchars($msg['message']); ?></em>
                    <br><small><?php echo $msg['sent_at']; ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
    <h2>📬 Demander la suppression d'une contribution</h2>
        <p>Cliquez sur une contribution pour envoyer une demande à l’administrateur.</p>
        <?php
            $musics = []; // Initialise un tableau vide pour éviter l'erreur

            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['username'])) {
                die("❌ Vous devez être connecté pour accéder à la messagerie.");
            }

            // Récupérer les musiques pour lesquelles il y a une demande de suppression
            $sql = "SELECT m.id, m.title, m.artist, m.album, m.release_year, r.user_id, r.status 
                    FROM musics m
                    INNER JOIN suppression_requests r ON m.id = r.music_id
                    WHERE r.status = 'pending'";
            $stmt = $bdd->prepare($sql);
            $stmt->execute();
            $musics = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère les musiques demandées

            // Vérifier si la requête a bien retourné des résultats
            if (!$musics) {
                $musics = []; // S'assurer que la variable est bien un tableau
            }
        ?>
        <ul>
            <?php foreach ($musics as $music): ?>
                <li>
                    <strong><?php echo htmlspecialchars($music['title']); ?></strong> - 
                    <?php echo htmlspecialchars($music['artist']); ?> (<?php echo htmlspecialchars($music['album']); ?>, <?php echo htmlspecialchars($music['release_year']); ?>)
                    <form method="POST" action="demande_suppression.php">
                        <input type="hidden" name="music_id" value="<?php echo $music['id']; ?>">
                        <label for="reason_<?php echo $music['id']; ?>">Raison :</label>
                        <input type="text" id="reason_<?php echo $music['id']; ?>" name="reason" required>
                        <button type="submit">Demander la suppression</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
</body>
</html>