<?php
function handleCsvImport($conn) {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["csv_file"])) {
        try {
            // Vérifiez si un fichier a été téléversé
            $fileTmpPath = $_FILES["csv_file"]["tmp_name"];
            $uploadDir = '/var/www/html/uploads/';
            $filePath = $uploadDir . basename($_FILES["csv_file"]["name"]);

            // Déplacer le fichier téléversé dans le dossier temporaire
            if (!move_uploaded_file($fileTmpPath, $filePath)) {
                throw new Exception("Échec du téléversement du fichier.");
            }

            // Ouvrir le fichier CSV
            if (($handle = fopen($filePath, 'r')) === false) {
                throw new Exception("Impossible d'ouvrir le fichier CSV.");
            }

            // Ignorer la première ligne (en-têtes)
            fgetcsv($handle);

            // Préparer la requête d'insertion conditionnelle
            $stmt = $conn->prepare("INSERT INTO machines (service_tag, lieu_geographique, ip_privee, ip_publique, port_public, image, reservee, reservee_du, reservee_jusquau) 
                                    SELECT :service_tag, :lieu_geographique, :ip_privee, :ip_publique, :port_public, :image, :reservee, :reservee_du, :reservee_jusquau
                                    WHERE NOT EXISTS (
                                        SELECT 1 FROM machines WHERE service_tag = :service_tag
                                    )");

            // Parcourir les lignes et insérer les données
            while (($data = fgetcsv($handle)) !== false) {
                $stmt->execute([
                    ':service_tag' => $data[0],
                    ':lieu_geographique' => $data[1],
                    ':ip_privee' => $data[2],
                    ':ip_publique' => $data[3],
                    ':port_public' => $data[4],
                    ':image' => $data[5],
                    ':reservee' => $data[6] ?: 0,
                    ':reservee_du' => $data[7] ?: null,
                    ':reservee_jusquau' => $data[8] ?: null,
                ]);
            }

            fclose($handle);
            unlink($filePath); // Supprimez le fichier téléversé après import

        } catch (Exception $e) {
            // Gérer les erreurs
            die("Erreur : " . htmlspecialchars($e->getMessage()));
        }
    }
}
?>
