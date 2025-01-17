<?php

session_start(); 


if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}


$user_id = $_SESSION['user_id'];

require 'db.php';


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); 
    exit;
}

// luam datele utilizatorului din db
$username = $_SESSION['username'];
$sql = "SELECT id, username, email, passwords FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

// actualizare parola
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['old_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // verificam daca parola veche introdusa este corecta
    if (password_verify($old_password, $user['passwords'])) {
        // verificam daca sunt corecte parolele noi
        if ($new_password === $confirm_password) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // schimbam parola in db
            $update_sql = "UPDATE users SET passwords = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, 'si', $hashed_new_password, $user['id']);
            if (mysqli_stmt_execute($update_stmt)) {
                $message = "Password updated successfully!";
            } else {
                $error = "There was an error updating the password.";
            }
        } else {
            $error = "The new passwords do not match.";
        }
    } else {
        $error = "The old password is incorrect.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        a {
            color: #43d6e0;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        h1 {
            margin-top: 20px;
            color: #43d6e0;
            font-size: 2.5em;
            text-align: center;
        }

        .account-box {
            background-color: #1f1f1f;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            padding: 20px;
            margin-top: 30px;
            width: 90%;
            max-width: 500px;
        }

        .account-box h2 {
            color: #43d6e0;
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .account-box p {
            margin: 10px 0;
            font-size: 1.2em;
            line-height: 1.5;
        }

        .account-box form {
            margin-top: 20px;
        }

        .account-box label {
            display: block;
            font-weight: bold;
            margin: 10px 0 5px;
        }

        .account-box input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #333;
            background-color: #2c2c2c;
            color: #ffffff;
            font-size: 1em;
        }

        .account-box input:focus {
            border-color: #43d6e0;
            outline: none;
        }

        .account-box button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #43d6e0;
            border: none;
            border-radius: 5px;
            color: #ffffff;
            font-size: 1.2em;
            cursor: pointer;
            width: 100%;
        }

        .account-box button:hover {
            background-color: #e5533d;
        }


        .success-message {
            color: #43a047;
            font-size: 1.2em;
            margin: 10px 0;
        }

        .error-message {
            color: #e53935;
            font-size: 1.2em;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <nav>
        <a href="logout.php?logout=true">Logout</a>
        <a href="account.php?logout=true">Account</a>
        <a href="index.php">Home</a>
        <a href="mylist.php">Watch List</a>
    </nav>

    <h1>Account Information</h1>

    <div class="account-box">
        <?php if (isset($message)): ?>
            <p class="success-message"><?= $message; ?></p>
        <?php elseif (isset($error)): ?>
            <p class="error-message"><?= $error; ?></p>
        <?php endif; ?>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>

        <h2>Change Password</h2>
        <form method="post" action="account.php">
            <label for="old_password">Old Password:</label>
            <input type="password" name="old_password" id="old_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>
