-- Rooms table (parent)
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100) NOT NULL,
    theme VARCHAR(100),
    max_capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Players table (child)
CREATE TABLE players (
    player_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    player_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Foreign key (many players → one room)
    CONSTRAINT fk_room
        FOREIGN KEY (room_id)
        REFERENCES rooms(room_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);