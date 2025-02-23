<?php
$servername = "localhost";
$username = "root";
$password = "root";
$sql = "SELECT username, role FROM users WHERE username = :username";

try {
    $bdd = new PDO("mysql:host=$servername;dbname=xtjh1161_wiki_music_bdd", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "";
} 
catch (PDOException $e) {
    echo "Erreur : ".$e->getMessage();

}
?>
