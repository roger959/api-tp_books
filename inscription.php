<?php
require_once 'config.php';

// Vérifier si une session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Vérifier si l'email existe déjà
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error_message = "Cet email est déjà utilisé.";
    } else {
        // Insérer l'utilisateur
        $stmt = $mysqli->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $password);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header('Location: index.php');
            exit;
        } else {
            $error_message = "Erreur lors de l'inscription.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
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
                <p class="eyebrow">Nouvelle inscription</p>
                <h1>Creez un profil qui vous ressemble.</h1>
                <p>Inscrivez-vous pour sauvegarder vos trouvailles, construire votre collection et acceder a une
                    interface plus nette, plus immersive et plus agreable a utiliser.</p>
            </div>
            <div class="auth-highlights">
                <div>Un compte pour enregistrer vos lectures preferees.</div>
                <div>Un acces rapide a votre profil et vos favoris.</div>
                <div>Une presentation visuelle unifiee sur tout le site.</div>
            </div>
        </section>
        <section class="auth-panel">
            <h2>Inscription</h2>
            <p>Remplissez le formulaire pour demarrer votre bibliotheque personnelle.</p>
            <?php if (isset($error_message)): ?>
                <div class="message message-error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="POST" class="stack-form">
                <div>
                    <label class="field-label" for="name">Nom</label>
                    <input id="name" type="text" name="name" placeholder="Votre nom" required>
                </div>
                <div>
                    <label class="field-label" for="email">Email</label>
                    <input id="email" type="email" name="email" placeholder="vous@exemple.com" required>
                </div>
                <div>
                    <label class="field-label" for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" placeholder="Choisissez un mot de passe"
                        required>
                </div>
                <button class="button-primary" type="submit">S'inscrire</button>
            </form>
            <p>Vous avez deja un compte ? <a href="connexion.php" class="text-link">Se connecter</a></p>
        </section>
    </div>
</body>

</html>