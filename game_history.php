<?php
require 'config.php';
$query = "SELECT * FROM game_sessions ORDER BY created_at DESC LIMIT 10";
$stmt = $pdo->prepare($query);
$stmt->execute();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game History - Scoreboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="game_history.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Game History</h1>
        </div>

        <div class="scoreboard">
            <div class="scoreboard-header">
                <div class="scoreboard-title">Scoreboard 🏆</div>
                <div>Latest Games</div>
            </div>

            <?php if (!empty($games)): ?>
                <table class="game-table">
                    <thead>
                        <tr>
                            <th>Game ID</th>
                            <th>Winner</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($game['id']) ?></td>
                                <td class="<?= $game['winner'] ? 'winner' : 'no-winner' ?>">
                                    <?= htmlspecialchars($game['winner'] ?: 'No Winner') ?>
                                </td>
                                <td><?= htmlspecialchars($game['player1_tries']) ?> - <?= htmlspecialchars($game['player2_tries']) ?></td>
                                <td>
                                    <span class="game-status <?= $game['game_status'] == 'completed' ? 'status-completed' : 'status-ongoing' ?>">
                                        <?= htmlspecialchars(ucfirst($game['game_status'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($game['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No game history available yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>