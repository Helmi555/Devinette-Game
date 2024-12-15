<?php
session_start();

require 'config.php'; // Include your database connection

// Function to check if gameID exists
function gameIDExists($pdo, $gameID) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM game_sessions WHERE id = ? and game_status='waiting'");
    $stmt->execute([$gameID]);
    return $stmt->fetchColumn() > 0;
}

// Handle AJAX request
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

$_SESSION['game_id'] = "none";
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
            <h2>Select your game</h2>
            <input type="number" id="gameID" placeholder="Game ID:">
            <a href="#" class="btn" onclick="startGame()">Join Existing Game</a>
            <a href="start_game.php?&playerID=<?= $playerID ?>" class="btn">start new game</a>

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
                            // Redirect to the game page or handle success
                            
                            window.location.href = "start_game.php?&playerID=<?= $playerID ?>";
                        } else {
                            // Handle error
                            alert('Game ID does not exist!');
                        }
                    });
                }
            </script>
        </div>
    </div>
</body>
</html>