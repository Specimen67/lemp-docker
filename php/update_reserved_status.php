<?php
$servername = "mysql";
$username = "lemp_user";
$password = "lemp_password";
$dbname = "lemp_db";

try {
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mise à jour des machines expirées
    $stmt = $conn->prepare("UPDATE machines 
                            SET reservee = 0, reservee_du = NULL, reservee_jusquau = NULL 
                            WHERE reservee = 1 AND reservee_jusquau < CURDATE()");
    $stmt->execute();

    echo "Mise à jour des réservations effectuée avec succès.\n";
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage()) . "\n";
}
?>
