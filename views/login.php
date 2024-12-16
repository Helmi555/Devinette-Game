<?php
session_start();
require '../config.php';



$player = $_GET['playerID'];
$password = $_GET['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :playerID and password = :password");
$stmt->execute(['playerID' => $player , 'password' => $password]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html>
<head>
    <title>Devinet - Number Guessing Game</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

</head>
<body>
<div class="header">
            <h1>Devinette Game</h1>
    <?php if (!$user) : ?>
        
    
        
        <div class="login-form">
            
            <input type="text" id="username" placeholder="username">
            
            <input type="password" id="password"placeholder="password">
            <a href="#" class="btn" onclick="login()">login</a>

            <script>
                function login() {
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    console.log(username, password);
                    
                    if (username) {
                        const link = `login.php?playerID=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`;
                        window.location.href = link;
                    } else {
                        const link = `login.php?playerID=null&password=null`;
                        window.location.href = link;
                    }
                }
            </script>
        </div>
    
    <?php else : ?>
        <div class="game-selection">
            <a href="creategame.php?playerID=<?php echo $user['id']; ?>" class="btn">let's start</a>
        </div>
    
    <?php endif; ?>
        </div>
    
    
</body>
</html>