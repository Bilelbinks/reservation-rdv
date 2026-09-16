<?php
session_start();
require '../config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare("SELECT id, mot_de_passe_hash FROM admins WHERE login = :login");
    $stmt->execute(['login' => $login]);
    $admin = $stmt->fetch();

    // password_verify compare le mot de passe en clair au hash stocké —
    // jamais de comparaison directe de mots de passe en base.
    if ($admin && password_verify($motDePasse, $admin['mot_de_passe_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: index.php');
        exit();
    }

    $erreur = "Login ou mot de passe incorrect.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h2>Espace admin</h2>

            <?php if ($erreur): ?>
                <p class="flash flash-erreur"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <form method="post">
                <label>Login</label>
                <input type="text" name="login" required>

                <label>Mot de passe</label>
                <input type="password" name="mot_de_passe" required>

                <button type="submit" class="btn">Se connecter</button>
            </form>

            <p style="margin-top:16px;"><a href="../index.php">← Retour au site</a></p>
        </div>
    </div>
</body>
</html>
