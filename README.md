# Devinette Game 🎲  
A fun and interactive multiplayer guessing game developed using **HTML**, **CSS**, **JavaScript**, **PHP**, and **MySQL**.

---

## 📌 Features
- **Two-Player Mode:** Play against another player in real-time.
- **Game History:** Track the progress of all played games.
- **Dynamic Hints:** Receive helpful hints to guess the secret number.
- **Interactive UI:** Simple and engaging interface.

---

## 🛠️ Technologies Used
- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP  
- **Database:** MySQL  
- **Version Control:** Git and GitHub  

---

## 📋 Database Schema

### `game_sessions` Table  
This table stores information about each game session:  
```sql
CREATE TABLE IF NOT EXISTS game_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    secret_number INT NOT NULL,
    player1_ready BOOLEAN DEFAULT FALSE,
    player2_ready BOOLEAN DEFAULT FALSE,
    player1_tries INT DEFAULT 0,
    player2_tries INT DEFAULT 0,
    last_guess_player1 INT NULL,
    last_guess_player2 INT NULL,
    hint_player1 VARCHAR(10) NULL,
    hint_player2 VARCHAR(10) NULL,
    winner VARCHAR(10) NULL,
    game_status ENUM('waiting', 'playing', 'completed') DEFAULT 'waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);
```
##🖼️ Screenshots 🔵 Home Page Welcome players with an interactive and engaging interface. Home Page

###🔴 Two-Player Game Mode Players guess the secret number with real-time feedback and hints. Two-Player Game

###🟢 Game History Review past games, winners, and player stats. Game History

###🚀 Getting Started Prerequisites PHP (v7.4 or later) MySQL A web server (e.g., Apache, Nginx, or XAMPP) Installation Clone the repository:

