<?php

session_start();
require 'config.php'; // Fichier de configuration pour la connexion à la BDD

// Fonction pour rechercher des livres via l'API Google Books
function searchBooks($query)
{
    $apiKey = 'AIzaSyDUNFEKy74YLUS1m_XwxyMJ4LDGBPaJBgQ';
    $url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query) . "&key=" . $apiKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function normalizeThumbnailUrl($url)
{
    if (empty($url)) {
        return 'https://via.placeholder.com/300x420?text=Livre';
    }

    $normalizedUrl = str_replace('http://', 'https://', $url);
    $normalizedUrl = preg_replace('/&zoom=\d+/', '&zoom=0', $normalizedUrl);
    $normalizedUrl = preg_replace('/([&?])edge=curl/', '$1edge=none', $normalizedUrl);

    return $normalizedUrl;
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
            header('Location: profil.php');
            exit;
        } else {
            $error_message = 'Email ou mot de passe incorrect';
        }
    } elseif (isset($_POST['signup'])) {
        // Traitement du signup
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Insertion des informations de l'utilisateur dans la BDD
        $stmt = $mysqli->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();

        $success_message = 'Inscription reussie. Vous pouvez maintenant vous connecter.';
    } elseif (isset($_POST['search'])) {
        // Traitement de la recherche de livres
        $query = $_POST['query'];
        $books = searchBooks($query);
    } elseif (isset($_POST['add_favorite'])) {
        // Ajouter un livre aux favoris
        $book_id = $_POST['book_id'];
        $title = $_POST['title'];
        $authors = $_POST['authors'];
        $thumbnail = normalizeThumbnailUrl($_POST['thumbnail']);
        $user_id = $_SESSION['user_id'];

        $stmt = $mysqli->prepare('INSERT INTO favorites (user_id, book_id, title, authors, thumbnail) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('issss', $user_id, $book_id, $title, $authors, $thumbnail);
        $stmt->execute();

        $success_message = 'Livre ajoute aux favoris.';
    } elseif (isset($_POST['remove_favorite'])) {
        // Supprimer un livre des favoris
        $favorite_id = $_POST['favorite_id'];

        $stmt = $mysqli->prepare('DELETE FROM favorites WHERE id = ?');
        $stmt->bind_param('i', $favorite_id);
        $stmt->execute();

        $success_message = 'Livre supprime des favoris.';
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
$avatar_src = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'images/vf_avatar_main_2075.webp';

$stmt = $mysqli->prepare('SELECT * FROM favorites WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$favorites = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de livres</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Sora:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="app-page home-page">
    <?php include 'video.php'; ?>
    <div class="page-shell">
        <header class="topbar">
            <div class="brand">
                <div class="brand-mark">B</div>
                <div class="brand-copy">
                    <strong>Book Space</strong>
                    <span>Recherche et favoris</span>
                </div>
            </div>
            <nav class="nav-links" aria-label="Navigation principale">
                <a href="index.php" class="is-active">Accueil</a>
                <a href="profil.php">Profil</a>
                <a href="logout.php">Deconnexion</a>
            </nav>
            <div class="user-chip">
                <img src="<?= $avatar_src ?>" alt="Avatar de <?= htmlspecialchars($user['name']) ?>">
                <div>
                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                    <small><?= htmlspecialchars($user['email']) ?></small>
                </div>
            </div>
        </header>

        <section class="hero-panel">
            <div class="hero-copy">
                <p class="eyebrow">Bibliotheque personnelle</p>
                <h1>Trouvez vos prochains coups de coeur.</h1>
                <p>Explorez des milliers d'ouvrages, enregistrez vos favoris et gardez un espace de lecture plus vivant,
                    plus clair et nettement plus attractif.</p>
                <div class="stats-row">
                    <div class="stat-card">
                        <strong><?= count($favorites) ?></strong>
                        <span>livres en favoris</span>
                    </div>
                    <div class="stat-card">
                        <strong><?= isset($books['items']) ? count($books['items']) : 0 ?></strong>
                        <span>resultats visibles</span>
                    </div>
                    <div class="stat-card">
                        <strong><?= !empty($user['name']) ? strtoupper(substr($user['name'], 0, 1)) : 'B' ?></strong>
                        <span>profil actif</span>
                    </div>
                </div>
            </div>

            <aside class="search-card">
                <h2>Lancer une recherche</h2>
                <p>Saisissez un titre, un auteur ou un theme, puis ajoutez directement les livres que vous aimez dans
                    vos favoris.</p>
                <?php if (isset($success_message)): ?>
                    <div class="message message-success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>
                <?php if (isset($error_message)): ?>
                    <div class="message message-error"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <form method="POST" class="search-form">
                    <div>
                        <label class="field-label" for="query">Recherche</label>
                        <input id="query" type="text" name="query"
                            placeholder="Exemple : science-fiction, manga, Victor Hugo..." required>
                    </div>
                    <div class="button-row">
                        <button class="button-primary" type="submit" name="search">Chercher</button>
                        <a class="button-link button-secondary" href="profil.php">Voir mon profil</a>
                    </div>
                </form>
            </aside>
        </section>

        <section class="content-panel">
            <div class="section-heading">
                <div>
                    <h2>Resultats de recherche</h2>
                    <p>Les propositions apparaissent ici apres votre recherche.</p>
                </div>
                <?php if (isset($books['items'])): ?>
                    <p><?= count($books['items']) ?> livre(s) trouve(s)</p>
                <?php endif; ?>
            </div>

            <?php if (isset($books['items']) && is_array($books['items'])): ?>
                <ul class="book-grid">
                    <?php foreach ($books['items'] as $book): ?>
                        <?php
                        $volume = $book['volumeInfo'] ?? [];
                        $title = $volume['title'] ?? 'Titre indisponible';
                        $authors = !empty($volume['authors']) ? implode(', ', $volume['authors']) : 'Auteur inconnu';
                        $thumbnail = normalizeThumbnailUrl($volume['imageLinks']['smallThumbnail'] ?? $volume['imageLinks']['thumbnail'] ?? '');
                        ?>
                        <li class="book-card">
                            <img class="book-cover" src="<?= htmlspecialchars($thumbnail) ?>"
                                alt="Couverture de <?= htmlspecialchars($title) ?>">
                            <div>
                                <h3><?= htmlspecialchars($title) ?></h3>
                                <p class="book-meta"><?= htmlspecialchars($authors) ?></p>
                            </div>
                            <form method="POST" class="stack-form">
                                <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['id']) ?>">
                                <input type="hidden" name="title" value="<?= htmlspecialchars($title) ?>">
                                <input type="hidden" name="authors" value="<?= htmlspecialchars($authors) ?>">
                                <input type="hidden" name="thumbnail" value="<?= htmlspecialchars($thumbnail) ?>">
                                <button class="button-success" type="submit" name="add_favorite">Ajouter aux favoris</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif (isset($books)): ?>
                <div class="empty-state">
                    <p>Aucun resultat n'a ete trouve pour cette recherche. Essayez un autre titre, un auteur ou un mot-cle.
                    </p>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>Commencez par une recherche pour faire apparaitre des suggestions de livres.</p>
                </div>
            <?php endif; ?>
        </section>

        <section class="content-panel">
            <div class="section-heading">
                <div>
                    <h2>Vos favoris</h2>
                    <p>Retrouvez votre selection personnelle en un coup d'oeil.</p>
                </div>
                <p><?= count($favorites) ?> livre(s) enregistres</p>
            </div>

            <?php if (!empty($favorites)): ?>
                <ul class="book-grid">
                    <?php foreach ($favorites as $favorite): ?>
                        <li class="book-card">
                            <img class="book-cover" src="<?= htmlspecialchars(normalizeThumbnailUrl($favorite['thumbnail'])) ?>"
                                alt="Couverture de <?= htmlspecialchars($favorite['title']) ?>">
                            <div>
                                <h3><?= htmlspecialchars($favorite['title']) ?></h3>
                                <p class="book-meta"><?= htmlspecialchars($favorite['authors']) ?></p>
                            </div>
                            <form method="POST" class="stack-form">
                                <input type="hidden" name="favorite_id" value="<?= (int) $favorite['id'] ?>">
                                <button class="button-danger" type="submit" name="remove_favorite">Retirer des favoris</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <p>Vous n'avez pas encore de favoris. Utilisez la recherche ci-dessus pour commencer votre collection.
                    </p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>

</html>