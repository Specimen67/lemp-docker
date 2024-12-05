<?php
$servername = "mysql";
$username = "lemp_user";
$password = "lemp_password";
$dbname = "lemp_db";

try {
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Préparer la requête pour récupérer les données
    $stmt = $conn->query("SELECT * FROM machines");
    $machines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Définir les en-têtes HTTP pour forcer le téléchargement
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="machines_export.csv"');

    // Ouvrir un flux pour la sortie
    $output = fopen('php://output', 'w');

    // Ajouter les en-têtes de colonnes dans le CSV
    fputcsv($output, ['Service Tag', 'Lieu géographique', 'IP privée', 'IP publique', 'Port public', 'Image', 'Réservée', 'Réservée du', 'Réservée jusqu\'au']);

    // Ajouter les lignes de données dans le CSV
    foreach ($machines as $machine) {
        fputcsv($output, [
            $machine['service_tag'],
            $machine['lieu_geographique'],
            $machine['ip_privee'],
            $machine['ip_publique'],
            $machine['port_public'],
            $machine['image'],
            $machine['reservee'],
            $machine['reservee_du'],
            $machine['reservee_jusquau']
        ]);
    }

    // Fermer le flux
    fclose($output);
    exit;
} catch (Exception $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}
