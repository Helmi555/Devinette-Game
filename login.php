<?php
session_start();
require 'config.php';


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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <Script>
        
    </Script>
    <?php if (!$user) : ?>
    <div class="container">
        <h1>Devinet Game</h1>
        <div class="login-form">
            
            <input type="text" id="username">
            
            <input type="password" id="password">
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
    </div>
    <?php else : ?>
    <div class="container">
        <h1>Devinet Game</h1>
        <div class="game-selection">
            <a href="creategame.php?playerID=<?php echo $user['id']; ?>" class="btn">let's start</a>
        </div>
    </div>
    <?php endif; ?>
    
</body>
</html>