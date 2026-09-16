<?php
require 'config.php';

$stmt = $pdo->query("SELECT id, nom, duree_minutes, prix FROM services ORDER BY nom");
$services = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre rendez-vous</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrap">

        <header>
            <h1>Prendre rendez-vous</h1>
            <p>Choisis un service et une date pour voir les créneaux libres.</p>
        </header>

        <?php if (isset($_GET['message']) && $_GET['message'] === 'annule'): ?>
            <p class="flash">Rendez-vous annulé.</p>
        <?php endif; ?>

        <div class="card">
            <form method="get" action="creneaux.php">
                <label>Service</label>
                <select name="service_id" required>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>">
                            <?= htmlspecialchars($service['nom']) ?>
                            (<?= $service['duree_minutes'] ?> min — <?= number_format($service['prix'], 2) ?> €)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Date</label>
                <input type="date" name="date" min="<?= date('Y-m-d') ?>" required>

                <button type="submit" class="btn">Voir les créneaux</button>
            </form>
        </div>

        <p style="text-align:center;"><a href="admin/login.php">Espace admin →</a></p>

    </div>
</body>
</html>
