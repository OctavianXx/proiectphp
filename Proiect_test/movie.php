<?php
session_start();
require 'db.php'; // Include conexiunea la baza de date

// Verifică dacă utilizatorul este logat
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php"); // Redirecționează la login dacă nu este logat
    exit;
}

$user_id = $_SESSION['user_id']; // Preia ID-ul utilizatorului din sesiune

// Verifică dacă există un parametru 'title' în URL
if (isset($_GET['title'])) {
    $movie_title = urldecode($_GET['title']); // Decodează titlul filmului

    // Funcție pentru a obține detaliile unui film după titlu
    function get_movie_by_title($title) {
        global $conn;
        $stmt = mysqli_prepare($conn, "SELECT id, title, price, description, release_date, genre, director, rating, image FROM moviee WHERE title = ?");
        mysqli_stmt_bind_param($stmt, 's', $title);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    $movie = get_movie_by_title($movie_title); // Obține detaliile filmului

    if (!$movie) {
        die('Invalid movie title');
    }

    // Verifică dacă filmul este deja în lista de vizionare a utilizatorului
    function is_movie_in_wishlist($user_id, $movie_id) {
        global $conn;
        $stmt = mysqli_prepare($conn, "SELECT * FROM wishlist WHERE user_id = ? AND movie_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $movie_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }

    $is_in_wishlist = is_movie_in_wishlist($user_id, $movie['id']); // Verifică dacă filmul este în wishlist
} else {
    die('Invalid movie title');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movie['title']); ?> - Movie Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #ffffff;
        }

        nav {
            background-color: #1f1f1f;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        nav a {
            color: #43d6e0;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
            font-size: 1.1em;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
        }

        .movie-image {
            flex: 1 1 400px;
            text-align: center;
        }

        .movie-image img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        .movie-details {
            flex: 2 1 600px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .movie-details h1 {
            font-size: 3em;
            color: #43d6e0;
        }

        .movie-details p {
            font-size: 1.2em;
            line-height: 1.6;
        }

        .movie-details strong {
            color: #43d6e0;
        }

        button#wishlist-button {
            align-self: flex-start;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button#wishlist-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function toggleWishlist(movieId) {
            const button = document.getElementById('wishlist-button');

            fetch('wishlist_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `movie_id=${movieId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.textContent = data.action === 'added' ? 'Remove from Watchlist' : 'Add to My Watchlist';
                } else {
                    alert('Something went wrong!');
                }
            });
        }
    </script>
</head>
<body>
    <nav>
        <div>
            <a href="logout.php?logout=true">Logout</a>
            <a href="account.php">Account</a>
            <a href="index.php">Home</a>
            <a href="mylist.php">My Watchlist</a>
        </div>
    </nav>

    <div class="container">
        <!-- Movie Image -->
        <div class="movie-image">
            <?php if (!empty($movie['image'])): ?>
                <img src="<?= htmlspecialchars($movie['image']); ?>" alt="<?= htmlspecialchars($movie['title']); ?>">
            <?php else: ?>
                <p>No image available</p>
            <?php endif; ?>
        </div>

        <!-- Movie Details -->
        <div class="movie-details">
            <h1><?= htmlspecialchars($movie['title']); ?></h1>
            <p><strong>Price:</strong> $<?= number_format($movie['price'], 2); ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($movie['description']); ?></p>
            <p><strong>Release Date:</strong> <?= htmlspecialchars($movie['release_date']); ?></p>
            <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']); ?></p>
            <p><strong>Director:</strong> <?= htmlspecialchars($movie['director']); ?></p>
            <p><strong>Rating:</strong> <?= number_format($movie['rating'], 1); ?></p>

            <!-- Wishlist Button -->
            <button id="wishlist-button" onclick="toggleWishlist(<?= $movie['id']; ?>)">
                <?= $is_in_wishlist ? 'Remove from Watchlist' : 'Add to My Watchlist'; ?>
            </button>
        </div>
    </div>
</body>
</html>
