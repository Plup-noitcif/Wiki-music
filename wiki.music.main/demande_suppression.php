<?php
session_start();
include("./include/db.php");

if (!isset($_SESSION['username'])) {
    die("❌ Vous devez être connecté pour faire une demande.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["music_id"], $_POST["reason"])) {
    $musicId = $_POST["music_id"];
    $reason = trim($_POST["reason"]);

    // Récupérer l'ID de l'utilisateur connecté
    $userSql = "SELECT id FROM users WHERE username = :username";
    $userStmt = $bdd->prepare($userSql);
    $userStmt->execute([':username' => $_SESSION['username']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("❌ Utilisateur non trouvé.");
    }

    $userId = $user['id'];

    // Ajouter la demande
    $sql = "INSERT INTO suppression_requests (user_id, music_id, reason) VALUES (:user, :music, :reason)";
    $stmt = $bdd->prepare($sql);
    $stmt->execute([
        ':user' => $userId,
        ':music' => $musicId,
        ':reason' => $reason
    ]);

    echo "✅ Demande envoyée avec succès.";
    header("Location: contributions.php");
    exit;
}
?>