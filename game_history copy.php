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
    <style>
        :root {
            --bg-dark: #0f1020;
            --bg-darker: #0a0a15;
            --text-primary: #00ffff;
            --text-secondary: #00ff99;
            --accent-color: #ff00ff;
            --border-color: #1a1a3a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Space Mono', monospace;
            /*background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-darker) 100%);*/
            background: url('./assets/wallpaper.jpg') no-repeat center center fixed ;
            background-size: cover;
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(to right, #1a1a3a, #0f0f25);
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid var(--accent-color);
            margin-bottom: 20px;
        }

        .header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 0 0 10px rgba(0, 255, 255, 0.7);
        }

        .scoreboard {
            background-color: rgba(26, 26, 58, 0.8);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .scoreboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--border-color);
        }

        .scoreboard-title {
            font-size: 1.5rem;
            color: var(--text-secondary);
            font-weight: bold;
        }

        .game-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .game-table th {
            background-color: #1a1a3a;
            color: var(--text-primary);
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid var(--accent-color);
            font-family: 'Orbitron', sans-serif;
        }

        .game-table tr {
            transition: all 0.3s ease;
        }

        .game-table tr:nth-child(even) {
            background-color: rgba(26, 26, 58, 0.8);
        }
        .game-table tr:nth-child(odd) { 
            background-color: rgba(34, 34, 76, 0.8);
         }


        .game-table tr:hover {
            background-color: rgba(60, 60, 100, 0.7);
            transform: scale(1.02);
        }

        .game-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            color: #c0c0ff;
        }

        .winner {
            color: #00ff99;
            font-weight: bold;
            text-shadow: 0 0 5px rgba(0, 255, 153, 0.5);
        }

        .no-winner {
            color: #ff4500;
            font-weight: bold;
        }

        .game-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
        }

        .status-completed {
            background-color: #00a86b;
            color: white;
        }

        .status-ongoing {
            text-align: center;
            background-color: #ffa500;
            color: white;
            
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #6a6a9e;
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            .game-table {
                font-size: 0.9rem;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
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