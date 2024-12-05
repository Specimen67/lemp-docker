<?php
$servername = "mysql";
$username = "lemp_user";
$password = "lemp_password";
$dbname = "lemp_db";

if (!isset($_GET['center'])) {
    die("Centre non spécifié !");
}

$center = htmlspecialchars($_GET['center']);

try {
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les machines pour le centre donné
    $stmt = $conn->prepare("SELECT * FROM machines WHERE lieu_geographique = :center ORDER BY service_tag");
    $stmt->execute([':center' => $center]);
    $machines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Machines à <?= $center; ?></title>
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
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <h1>Machines à <?= $center; ?></h1>

    <div class="action-buttons">
        <form method="GET" action="index.php" style="display: inline;">
            <button type="submit">Retour</button>
        </form>
        <form method="GET" action="add_machine.php" style="display: inline;">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="center" value="<?= $center; ?>">
        <button type="submit">Ajouter une machine</button>
</form>
    </div>

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
            <?php if (empty($machines)): ?>
                <tr>
                    <td colspan="10" style="text-align: center;">Aucune machine pour ce centre.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($machines as $machine): ?>
                    <tr class="<?= $machine['reservee'] ? 'unavailable' : 'available'; ?>">
                        <td><?= htmlspecialchars($machine['service_tag']); ?></td>
                        <td><?= htmlspecialchars($machine['lieu_geographique']); ?></td>
                        <td><?= htmlspecialchars($machine['ip_privee']); ?></td>
                        <td><?= htmlspecialchars($machine['ip_publique']); ?></td>
                        <td><?= htmlspecialchars($machine['port_public']); ?></td>
                        <td><?= htmlspecialchars($machine['image']); ?></td>
                        <td><?= $machine['reservee'] ? 'Oui' : 'Non'; ?></td>
                        <td><?= $machine['reservee_du'] ? date("d-m-Y", strtotime($machine['reservee_du'])) : 'N/A'; ?></td>
                        <td><?= $machine['reservee_jusquau'] ? date("d-m-Y", strtotime($machine['reservee_jusquau'])) : 'N/A'; ?></td>
                        <td>
                            <form method="GET" action="update_machine.php" style="display: inline;">
                                <input type="hidden" name="service_tag" value="<?= $machine['service_tag']; ?>">
                                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                <input type="hidden" name="center" value="<?= $center; ?>">
                                <button type="submit">Modifier</button>
                            </form>
                            <form method="GET" action="delete_machine.php" style="display: inline;">
                                <input type="hidden" name="service_tag" value="<?= $machine['service_tag']; ?>">
                                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                <input type="hidden" name="center" value="<?= $center; ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
