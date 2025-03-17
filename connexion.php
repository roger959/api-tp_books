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


include "video.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        /* Arrière-plan et styles généraux */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #000000, #3a3a3a); /* Noir et gris foncé */
            color: #ffd700; /* Or */
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.1;
            z-index: -1;
        }

        .login-container {
            background: rgba(0, 0, 0, 0.9); /* Noir opaque */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            width: 350px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #ffd700; /* Or */
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }

        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ffd700; /* Or */
            border-radius: 5px;
            background: rgba(0, 0, 0, 0.3); /* Noir transparent */
            color: #fff; /* Blanc */
            transition: border-color 0.3s;
        }

        .login-container input:focus {
            border-color: #fff; /* Blanc */
            outline: none;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background: #ffd700; /* Or */
            color: black;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-container button:hover {
            background: #fff; /* Blanc */
            color: black;
        }

        .message {
            margin-top: 10px;
            color: red;
            font-weight: bold;
        }

        .signup-link {
            margin-top: 20px;
            display: block;
            text-align: center;
            color: #ffd700; /* Or */
            text-decoration: none;
            transition: color 0.3s;
        }

        .signup-link:hover {
            color: #fff; /* Blanc */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Connexion</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <?php if (isset($error_message)): ?>
            <div class="message"><?= $error_message ?></div>
        <?php endif; ?>
        <a href="inscription.php" class="signup-link">Pas encore inscrit ? Inscrivez-vous ici</a>
    </div>
</body>
</html>