<?php
include("./include/db.php");

if ($db) {
    echo "✅ Connexion réussie à la base de données.";
} else {
    echo "❌ Échec de la connexion.";
}
?>