<?php
$servername = "mysql";
$username = "lemp_user";
$password = "lemp_password";
$dbname = "lemp_db";

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

try {
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer la machine à modifier
    if (isset($_GET['service_tag']) && !empty($_GET['service_tag'])) {
        $stmt = $conn->prepare("SELECT * FROM machines WHERE service_tag = :service_tag");
        $stmt->execute([':service_tag' => $_GET['service_tag']]);
        $machine = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$machine) {
            die("Machine introuvable !");
        }
    } else {
        die("Service tag non fourni !");
    }

    // Mise à jour de la machine après soumission du formulaire
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $stmt = $conn->prepare("UPDATE machines 
                                SET ip_privee = :ip_privee,
                                    ip_publique = :ip_publique,
                                    port_public = :port_public,
                                    image = :image,
                                    reservee = :reservee,
                                    reservee_du = :reservee_du,
                                    reservee_jusquau = :reservee_jusquau
                                WHERE service_tag = :service_tag");
        $stmt->execute([
            ':service_tag' => $_GET['service_tag'], // Service tag d'origine pour l'identification
            ':ip_privee' => $_POST['ip_privee'],
            ':ip_publique' => $_POST['ip_publique'],
            ':port_public' => $_POST['port_public'],
            ':image' => $_POST['image'],
            ':reservee' => isset($_POST['reservee']) ? 1 : 0,
            ':reservee_du' => isset($_POST['reservee']) && !empty($_POST['reservee_du']) ? $_POST['reservee_du'] : null,
            ':reservee_jusquau' => isset($_POST['reservee']) && !empty($_POST['reservee_jusquau']) ? $_POST['reservee_jusquau'] : null,
        ]);

        header("Location: " . htmlspecialchars($redirect));
        exit;
    }
} catch (PDOException $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier une machine</title>
    <script>
        function toggleReservationFields() {
            const reservedCheckbox = document.getElementById('reservee');
            const reserveeDu = document.getElementById('reservee_du');
            const reserveeJusquau = document.getElementById('reservee_jusquau');

            if (reservedCheckbox.checked) {
                reserveeDu.disabled = false;
                reserveeJusquau.disabled = false;
            } else {
                reserveeDu.disabled = true;
                reserveeDu.value = '';
                reserveeJusquau.disabled = true;
                reserveeJusquau.value = '';
            }
        }

        function updateIPPublique() {
            const lieu = document.getElementById('lieu_geographique').value;
            const ipPubliqueInput = document.getElementById('ip_publique');

            const ipMapping = {
                'Strasbourg': '85.169.123.210',
                'Marseille': '82.127.147.7',
                'Lyon': '193.248.167.31',
                'Paris': '81.250.145.166',
                'Lille': '176.162.47.215',
                'Nantes': '80.13.163.208',
                'Autre': ''
            };

            ipPubliqueInput.value = ipMapping[lieu] || '';
            ipPubliqueInput.readOnly = !!ipMapping[lieu]; // Rendre le champ non modifiable si une IP correspondante est définie
        }
    </script>
</head>
<body onload="toggleReservationFields();">
    <h2>Modifier une machine</h2>
    <form method="POST" action="update_machine.php?service_tag=<?= htmlspecialchars($machine['service_tag'] ?? ''); ?>">
        <label>Service Tag :</label>
        <input type="text" name="service_tag" value="<?= htmlspecialchars($machine['service_tag'] ?? ''); ?>" readonly><br><br>
        
        <label>Lieu géographique :</label>
        <select name="lieu_geographique" id="lieu_geographique" onchange="updateIPPublique()" required>
            <option value="Strasbourg" <?= isset($machine['lieu_geographique']) && $machine['lieu_geographique'] === 'Strasbourg' ? 'selected' : ''; ?>>Strasbourg</option>
            <option value="Marseille" <?= isset($machine['lieu_geographique']) && $machine['lieu_geographique'] === 'Marseille' ? 'selected' : ''; ?>>Marseille</option>
            <option value="Lyon" <?= isset($machine['lieu_geographique']) && $machine['lieu_geographique'] === 'Lyon' ? 'selected' : ''; ?>>Lyon</option>
            <option value="Paris" <?= isset($machine['lieu_geographique']) && $machine['lieu_geographique'] === 'Paris' ? 'selected' : ''; ?>>Paris</option>
            <option value="Lille" <?= isset($machine['lieu_geographique']) && $machine['lieu_geographique'] === 'Lille' ? 'selected' : ''; ?>>Lille</option>
            <option value="Nantes" <?= isset($machine['lieu_geographique']) && $machine['lieu_geographique'] === 'Nantes' ? 'selected' : ''; ?>>Nantes</option>
            <option value="Autre" <?= isset($machine['lieu_geographique']) && $machine['lieu_geographique'] === 'Autre' ? 'selected' : ''; ?>>Autre</option>
        </select><br><br>
        
        <label>IP privée :</label>
        <input type="text" name="ip_privee" value="<?= htmlspecialchars($machine['ip_privee'] ?? ''); ?>" required><br><br>
        
        <label>IP publique :</label>
        <input type="text" name="ip_publique" id="ip_publique" value="<?= htmlspecialchars($machine['ip_publique'] ?? ''); ?>"><br><br>
        
        <label>Port public :</label>
        <input type="text" name="port_public" value="<?= htmlspecialchars($machine['port_public'] ?? ''); ?>" required><br><br>
        
        <label>Image :</label>
        <select name="image" required>
            <option value="ATC" <?= isset($machine['image']) && $machine['image'] === 'ATC' ? 'selected' : ''; ?>>ATC</option>
            <option value="Multi_Embedded" <?= isset($machine['image']) && $machine['image'] === 'Multi_Embedded' ? 'selected' : ''; ?>>Multi Embedded</option>
            <option value="Solidworks" <?= isset($machine['image']) && $machine['image'] === 'Solidworks' ? 'selected' : ''; ?>>Solidworks</option>
            <option value="Adobe" <?= isset($machine['image']) && $machine['image'] === 'Adobe' ? 'selected' : ''; ?>>Adobe</option>
            <option value="Ubuntu" <?= isset($machine['image']) && $machine['image'] === 'Ubuntu' ? 'selected' : ''; ?>>Ubuntu</option>
            <option value="Office" <?= isset($machine['image']) && $machine['image'] === 'Office' ? 'selected' : ''; ?>>Office</option>
            <option value="Autre" <?= isset($machine['image']) && $machine['image'] === 'Autre' ? 'selected' : ''; ?>>Autre</option>
        </select><br><br>
        
        <label>Réservée :</label>
        <input type="checkbox" id="reservee" name="reservee" <?= isset($machine['reservee']) && $machine['reservee'] ? 'checked' : ''; ?> onchange="toggleReservationFields()"><br><br>
        
        <label>Réservée depuis :</label>
        <input type="date" id="reservee_du" name="reservee_du" value="<?= isset($machine['reservee_du']) ? htmlspecialchars($machine['reservee_du']) : ''; ?>"><br><br>
        
        <label>Réservée jusqu'au :</label>
        <input type="date" id="reservee_jusquau" name="reservee_jusquau" value="<?= isset($machine['reservee_jusquau']) ? htmlspecialchars($machine['reservee_jusquau']) : ''; ?>"><br><br>
        
        <button type="submit">Modifier</button>
    </form>
</body>
</html>
