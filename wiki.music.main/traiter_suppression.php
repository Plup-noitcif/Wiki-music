<?php
session_start();
include("./panel/include/db.php");

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("❌ Accès refusé.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === "accept") {
        // Supprimer la musique associée
        $delete_sql = "DELETE FROM musics WHERE id = (SELECT music_id FROM deletion_requests WHERE id = :request_id)";
        $delete_stmt = $bdd->prepare($delete_sql);
        $delete_stmt->execute([':request_id' => $request_id]);

        // Supprimer la demande
        $delete_request_sql = "DELETE FROM deletion_requests WHERE id = :request_id";
        $delete_request_stmt = $bdd->prepare($delete_request_sql);
        $delete_request_stmt->execute([':request_id' => $request_id]);

        echo "✅ Contribution supprimée.";
    } elseif ($action === "reject") {
        // Mettre à jour le statut de la demande en "refusée"
        $update_sql = "UPDATE deletion_requests SET status = 'rejected' WHERE id = :request_id";
        $update_stmt = $bdd->prepare($update_sql);
        $update_stmt->execute([':request_id' => $request_id]);

        echo "❌ Demande refusée.";
    }

    header("Location: messagerie.php");
    exit();
} else {
    die("❌ Requête invalide.");
}
?>