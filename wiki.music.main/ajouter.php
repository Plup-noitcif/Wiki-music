<?php
session_start(); // Démarrer la session

if (!isset($_SESSION['id'])) {
    die("❌ Erreur : utilisateur non connecté.");
}
// Debugging - afficher les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("./include/db.php");

// Vérifie si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $album = trim($_POST['album'] ?? '');
    $release_year = intval($_POST['release_year'] ?? 0);
    $lyrics = $_POST['lyrics'] ?? '';
    $added_by = $_SESSION['id'];

    if (!empty($title) && !empty($artist) && !empty($album) && $release_year > 0) {
        $sql = "INSERT INTO musics (title, artist, album, release_year, lyrics, added_by)
                VALUES (:title, :artist, :album, :release_year, :lyrics, :added_by)";
        $stmt = $bdd->prepare($sql);
        $result = $stmt->execute([
            ':title' => $title,
            ':artist' => $artist,
            ':album' => $album,
            ':release_year' => $release_year,
            ':lyrics' => $lyrics,
            ':added_by' => $added_by
        ]);

        if ($result) {
            header("Location: index.php?message=ajout_reussi");
            exit;
        } else {
            echo "❌ Erreur lors de l'ajout de la musique.";
        }
    } else {
        echo "⚠️ Tous les champs sont obligatoires.";
    }
} else {
    echo "⚠️ Accès non autorisé.";
}
?>