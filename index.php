<?php
session_start();
require 'config.php';

// Generate a new game session with a random number
$secretNumber = rand(1, 100);
$sql = "SELECT id FROM game_sessions WHERE game_status = 'waiting' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
// Fetch the result
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {

    $gameId = $result['id'];
} else {

    $stmt = $pdo->prepare("INSERT INTO game_sessions (secret_number) VALUES (?)");
    $stmt->execute([$secretNumber]);
    $gameId = $pdo->lastInsertId();
}


$_SESSION['game_id'] = $gameId;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Devinet - Number Guessing Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Devinet Game</h1>
        <div class="game-selection">
            <h2>Select Your Player</h2>
            <a href="game.php?player=player1" class="btn">Player 1</a>
            <a href="game.php?player=player2" class="btn">Player 2</a>
        </div>
    </div>
</body>
</html>