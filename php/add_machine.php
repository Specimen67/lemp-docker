<?php
$servername = "mysql";
$username = "lemp_user";
$password = "lemp_password";
$dbname = "lemp_db";

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("INSERT IGNORE INTO machines (service_tag, lieu_geographique, ip_privee, ip_publique, port_public, image, reservee, reservee_du, reservee_jusquau)
                                VALUES (:service_tag, :lieu_geographique, :ip_privee, :ip_publique, :port_public, :image, :reservee, :reservee_du, :reservee_jusquau)");
        $stmt->execute([
            ':service_tag' => $_POST['service_tag'],
            ':lieu_geographique' => $_POST['lieu_geographique'],
            ':ip_privee' => $_POST['ip_privee'],
            ':ip_publique' => $_POST['ip_publique'],
            ':port_public' => $_POST['port_public'],
            ':image' => $_POST['image'],
            ':reservee' => isset($_POST['reservee']) ? 1 : 0,
            ':reservee_du' => !empty($_POST['reservee_du']) ? $_POST['reservee_du'] : null,
            ':reservee_jusquau' => !empty($_POST['reservee_jusquau']) ? $_POST['reservee_jusquau'] : null,
        ]);

        // Redirection dynamique vers la page d'origine
        header("Location: " . htmlspecialchars($redirect));
        exit;
    } catch (PDOException $e) {
        die("Erreur : " . htmlspecialchars($e->getMessage()));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter une machine</title>
    <script>
        // Fonction pour mettre à jour l'IP publique en fonction du lieu géographique sélectionné
        function updateIPPublique() {
            const lieu = document.getElementById('lieu_geographique').value;
            const ipPubliqueInput = document.getElementById('ip_publique');

            // Mapping des lieux géographiques et des IP publiques associées
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
            ipPubliqueInput.readOnly = !!ipMapping[lieu]; // Rendre le champ non modifiable
        }
    </script>
</head>
<body>
    <h2>Ajouter une machine</h2>
    <form method="POST" action="add_machine.php?redirect=<?= urlencode($_GET['redirect'] ?? 'index.php'); ?>">
        <label>Service Tag :</label>
        <input type="text" name="service_tag" required><br><br>

        <label>Lieu géographique :</label>
        <select name="lieu_geographique" id="lieu_geographique" onchange="updateIPPublique()" required>
            <option value="Strasbourg">Strasbourg</option>
            <option value="Marseille">Marseille</option>
            <option value="Lyon">Lyon</option>
            <option value="Paris">Paris</option>
            <option value="Lille">Lille</option>
            <option value="Nantes">Nantes</option>
            <option value="Autre">Autre</option>
        </select><br><br>

        <label>IP privée :</label>
        <input type="text" name="ip_privee"><br><br>

        <label>IP publique :</label>
        <input type="text" id="ip_publique" name="ip_publique" readonly><br><br>

        <label>Port public :</label>
        <input type="text" name="port_public"><br><br>

        <label>Image :</label>
        <select name="image" required>
            <option value="ATC">ATC</option>
            <option value="Multi_Embedded">Multi_Embedded</option>
            <option value="Solidworks">Solidworks</option>
            <option value="Adobe">Adobe</option>
            <option value="Ubuntu">Ubuntu</option>
            <option value="Office">Office</option>
        </select><br><br>

        <label>Réservée :</label>
        <input type="checkbox" name="reservee"><br><br>

        <label>Réservée depuis :</label>
        <input type="date" name="reservee_du"><br><br>

        <label>Réservée jusqu'au :</label>
        <input type="date" name="reservee_jusquau"><br><br>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
