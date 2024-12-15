<?php
session_start();
require 'config.php';

// Validate player selection
if (!isset($_GET['player']) || !in_array($_GET['player'], ['player1', 'player2'])) {
    die("Invalid player selection");
}

$player = $_GET['player'];
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
                        game_status = CASE 
                            WHEN {$opponentPlayer}_ready = TRUE THEN 'playing' 
                            ELSE 'waiting' 
                        END 
                    WHERE id = :gameId");
                $stmt->execute(['gameId' => $gameId]);
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
        header("Location: game.php?player={$player}");
        exit;
    }
}

// Fetch updated game state
$stmt = $pdo->prepare("SELECT * FROM game_sessions WHERE id = :gameId");
$stmt->execute(['gameId' => $gameId]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Devinet - <?php echo ucfirst($player); ?></title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="5">
</head>
<body>
    <div class="container">
        <h1>Devinet Game - <?php echo ucfirst($player); ?></h1>
        
        <?php if ($game['game_status'] === 'waiting'): ?>
            <?php if (!$game["{$player}_ready"]): ?>
                <div class="waiting-phase">
                    <h2>Get Ready!</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="ready">
                        <button type="submit" class="btn">I'm Ready</button>
                    </form>
                    <p>Waiting for the other player to be ready...</p>
                </div>
            <?php else: ?>
                <p>Waiting for the other player to be ready...</p>
            <?php endif; ?>

        <?php elseif ($game['game_status'] === 'playing'): ?>
            <?php if ($game['winner']): ?>
                <div class="game-over">
                    <h2>Game Over!</h2>
                    <p>
                        <?php 
                        if ($game['winner'] === $player) {
                            echo "Congratulations! You Won! 🎉";
                        } else {
                            echo "Sorry, {$game['winner']} Won! 😢";
                        }
                        ?>
                    </p>
                    <p>Secret Number was: <?php echo $game['secret_number']; ?></p>
                </div>
            <?php else: ?>
                <?php if ($game[$player . '_tries'] <= $game[$opponent . '_tries']): ?>
                    <div class="game-phase">
                    <h2>Guess the Number (1-100)</h2>
                    
                    <?php if ($game["last_guess_{$player}"] !== null): ?>
                        <p>Your Last Guess: <?php echo $game["last_guess_{$player}"]; ?></p>
                        <p>Hint: Go <?php echo $game["hint_{$player}"]; ?></p>
                    <?php endif; ?>
                    
                    <?php if ($game["last_guess_{$opponentPlayer}"] !== null): ?>
                        <p>Opponent's Last Guess: <?php echo $game["last_guess_{$opponentPlayer}"]; ?></p>
                        <p>Opponent Hint: <?php echo $game["hint_{$opponentPlayer}"]; ?></p>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="number" name="guess" min="1" max="100" required>
                        <input type="hidden" name="action" value="guess">
                        <button type="submit">Guess</button>
                    </form>

                    <div class="tries">
                        <p>Your Tries: <?php echo $game["{$player}_tries"]; ?></p>
                        <p>Opponent Tries: <?php echo $game["{$opponentPlayer}_tries"]; ?></p>
                    </div>
                </div>
                <?php else: ?>
                    <p>Waiting for the other player to make a guess...</p>
                <?php endif; ?>
            <?php endif; ?>

        <?php elseif ($game['game_status'] === 'completed'): ?>
            <div class="game-over">
                <h2>Game Over!</h2>
                <p>
                    <?php 
                    if ($game['winner'] === $player) {
                        echo "Congratulations! You Won! 🎉";
                    } else {
                        echo "Sorry, {$game['winner']} Won! 😢";
                    }
                    ?>
                </p>
                <p>Secret Number was: <?php echo $game['secret_number']; ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>