<?php
require 'auth.php';
require '../config.php';

if (isset($_GET['annuler'])) {
    $stmt = $pdo->prepare("DELETE FROM rendez_vous WHERE id = :id");
    $stmt->execute(['id' => (int) $_GET['annuler']]);
    header('Location: index.php?message=annule');
    exit();
}

$stmt = $pdo->query(
    "SELECT rv.id, rv.client_nom, rv.client_email, rv.date_rdv, rv.heure_debut, rv.heure_fin, s.nom AS service_nom
     FROM rendez_vous rv
     JOIN services s ON s.id = rv.service_id
     WHERE rv.date_rdv >= CURDATE()
     ORDER BY rv.date_rdv, rv.heure_debut"
);
$rendezVous = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Rendez-vous</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="wrap">

        <div class="top-actions">
            <h1 style="font-size:1.4rem;margin:0;">Rendez-vous à venir</h1>
            <div class="actions" style="margin:0;">
                <a href="services.php" class="btn btn-ghost">Gérer les services</a>
                <a href="logout.php" class="btn btn-ghost">Déconnexion</a>
            </div>
        </div>

        <?php if (isset($_GET['message']) && $_GET['message'] === 'annule'): ?>
            <p class="flash">Rendez-vous annulé.</p>
        <?php endif; ?>

        <div class="card">
            <?php if (empty($rendezVous)): ?>
                <p class="empty">Aucun rendez-vous à venir.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Service</th>
                            <th>Client</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rendezVous as $rdv): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($rdv['date_rdv'])) ?></td>
                                <td><?= substr($rdv['heure_debut'], 0, 5) ?>–<?= substr($rdv['heure_fin'], 0, 5) ?></td>
                                <td><?= htmlspecialchars($rdv['service_nom']) ?></td>
                                <td>
                                    <?= htmlspecialchars($rdv['client_nom']) ?><br>
                                    <span style="color:var(--text-dim);font-size:0.85rem;">
                                        <?= htmlspecialchars($rdv['client_email']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a
                                        href="index.php?annuler=<?= $rdv['id'] ?>"
                                        onclick="return confirm('Annuler ce rendez-vous ?');"
                                        style="color:#e34948;"
                                    >Annuler</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
