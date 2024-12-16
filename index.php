<?php
session_start();
require 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Devinet - Number Guessing Game</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

</head>
<body>
<div class="header">
<h1>Devinette Game</h1>

            <div class="game-selection">
            <a href="#" id="playerLink" class="btn">let's start</a>

            <script>
                const username = localStorage.getItem('username');
                
                if (username) {
                    const link = document.getElementById('playerLink');
                    link.href = `./views/start_game.php?playerID=${encodeURIComponent(username)}`;
                } else {
                    const link = document.getElementById('playerLink');
                    link.href = `./views/login.php?playerID=null&password=null`;
                }
            </script>
        </div>
        </div>
        
        
    
    
</body>
</html>