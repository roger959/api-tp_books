<?php
session_start();
require 'config.php'; // Fichier de configuration pour la connexion à la BDD

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Gestion des soumissions de formulaire pour modifier le profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $avatar = $_POST['avatar'];

    $stmt = $mysqli->prepare('UPDATE users SET name = ?, email = ?, avatar = ? WHERE id = ?');
    $stmt->bind_param('sssi', $name, $email, $avatar, $user_id);
    $stmt->execute();

    $success_message = 'Profil mis a jour avec succes.';
}

// Récupérer les informations de l'utilisateur
$stmt = $mysqli->prepare('SELECT * FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Définir un avatar par défaut si aucun avatar n'est défini
$avatar_src = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'images/vf_avatar_main_2075.webp';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Sora:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="app-page profile-page">
    <?php include 'video.php'; ?>
    <div class="page-shell">
        <header class="topbar">
            <div class="brand">
                <div class="brand-mark">B</div>
                <div class="brand-copy">
                    <strong>Book Space</strong>
                    <span>Profil et preferences</span>
                </div>
            </div>
            <nav class="nav-links" aria-label="Navigation principale">
                <a href="index.php">Accueil</a>
                <a href="profil.php" class="is-active">Profil</a>
                <a href="logout.php">Deconnexion</a>
            </nav>
        </header>

        <section class="profile-grid">
            <article class="profile-overview">
                <p class="eyebrow">Votre espace</p>
                <img class="profile-avatar" src="<?= $avatar_src ?>"
                    alt="Avatar de <?= htmlspecialchars($user['name']) ?>">
                <h1><?= htmlspecialchars($user['name']) ?></h1>
                <p>Personnalisez vos informations pour rendre votre espace plus identifiable et garder une presentation
                    propre dans l'interface.</p>
                <div class="profile-badges">
                    <span><?= htmlspecialchars($user['email']) ?></span>
                    <span>Profil modifiable a tout moment</span>
                </div>
            </article>

            <article class="profile-card">
                <h2>Modifier le profil</h2>
                <p>Mettez a jour votre nom, votre email ou l'adresse de votre avatar.</p>
                <?php if (isset($success_message)): ?>
                    <div class="message message-success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>
                <?php if (isset($error_message)): ?>
                    <div class="message message-error"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <form method="POST" class="stack-form">
                    <div>
                        <label class="field-label" for="name">Nom</label>
                        <input id="name" type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                            placeholder="Nom" required>
                    </div>
                    <div>
                        <label class="field-label" for="email">Email</label>
                        <input id="email" type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                            placeholder="Email" required>
                    </div>
                    <div>
                        <label class="field-label" for="avatar">Avatar</label>
                        <input id="avatar" type="text" name="avatar" value="<?= htmlspecialchars($user['avatar']) ?>"
                            placeholder="URL de l'avatar">
                        <p class="field-hint">Vous pouvez laisser ce champ vide pour conserver l'image par defaut.</p>
                    </div>
                    <div class="button-row">
                        <button class="button-primary" type="submit">Mettre a jour</button>
                        <a class="button-link button-secondary" href="index.php">Retour a l'accueil</a>
                    </div>
                </form>
            </article>
        </section>
    </div>
</body>

</html>