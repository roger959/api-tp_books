<?php
require_once 'config.php';

// Vérifier si une session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Vérifier si l'utilisateur existe
    $stmt = $mysqli->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Stocker les infos utilisateur en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $email;

        // Redirection vers la page d'accueil
        header('Location: index.php');
        exit;
    } else {
        $error_message = "Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Sora:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="auth-page">
    <?php include 'video.php'; ?>
    <div class="auth-card">
        <section class="auth-copy">
            <div>
                <p class="eyebrow">Club et competitions</p>
                <h1>Entrez dans votre espace lecture.</h1>
                <p>Connectez-vous pour retrouver vos favoris, explorer de nouveaux titres et profiter d'une interface
                    plus claire et plus immersive.</p>
            </div>
            <div class="auth-highlights">
                <div>Recherche rapide de livres via Google Books.</div>
                <div>Gestion simple de votre selection favorite.</div>
                <div>Design plus moderne avec fond video et panneaux contrastes.</div>
            </div>
        </section>
        <section class="auth-panel">
            <h2>Connexion</h2>
            <p>Renseignez vos identifiants pour acceder a votre bibliotheque.</p>
            <?php if (isset($error_message)): ?>
                <div class="message message-error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="POST" class="stack-form">
                <div>
                    <label class="field-label" for="email">Email</label>
                    <input id="email" type="email" name="email" placeholder="vous@exemple.com" required>
                </div>
                <div>
                    <label class="field-label" for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" placeholder="Votre mot de passe" required>
                </div>
                <button class="button-primary" type="submit">Se connecter</button>
            </form>
            <p>Pas encore de compte ? <a href="inscription.php" class="text-link">Creer un profil</a></p>
        </section>
    </div>
</body>

</html>