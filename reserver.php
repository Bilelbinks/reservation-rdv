<?php
require 'config.php';
require 'functions.php';

$serviceId = (int) ($_GET['service_id'] ?? $_POST['service_id'] ?? 0);
$date = $_GET['date'] ?? $_POST['date'] ?? '';
$heure = $_GET['heure'] ?? $_POST['heure'] ?? '';

$stmt = $pdo->prepare("SELECT id, nom, duree_minutes, prix FROM services WHERE id = :id");
$stmt->execute(['id' => $serviceId]);
$service = $stmt->fetch();

if (!$service || !estDateValide($date)) {
    header('Location: index.php');
    exit();
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Le créneau a pu être pris par quelqu'un d'autre entre l'affichage de
    // la page et la validation du formulaire : on revérifie avant d'insérer.
    $creneauxActuels = getCreneauxDisponibles($pdo, $date, (int) $service['duree_minutes']);

    if ($nom === '' || $email === '') {
        $erreur = "Merci de renseigner ton nom et ton email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse email invalide.";
    } elseif (!in_array($heure, $creneauxActuels, true)) {
        $erreur = "Ce créneau vient d'être pris par quelqu'un d'autre. Choisis-en un autre.";
    } else {
        $heureFin = date('H:i:s', strtotime($heure) + $service['duree_minutes'] * 60);

        $stmtInsert = $pdo->prepare(
            "INSERT INTO rendez_vous (service_id, client_nom, client_email, date_rdv, heure_debut, heure_fin)
             VALUES (:service_id, :nom, :email, :date, :heure_debut, :heure_fin)"
        );
        $stmtInsert->execute([
            'service_id' => $serviceId,
            'nom' => $nom,
            'email' => $email,
            'date' => $date,
            'heure_debut' => $heure,
            'heure_fin' => $heureFin,
        ]);

        header('Location: confirmation.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmer le rendez-vous</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrap">

        <div class="card">
            <h2>Confirmer le rendez-vous</h2>
            <p>
                <?= htmlspecialchars($service['nom']) ?> —
                <?= date('d/m/Y', strtotime($date)) ?> à <?= htmlspecialchars($heure) ?>
                (<?= $service['duree_minutes'] ?> min, <?= number_format($service['prix'], 2) ?> €)
            </p>

            <?php if ($erreur): ?>
                <p class="flash flash-erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="service_id" value="<?= $serviceId ?>">
                <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
                <input type="hidden" name="heure" value="<?= htmlspecialchars($heure) ?>">

                <label>Nom</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

                <div class="actions">
                    <button type="submit" class="btn">Confirmer</button>
                    <a href="index.php" class="btn btn-ghost">Annuler</a>
                </div>
            </form>
        </div>

    </div>
</body>
</html>
