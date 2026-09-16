<?php
require 'auth.php';
require '../config.php';

$erreur = '';
$editId = (int) ($_GET['edit'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $duree = (int) ($_POST['duree_minutes'] ?? 0);
    $prix = (float) str_replace(',', '.', $_POST['prix'] ?? '0');
    $id = (int) ($_POST['id'] ?? 0);

    if ($nom === '' || $duree <= 0 || $prix < 0) {
        $erreur = "Merci de remplir tous les champs correctement.";
    } elseif ($id > 0) {
        $stmt = $pdo->prepare(
            "UPDATE services SET nom = :nom, duree_minutes = :duree, prix = :prix WHERE id = :id"
        );
        $stmt->execute(['nom' => $nom, 'duree' => $duree, 'prix' => $prix, 'id' => $id]);
        header('Location: services.php?message=modifie');
        exit();
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO services (nom, duree_minutes, prix) VALUES (:nom, :duree, :prix)"
        );
        $stmt->execute(['nom' => $nom, 'duree' => $duree, 'prix' => $prix]);
        header('Location: services.php?message=ajoute');
        exit();
    }
}

if (isset($_GET['supprimer'])) {
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = :id");
    $stmt->execute(['id' => (int) $_GET['supprimer']]);
    header('Location: services.php?message=supprime');
    exit();
}

$serviceAEditer = null;
if ($editId > 0) {
    $stmt = $pdo->prepare("SELECT id, nom, duree_minutes, prix FROM services WHERE id = :id");
    $stmt->execute(['id' => $editId]);
    $serviceAEditer = $stmt->fetch();
}

$services = $pdo->query("SELECT id, nom, duree_minutes, prix FROM services ORDER BY nom")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Services</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="wrap">

        <div class="top-actions">
            <h1 style="font-size:1.4rem;margin:0;">Services</h1>
            <a href="index.php" class="btn btn-ghost">← Rendez-vous</a>
        </div>

        <?php if (isset($_GET['message'])): ?>
            <p class="flash">
                <?= ['ajoute' => 'Service ajouté.', 'modifie' => 'Service modifié.', 'supprime' => 'Service supprimé.'][$_GET['message']] ?? '' ?>
            </p>
        <?php endif; ?>

        <div class="card">
            <h2><?= $serviceAEditer ? 'Modifier le service' : 'Ajouter un service' ?></h2>

            <?php if ($erreur): ?>
                <p class="flash flash-erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="id" value="<?= $serviceAEditer['id'] ?? '' ?>">

                <label>Nom</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($serviceAEditer['nom'] ?? '') ?>" required>

                <label>Durée (minutes)</label>
                <input type="number" name="duree_minutes" value="<?= $serviceAEditer['duree_minutes'] ?? '' ?>" min="5" step="5" required>

                <label>Prix (€)</label>
                <input type="number" name="prix" value="<?= $serviceAEditer['prix'] ?? '' ?>" min="0" step="0.5" required>

                <div class="actions">
                    <button type="submit" class="btn"><?= $serviceAEditer ? 'Enregistrer' : 'Ajouter' ?></button>
                    <?php if ($serviceAEditer): ?>
                        <a href="services.php" class="btn btn-ghost">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr><th>Nom</th><th>Durée</th><th>Prix</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= htmlspecialchars($service['nom']) ?></td>
                            <td><?= $service['duree_minutes'] ?> min</td>
                            <td><?= number_format($service['prix'], 2) ?> €</td>
                            <td>
                                <a href="services.php?edit=<?= $service['id'] ?>">Modifier</a>
                                ·
                                <a
                                    href="services.php?supprimer=<?= $service['id'] ?>"
                                    onclick="return confirm('Supprimer ce service ? Les rendez-vous liés seront aussi supprimés.');"
                                    style="color:#e34948;"
                                >Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
