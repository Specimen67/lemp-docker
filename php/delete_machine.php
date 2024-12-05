<?php
$servername = "mysql";
$username = "lemp_user";
$password = "lemp_password";
$dbname = "lemp_db";

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($_GET['service_tag'])) {
        // Préparer la requête de suppression
        $stmt = $conn->prepare("DELETE FROM machines WHERE service_tag = :service_tag");
        $stmt->execute([':service_tag' => $_GET['service_tag']]);

        // Rediriger vers la page d'accueil après suppression
        header("Location: " . htmlspecialchars($redirect));
        exit;
    } else {
        die("Erreur : service_tag non fourni pour la suppression.");
    }
} catch (PDOException $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}
?>
