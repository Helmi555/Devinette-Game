<?php
session_start();

require '../config.php';

function gameIDExists($pdo, $gameID) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM game_sessions WHERE id = ? and game_status='waiting'");
    $stmt->execute([$gameID]);
    return $stmt->fetchColumn() > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gameID'])) {
    $gameID = $_POST['gameID'];
        $_SESSION['game_id'] = $gameID;
    if (gameIDExists($pdo, $gameID)) {
        
        echo "Game ID exists!";
    } else {
        echo "Game ID does not exist!";
    }
    exit;
}

$playerID = $_GET['playerID'];

$secretNumber = rand(1, 100);
$sql = "SELECT id FROM game_sessions WHERE game_status = 'waiting' LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $gameId = $result['id'];
} else {
    $stmt = $pdo->prepare("INSERT INTO game_sessions (secret_number) VALUES (?)");
    $stmt->execute([$secretNumber]);
    $gameId = $pdo->lastInsertId();
}

$_SESSION['game_id'] = "none";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Devinette - Number Guessing Game</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

   <style>
    body{
        display:flex;
        
    }
   </style>
</head>
<body>
<div class="header">
<h1>Devinette Game</h1>
 
        <div class="game-selection">
            <h5>Select your game</h5>
            <div class="join">
                <input type="number" id="gameID" placeholder="Game ID">
                <a href="#" class="btn" onclick="startGame()">Join Existing Game</a>

            </div>
            <div class="control">
                <a href="start_game.php?&playerID=<?= $playerID ?>" class="btn">New game</a>
                <a href="game_history.php?&playerID=<?= $playerID ?>" class="btn">Your game history</a>
            </div>

            <script>
                function startGame() {
                    const gameID = document.getElementById('gameID').value;

                    fetch('creategame.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'gameID=' + gameID,
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log("data: "+data+" :end")
                        if (data === 'Game ID exists!') {
                            
                            window.location.href = "start_game.php?&playerID=<?= $playerID ?>";
                        } else {
                            alert('Game ID does not exist!');
                        }
                    });
                }
            </script>
        
    </div>
        </div>
    
       
</body>
</html>