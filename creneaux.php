<?php
require 'config.php';
require 'functions.php';

$serviceId = (int) ($_GET['service_id'] ?? 0);
$date = $_GET['date'] ?? '';

$stmt = $pdo->prepare("SELECT id, nom, duree_minutes, prix FROM services WHERE id = :id");
$stmt->execute(['id' => $serviceId]);
$service = $stmt->fetch();

if (!$service) {
    header('Location: index.php');
    exit();
}

$erreur = '';
$creneaux = [];

if (!estDateValide($date)) {
    $erreur = "Cette date n'est pas disponible (jour fermé ou date passée). Choisis-en une autre.";
} else {
    $creneaux = getCreneauxDisponibles($pdo, $date, (int) $service['duree_minutes']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créneaux disponibles</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrap">

        <div class="card">
            <h2><?= htmlspecialchars($service['nom']) ?> — <?= date('d/m/Y', strtotime($date)) ?></h2>

            <?php if ($erreur): ?>
                <p class="flash flash-erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php elseif (empty($creneaux)): ?>
                <p class="empty">Plus aucun créneau libre ce jour-là. Essaie une autre date.</p>
            <?php else: ?>
                <div class="creneaux-grid">
                    <?php foreach ($creneaux as $heure): ?>
                        <a
                            class="creneau-btn"
                            href="reserver.php?service_id=<?= $serviceId ?>&date=<?= urlencode($date) ?>&heure=<?= urlencode($heure) ?>"
                        ><?= $heure ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="actions">
                <a href="index.php" class="btn btn-ghost">← Changer de service/date</a>
            </div>
        </div>

    </div>
</body>
</html>
