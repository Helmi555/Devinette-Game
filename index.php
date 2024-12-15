<?php
session_start();
require 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Devinet - Number Guessing Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <Script>
        
    </Script>
    <div class="container">
        <h1>Devinet Game</h1>
        <div class="game-selection">
            <a href="#" id="playerLink" class="btn">let's start</a>

            <script>
                const username = localStorage.getItem('username');
                
                if (username) {
                    const link = document.getElementById('playerLink');
                    link.href = `start_game.php?playerID=${encodeURIComponent(username)}`;
                } else {
                    const link = document.getElementById('playerLink');
                    link.href = `login.php?playerID=null&password=null`;
                }
            </script>
        </div>
    </div>
    
</body>
</html>