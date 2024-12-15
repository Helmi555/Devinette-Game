<?php
session_start();
require 'config.php';

// Validate player selection
if (!isset($_GET['player']) || !in_array($_GET['player'], ['player1', 'player2'])) {
    die("Invalid player selection");
}

$player = $_GET['player'];
$playerID = $_GET['playerID'];

$sql = "SELECT username FROM users WHERE id = :playerID;";
$stmt = $pdo->prepare($sql);
$stmt->execute(['playerID' => $playerID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($player === 'player1'){
    $opponent = 'player2';
}else{
    $opponent = 'player1';
}
$opponentPlayer = ($player === 'player1') ? 'player2' : 'player1';
$gameId = $_SESSION['game_id'];

// Handle player actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's a guess or ready status
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ready':
                // Mark player as ready
                $stmt = $pdo->prepare("UPDATE game_sessions 
                    SET {$player}_ready = TRUE,
                        {$player}_ID=:playerID,
                        game_status = CASE 
                            WHEN {$opponentPlayer}_ready = TRUE THEN 'playing' 
                            ELSE 'waiting' 
                        END 
                    WHERE id = :gameId");
                $stmt->execute(['gameId' => $gameId, 'playerID' => $playerID]);
                break;

            case 'guess':
                $guess = intval($_POST['guess']);
                
                // Fetch current game state
                $stmt = $pdo->prepare("SELECT secret_number FROM game_sessions WHERE id = :gameId");
                $stmt->execute(['gameId' => $gameId]);
                $game = $stmt->fetch(PDO::FETCH_ASSOC);
                $secretNumber = $game['secret_number'];

                // Determine hint
                $hint = ($guess < $secretNumber) ? 'higher' : 
                        (($guess > $secretNumber) ? 'lower' : 'correct');

                // Update game state
                $stmt = $pdo->prepare("UPDATE game_sessions
                    SET 
                        {$player}_tries = {$player}_tries + 1,
                        last_guess_{$player} = :guess,
                        hint_{$player} = :hint,
                        winner = CASE WHEN :guess = secret_number THEN :player ELSE winner END,
                        game_status = CASE WHEN :guess = secret_number THEN 'completed' ELSE game_status END
                    WHERE id = :gameId");
                
                $stmt->execute([
                    'guess' => $guess,
                    'hint' => $hint,
                    'player' => $player,
                    'gameId' => $gameId
                ]);
                break;
        }

        // Redirect to refresh
        header("Location: game.php?player={$player}&playerID={$playerID}");
        exit;
    }
}

// Fetch updated game state
$stmt = $pdo->prepare("SELECT * FROM game_sessions WHERE id = :gameId");
$stmt->execute(['gameId' => $gameId]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$game) {
    header("Location: creategame.php?&playerID=$playerID");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Devinet - <?php echo ucfirst($player); ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <meta http-equiv="refresh" content="5">
</head>
<body>
<div class="header">
<h1>Devinette Game</h1>

        <h2 style="color:white">Game : <?php echo $gameId ?><br> Player:  <?php echo $user["username"] ?></h2>
        
        <?php if ($game['game_status'] === 'waiting'): ?>
            <?php if (!$game["{$player}_ready"]): ?>
                <div class="waiting-phase">
                    <h2 style="color:white">Get Ready!</h2>
                    <form method="POST">
                        <input style="color:white" type="hidden" name="action" value="ready">
                        <button type="submit" class="btn">I'm Ready</button>
                    </form>
            <p style="color: white;">Waiting for the other player to be ready...</p>
            <a class="btn2" href="creategame.php?&playerID=<?= $playerID ?>">Back to Home</a>

        </div>
                </div>
            <?php else: ?>
                <div class="waiting-indicator">
            <p style="color: white;">Waiting for the other player to be ready...</p>
            <div class="spinner"></div>
        </div>
            <a class="btn2" href="creategame.php?&playerID=<?= $playerID ?>">Back to Home</a>

            <?php endif; ?>

        <?php elseif ($game['game_status'] === 'playing'): ?>
            <?php if ($game['winner']): ?>
                <div class="game-over">
                    <h2 style="color:purple">Game Over !</h2>
                    <p>
                        <?php 
                        if ($game['winner'] === $player) {
                            echo "Congratulations! You Won! 🎉";
                        } else {
                            echo "Sorry, {$game['winner']} Won! 😢";
                        }
                        ?>
                    </p>
                    <p style="color:white">Secret Number was: <?php echo $game['secret_number']; ?></p>
                </div>
            <?php else: ?>
                <?php if ($game[$player . '_tries'] <= $game[$opponent . '_tries']): ?>
                    <div class="game-phase">
                    <h2 style="color:white">Guess the Number (1-100)</h2>
                    
                    <?php if ($game["last_guess_{$player}"] !== null): ?>
                        <p style="color:white">Your Last Guess: <?php echo $game["last_guess_{$player}"]; ?></p>
                        <p style="color:white">Hint: Go <?php echo $game["hint_{$player}"]; ?></p>
                    <?php endif; ?>
                    
                    <?php if ($game["last_guess_{$opponentPlayer}"] !== null): ?>
                        <p style="color:white">Opponent's Last Guess: <?php echo $game["last_guess_{$opponentPlayer}"]; ?></p>
                        <p style="color:white">Opponent Hint: <?php echo $game["hint_{$opponentPlayer}"]; ?></p>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="number" name="guess" min="1" max="100" required>
                        <input type="hidden" name="action" value="guess">
                        <button class="btn-guess" type="submit">Guess</button>
                    </form>

                    <div class="tries">
                        <p style="color:white">Your Tries: <?php echo $game["{$player}_tries"]; ?></p>
                        <p style="color:white">Opponent Tries: <?php echo $game["{$opponentPlayer}_tries"]; ?></p>
                    </div>
                </div>
                <?php else: ?>
                    <p style="color:white">Waiting for the other player to make a guess...</p>
                <?php endif; ?>
            <?php endif; ?>

        <?php elseif ($game['game_status'] === 'completed'): ?>
            <div class="game-over">
                <h2 style="color:white">Game Over!</h2>
                <p style="color:white" >
                    <?php 
                    if ($game['winner'] === $player) {
                        echo "Congratulations! You Won! 🎉";
                    } else {
                        echo "Sorry, {$game['winner']} Won! 😢";
                    }
                    ?>
                </p>
                <p style="color:white">Secret Number was: <?php echo $game['secret_number']; ?></p>
            </div>
            <a class="btn2" href="creategame.php?&playerID=<?= $playerID ?>">Back to Home</a>

        <?php endif; ?>
  
    
        
    
</body>
</html>