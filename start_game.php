<?php
session_start();
require 'config.php';


$playerID = $_GET['playerID'];
$gameID=$_SESSION['game_id'];
if($gameID==="none"){
    
    $secretNumber = rand(1, 100);

    $stmt = $pdo->prepare("INSERT INTO game_sessions (secret_number) VALUES (?)");
    $stmt->execute([$secretNumber]);
    $gameId = $pdo->lastInsertId();
    $_SESSION['game_id'] = $gameId;
}



// Generate a new game session with a random number

?>
<!DOCTYPE html>
<html>
<head>
    <title>Devinet - Number Guessing Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Devinet Game: <?php echo $gameID ?></h1>
        <div class="game-selection">
            <h2>Select Your Player</h2>
            <a href="game.php?player=player1&playerID=<?= $playerID ?>" class="btn">Player 1</a>
            <a href="game.php?player=player2&playerID=<?= $playerID ?>" class="btn">Player 2</a>
        </div>
    </div>
</body>
</html>