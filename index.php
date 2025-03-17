<?php 

session_start();
require 'config.php'; // Fichier de configuration pour la connexion à la BDD

// Fonction pour rechercher des livres via l'API Google Books
function searchBooks($query) {
    $apiKey = 'AIzaSyDUNFEKy74YLUS1m_XwxyMJ4LDGBPaJBgQ';
    $url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query) . "&key=" . $apiKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Gestion des soumissions de formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Traitement du login
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Vérification des informations de connexion (chatgpt)
        $stmt = $mysqli->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: profile.php');
            exit;
        } else {
            echo 'Email ou mot de passe incorrect';
        }
    } elseif (isset($_POST['signup'])) {
        // Traitement du signup
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Insertion des informations de l'utilisateur dans la BDD
        $stmt = $mysqli->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();

        echo 'Inscription réussie. Vous pouvez maintenant vous connecter.';
    } elseif (isset($_POST['search'])) {
        // Traitement de la recherche de livres
        $query = $_POST['query'];
        $books = searchBooks($query);
    } elseif (isset($_POST['add_favorite'])) {
        // Ajouter un livre aux favoris
        $book_id = $_POST['book_id'];
        $title = $_POST['title'];
        $authors = $_POST['authors'];
        $thumbnail = $_POST['thumbnail'];
        $user_id = $_SESSION['user_id'];

        $stmt = $mysqli->prepare('INSERT INTO favorites (user_id, book_id, title, authors, thumbnail) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('issss', $user_id, $book_id, $title, $authors, $thumbnail);
        $stmt->execute();

        echo 'Livre ajouté aux favoris.';
    } elseif (isset($_POST['remove_favorite'])) {
        // Supprimer un livre des favoris
        $favorite_id = $_POST['favorite_id'];

        $stmt = $mysqli->prepare('DELETE FROM favorites WHERE id = ?');
        $stmt->bind_param('i', $favorite_id);
        $stmt->execute();

        echo 'Livre supprimé des favoris.';
    }
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$stmt = $mysqli->prepare('SELECT name, avatar, email FROM users WHERE id = ?');
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
    <title>Recherche de livres</title>
    <style>
        /* Styles généraux */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #000000, #3a3a3a);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: #fff;
            overflow-x: hidden;
        }

        h1, h2 {
            color: #ffd700; /* Or */
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
            margin: 20px 0;
        }

        /* Barre de navigation */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 20px 40px;
            background: rgba(0, 0, 0, 0.8);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0;
            z-index: 1000;
        }

        .navbar a {
            text-decoration: none;
            color: #ffd700; /* Or */
            font-weight: bold;
            margin: 0 15px;
            font-size: 18px;
            transition: color 0.3s ease-in-out;
        }

        .navbar a:hover {
            color: #fff; /* Blanc */
        }

        /* Informations utilisateur */
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid #ffd700; /* Or */
        }

        .user-info .user-details {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-info .user-details span {
            color: #ffd700; /* Or */
            font-size: 20px;
            font-weight: bold;
        }

        .user-info .user-details small {
            color: #fff; /* Blanc */
            font-size: 14px;
        }

        /* Formulaire de recherche */
        form {
            display: flex;
            gap: 10px;
            background: rgba(0, 0, 0, 0.8);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
            margin-top: 120px;
        }

        input[type="text"] {
            padding: 12px;
            width: 300px;
            border: 1px solid #ffd700; /* Or */
            border-radius: 5px;
            outline: none;
            background: rgba(0, 0, 0, 0.3);
            color: #fff;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #fff; /* Blanc */
        }

        button {
            padding: 12px 20px;
            background: #ffd700; /* Or */
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #fff; /* Blanc */
            color: black;
        }

        /* Résultats de recherche */
        ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 0;
            list-style: none;
            gap: 20px;
            margin-top: 20px;
        }

        .book {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            width: 220px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .book:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.7); /* Or */
        }

        .book img {
            width: 100px;
            height: 150px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .book p {
            color: #ffd700; /* Or */
            font-weight: bold;
        }

        /* Bouton d'ajout aux favoris */
        .book button {
            margin-top: 10px;
            background: #28a745; /* Vert */
        }

        .book button:hover {
            background: #218838; /* Vert foncé */
        }

        /* Favoris */
        .book form button[name="remove_favorite"] {
            background: #dc3545; /* Rouge */
        }

        .book form button[name="remove_favorite"]:hover {
            background: #c82333; /* Rouge foncé */
        }

        /* Arrière-plan vidéo */
        .background-video, #bg-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        #bg-video {
            filter: brightness(0.3);
        }
    </style>
</head>
<body>
    <div class="background-video">
        <video autoplay muted loop id="bg-video">
            <source src="path/to/your/video.mp4" type="video/mp4">
        </video>
    </div>
    <div class="navbar">
        <div>
            <a href="index.php">Accueil</a>
            <a href="profil.php">Profil</a>
            <a href="logout.php">Déconnexion</a>
        </div>
        <div class="user-info">
            <img src="images/vf_avatar_main_2075.webp" alt="Avatar">
            <div class="user-details">
                <span><?= htmlspecialchars($user['name']) ?></span>
                
            </div>
        </div>
    </div>
    <h1>Recherche de livres</h1>
    <form method="POST">
        <input type="text" name="query" placeholder="Rechercher un livre..." required>
        <button type="submit" name="search">Chercher</button>
    </form>

    <?php if (isset($books)): ?>
        <h2>Résultats de la recherche</h2>
        <ul>
            <?php foreach ($books['items'] as $book): ?>
                <li class="book">
                    <img src="<?= $book['volumeInfo']['imageLinks']['thumbnail'] ?>" alt="<?= $book['volumeInfo']['title'] ?>">
                    <p><?= $book['volumeInfo']['title'] ?> par <?= implode(', ', $book['volumeInfo']['authors']) ?></p>
                    <form method="POST">
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <input type="hidden" name="title" value="<?= $book['volumeInfo']['title'] ?>">
                        <input type="hidden" name="authors" value="<?= implode(', ', $book['volumeInfo']['authors']) ?>">
                        <input type="hidden" name="thumbnail" value="<?= $book['volumeInfo']['imageLinks']['thumbnail'] ?>">
                        <button type="submit" name="add_favorite">Ajouter aux favoris</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Vos livres favoris</h2>
    <ul>
        <?php
        $user_id = $_SESSION['user_id'];
        $stmt = $mysqli->prepare('SELECT * FROM favorites WHERE user_id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $favorites = $result->fetch_all(MYSQLI_ASSOC);

        foreach ($favorites as $favorite) {
            echo '<li class="book">';
            echo '<img src="' . $favorite['thumbnail'] . '" alt="' . $favorite['title'] . '">';
            echo '<p>' . $favorite['title'] . ' par ' . $favorite['authors'] . '</p>';
            echo '<form method="POST">';
            echo '<input type="hidden" name="favorite_id" value="' . $favorite['id'] . '">';
            echo '<button type="submit" name="remove_favorite">Supprimer des favoris</button>';
            echo '</form>';
            echo '</li>';
        }
        ?>
    </ul>
</body>
</html>