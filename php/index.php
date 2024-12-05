<?php
require_once 'import_csv.php';

$servername = "mysql";
$username = "lemp_user";
$password = "lemp_password";
$dbname = "lemp_db";

try {
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["import_csv"])) {
        handleCsvImport($conn);
        header("Location: index.php");
        exit;
    }

    // Mise à jour des machines expirées
    $stmt = $conn->prepare("UPDATE machines 
                            SET reservee = 0, reservee_jusquau = NULL 
                            WHERE reservee = 1 AND reservee_jusquau < CURDATE()");
    $stmt->execute();

    // Récupération des machines pour l'affichage
    $stmt = $conn->query("SELECT * FROM machines ORDER BY lieu_geographique");
    $machines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()));
}

// Regrouper les machines par centre
$centers = [
    'Strasbourg' => [],
    'Marseille' => [],
    'Lyon' => [],
    'Paris' => [],
    'Lille' => [],
    'Nantes' => [],
    'Autre' => []
];

foreach ($machines as $machine) {
    if (array_key_exists($machine['lieu_geographique'], $centers)) {
        $centers[$machine['lieu_geographique']][] = $machine;
    } else {
        $centers['Autre'][] = $machine;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des machines par centre</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .available {
            background-color: #d4edda; /* Vert clair */
        }
        .unavailable {
            background-color: #f8d7da; /* Rouge clair */
        }
        .action-buttons {
            margin: 20px 0;
        }
        button {
            margin-right: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        h2 {
            color: #007bff;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <h1>Liste des machines par centre</h1>

    <div class="action-buttons">
        <!-- Bouton Ajouter -->
        <form method="GET" action="add_machine.php" style="display: inline;">
            <input type="hidden" name="redirect" value="index.php">
            <button type="submit">Ajouter une machine</button>
        </form>
        <!-- Bouton Importer -->
        <form method="POST" action="index.php" enctype="multipart/form-data" style="display: inline;">
            <input type="file" name="csv_file" accept=".csv" required>
            <button type="submit" name="import_csv">Importer depuis CSV</button>
        </form>
        <!-- Bouton Exporter -->
        <form method="GET" action="export_to_csv.php" style="display: inline;">
            <button type="submit">Exporter vers CSV</button>
        </form>
    </div>

    <?php foreach ($centers as $center => $machines): ?>
        <h2>
            <a href="view_center.php?center=<?= urlencode($center); ?>" style="text-decoration: none; color: #007bff;">
                <?= htmlspecialchars($center); ?>
            </a>
        </h2>
        <?php if (empty($machines)): ?>
            <p>Aucune machine pour ce centre.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Service Tag</th>
                        <th>Lieu géographique</th>
                        <th>IP privée</th>
                        <th>IP publique</th>
                        <th>Port public</th>
                        <th>Image</th>
                        <th>Réservée</th>
                        <th>Réservée du</th>
                        <th>Réservée jusqu'au</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($machines as $machine): ?>
                        <tr class="<?= $machine['reservee'] ? 'unavailable' : 'available'; ?>">
                            <td><?= htmlspecialchars($machine['service_tag']); ?></td>
                            <td><?= htmlspecialchars($machine['lieu_geographique']); ?></td>
                            <td><?= htmlspecialchars($machine['ip_privee']); ?></td>
                            <td><?= htmlspecialchars($machine['ip_publique']); ?></td>
                            <td><?= htmlspecialchars($machine['port_public']); ?></td>
                            <td><?= htmlspecialchars($machine['image']); ?></td>
                            <td><?= htmlspecialchars($machine['reservee'] ? 'Oui' : 'Non'); ?></td>
                            <td><?= isset($machine['reservee_du']) ? date("d-m-Y", strtotime($machine['reservee_du'])) : 'N/A'; ?></td>
                            <td><?= isset($machine['reservee_jusquau']) ? date("d-m-Y", strtotime($machine['reservee_jusquau'])) : 'N/A'; ?></td>
                            <td>
                                <form method="GET" action="update_machine.php" style="display: inline;">
                                    <input type="hidden" name="service_tag" value="<?= $machine['service_tag']; ?>">
                                    <button type="submit">Modifier</button>
                                </form>
                                <form method="GET" action="delete_machine.php" style="display: inline;">
                                    <input type="hidden" name="service_tag" value="<?= $machine['service_tag']; ?>">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>
</body>
</html>
