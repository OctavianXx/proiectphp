<?php
session_start();

// Verificam daca utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
   
    header('Location: login.php'); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
// Includem baz de date
require 'db.php';

// scoatem filmele din db
$movies = get_all_movies();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieStorage</title>
    <link rel="stylesheet" href="stilizat.css">
    
</head>
<body>
    
<nav>
    <a href="index.php" class="logo">MovieStorage</a><br>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="mylist.php">Watch List</a></li>
        <li><a href="account.php">Account</a></li>
    </ul><br>
    <a href="logout.php?logout=true" class="cta">Logout</a>
</nav>

<h2>Our Movies</h2>
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
    <h3> <a href="image.php">Rating list</a></h3>
</body>
</html>

