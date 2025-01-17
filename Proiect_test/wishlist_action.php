<?php
session_start();
require 'db.php'; 


if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in']);
    exit;
}

$user_id = $_SESSION['user_id']; 

if (isset($_POST['movie_id'])) {
    $movie_id = $_POST['movie_id'];

    // verificam daca filmul ales e deja in wishlist
    function is_movie_in_wishlist($user_id, $movie_id) {
        global $conn;
        $stmt = mysqli_prepare($conn, "SELECT * FROM wishlist WHERE user_id = ? AND movie_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $movie_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }

    // adaugam sau scoatem din wishlist filmul in functie de situatie
    if (is_movie_in_wishlist($user_id, $movie_id)) {
        // eliminam filmul daca deja era in wishlist
        $stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE user_id = ? AND movie_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $movie_id);
        mysqli_stmt_execute($stmt);
        echo json_encode(['success' => true, 'action' => 'removed']);
    } else {
        // il adaugam daca nu era deja
        $stmt = mysqli_prepare($conn, "INSERT INTO wishlist (user_id, movie_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $movie_id);
        mysqli_stmt_execute($stmt);
        echo json_encode(['success' => true, 'action' => 'added']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid movie ID']);
}

?>

