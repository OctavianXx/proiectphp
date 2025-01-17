<?php
session_start();
require 'db.php'; 

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php"); 
    exit;
}

$user_id = $_SESSION['user_id']; 

// aflam ce filme sunt in wishlist
function get_movies_from_wishlist($user_id) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, "SELECT m.id, m.title, m.price, m.description, m.release_date, m.genre, m.director, m.rating, m.image 
                                    FROM wishlist w
                                    JOIN moviee m ON w.movie_id = m.id
                                    WHERE w.user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $movies = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    return $movies;
}


$movies = get_movies_from_wishlist($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Watchlist</title>
    <link rel="stylesheet" href="stilizat.css">
</head>
<body>

    <nav>
    <a href="index.php" class="logo">MovieStorage</a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="mylist.php">Watch List</a></li>
        <li><a href="account.php">Account</a></li>

    </ul>
    <a href="logout.php?logout=true" class="cta">Logout</a>
</nav>

    <h1>My Watchlist</h1>
    
    <?php if (empty($movies)): ?>
        <p>Your watchlist is empty. Start adding movies!</p>
    <?php else: ?>
        <div class="movie-list">
            <?php foreach ($movies as $movie): ?>
                <div class="movie">
                    <a href="movie.php?title=<?= urlencode($movie['title']); ?>">
                        <h3><?= htmlspecialchars($movie['title']); ?></h3>
                        <p class="price">$<?= number_format($movie['price'], 2); ?></p>
                        <?php if (!empty($movie['image'])): ?>
                            <img src="<?= htmlspecialchars($movie['image']); ?>" alt="<?= htmlspecialchars($movie['title']); ?>" />
                        <?php else: ?>
                            <p>No image available</p>
                        <?php endif; ?>
                        <p><strong>Description:</strong> <?= htmlspecialchars($movie['description']); ?></p>
                        <p><strong>Release Date:</strong> <?= htmlspecialchars($movie['release_date']); ?></p>
                        <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']); ?></p>
                        <p><strong>Director:</strong> <?= htmlspecialchars($movie['director']); ?></p>
                        <p><strong>Rating:</strong> <?= number_format($movie['rating'], 1); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>

