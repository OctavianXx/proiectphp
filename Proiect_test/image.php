<?php
// Database connection (update with your credentials)
$host = 'localhost';
$dbname = 'moviestore';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch average ratings for each genre
$query = "SELECT genre, AVG(rating) AS avg_rating FROM moviee GROUP BY genre";
$stmt = $pdo->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the bar chart
$genres = array_column($data, 'genre');
$ratings = array_column($data, 'avg_rating');

// Chart dimensions
$width = 800;
$height = 600;
$margin = 50;
$bar_width = 40;
$bar_spacing = 20;

// Create the image
$image = imagecreate($width, $height);

// Colors
$background_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
$bar_color = imagecolorallocate($image, 100, 149, 237);
$axis_color = imagecolorallocate($image, 0, 0, 0);

// Fill the background
imagefill($image, 0, 0, $background_color);

// Draw axes
imageline($image, $margin, $height - $margin, $width - $margin, $height - $margin, $axis_color); // X-axis
imageline($image, $margin, $margin, $margin, $height - $margin, $axis_color); // Y-axis

// Calculate the scale
$max_rating = max($ratings);
$scale = ($height - 2 * $margin) / $max_rating;

// Draw bars
foreach ($ratings as $index => $rating) {
    $bar_height = $rating * $scale;
    $x1 = $margin + $index * ($bar_width + $bar_spacing);
    $y1 = $height - $margin - $bar_height;
    $x2 = $x1 + $bar_width;
    $y2 = $height - $margin;

    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $bar_color);

    // Add genre labels
    $genre_label = $genres[$index];
    imagestring($image, 5, $x1, $height - $margin + 5, $genre_label, $text_color);

    // Add rating labels
    $rating_label = number_format($rating, 1);
    imagestring($image, 5, $x1 + 5, $y1 - 15, $rating_label, $text_color);
}

// Add title
$title = "Average Ratings by Genre";
imagestring($image, 5, ($width / 2) - (strlen($title) * 5 / 2), $margin / 2, $title, $text_color);

// Output the image
header("Content-Type: image/png");
imagepng($image);

// Free memory
imagedestroy($image);
?>
