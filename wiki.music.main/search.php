<?php
include("./include/db.php");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $query = trim($_POST['query'] ?? '');
    $type = $_POST['type'] ?? 'musics';
    
    // Si la recherche est vide, on récupère toutes les musiques
    if (empty($query)) {
        $sql = "SELECT * FROM musics ORDER BY id DESC";  // On ne filtre pas si la recherche est vide
        $stmt = $bdd->prepare($sql);
        $stmt->execute();  // Pas de paramètre à lier ici
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Si un terme de recherche est donné, on continue la recherche comme avant
        $allowedTypes = ['musics', 'users'];
        if (!in_array($type, $allowedTypes)) {
            echo "<p>Type de recherche non valide.</p>";
            exit;
        }
        
        $columns = [
            'musics' => ['title', 'artist', 'album'],
            'users' => ['username']
        ];

        // Construction de la clause WHERE
        $whereClauses = [];
        foreach ($columns[$type] as $column) {
            $whereClauses[] = "$column LIKE :query";
        }

        // Si la recherche porte sur plusieurs colonnes, on met "OR" entre elles
        $whereSql = implode(' OR ', $whereClauses);

        // Requête SQL
        $sql = "SELECT * FROM $type WHERE $whereSql ORDER BY id DESC";
        $stmt = $bdd->prepare($sql);
        // Passer un seul paramètre :query pour toutes les colonnes
        $stmt->execute([':query' => "%$query%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Affichage des résultats
    if ($results) {
        echo "<ul>";
        foreach ($results as $row) {
            if ($type === 'musics') {
                echo "<li><a href='music_details.php?id=" . $row['id'] . "'><strong>Title:</strong> " . htmlspecialchars($row['title']) . " | <strong>Artist:</strong> " . htmlspecialchars($row['artist']) . " | <strong>Album:</strong> " . htmlspecialchars($row['album']) . "</a></li>";
            } elseif ($type === 'users') {
                echo "<li><strong>Username:</strong> " . htmlspecialchars($row['username']) . "</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>Aucun résultat trouvé.</p>";
    }
}
?>