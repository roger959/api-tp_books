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

    echo 'Profil mis à jour.';
}

// Récupérer les informations de l'utilisateur
$stmt = $mysqli->prepare('SELECT * FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Définir un avatar par défaut si aucun avatar n'est défini
$avatar = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'vf_avatar_main_2075.webp';

include "video.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <style>
        /* Arrière-plan animé */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #000000, #3a3a3a); /* Noir et gris foncé */
            overflow: hidden;
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

        .navbar {
            position: absolute;
            top: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.8); /* Noir semi-transparent */
            padding: 15px;
            display: flex;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .navbar a {
            color: #ffd700; /* Or */
            text-decoration: none;
            padding: 10px 15px;
            font-weight: bold;
            transition: background 0.3s, border-radius 0.3s;
        }

        .navbar a:hover {
            background: #3a3a3a; /* Gris foncé */
            border-radius: 5px;
        }

        .profile-container {
            background: rgba(0, 0, 0, 0.9); /* Noir opaque */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            width: 350px;
            text-align: center;
        }

        .profile-container h2 {
            margin-bottom: 20px;
            color: #ffd700; /* Or */
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }

        .profile-container img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 20px;
            border: 3px solid #ffd700; /* Or */
        }

        .profile-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ffd700; /* Or */
            border-radius: 5px;
            background: rgba(0, 0, 0, 0.3); /* Noir transparent */
            color: #fff; /* Blanc */
            transition: border-color 0.3s;
        }

        .profile-container input:focus {
            border-color: #fff; /* Blanc */
            outline: none;
        }

        .profile-container button {
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

        .profile-container button:hover {
            background: #fff; /* Blanc */
            color: black;
        }

        .message {
            margin-top: 10px;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Accueil</a>
        <a href="profil.php">Profil</a>
        <a href="logout.php">Déconnexion</a>
    </div>
    <div class="profile-container">
        <h2>Modifier le profil</h2>
        <img src="images/vf_avatar_main_2075.webp" alt="Avatar">
        <form method="POST">
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Nom" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" required>
            <input type="text" name="avatar" value="<?= htmlspecialchars($user['avatar']) ?>" placeholder="URL de l'avatar">
            <button type="submit">Mettre à jour</button>
        </form>
        <?php if (isset($error_message)): ?>
            <div class="message"><?= $error_message ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
