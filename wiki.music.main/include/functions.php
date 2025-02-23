<?php

function ajouterMusique($db, $title, $artist, $album, $release_year, $lyrics) {
    $sql = "INSERT INTO musics (title, artist, album, release_year, lyrics) 
            VALUES (:title, :artist, :album, :release_year, :lyrics)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':title' => htmlspecialchars($title),
        ':artist' => htmlspecialchars($artist),
        ':album' => htmlspecialchars($album),
        ':release_year' => $release_year,
        ':lyrics' => htmlspecialchars($lyrics)
    ]);
    return $stmt->rowCount() > 0;
}

function getMusics($db) {
    $stmt = $db->query("SELECT * FROM musics ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function secureInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}