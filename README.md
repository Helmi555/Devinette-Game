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
