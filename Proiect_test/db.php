<?php

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'moviestore';

$conn = mysqli_connect($host, $username, $password, $dbname);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


function get_all_movies() {
    global $conn;
    $sql = "SELECT title, price, image, description, release_date, genre, director, rating FROM moviee";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Error executing query: " . mysqli_error($conn));  
    }

    $movies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $movie = [
            "title" => $row['title'],
            "price" => (float)$row['price'],
            "description" => $row['description'],
            "release_date" => $row['release_date'],
            "genre" => $row['genre'],
            "director" => $row['director'],
            "rating" => (float)$row['rating'],
            "image" => $row['image']
        ];
        $movies[] = $movie;
    }
    return $movies;
}


function get_movie_by_id($movie_id) {
    global $conn;
    $sql = "SELECT id, title, price, description, release_date, genre, director, rating, image FROM movies WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $movie_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_fetch_assoc($result);
}

?>
