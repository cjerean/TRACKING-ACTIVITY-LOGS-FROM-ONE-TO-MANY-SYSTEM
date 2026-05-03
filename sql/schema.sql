CREATE TABLE users (
	user_id INT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(50) UNIQUE NOT NULL,
	email VARCHAR(100) UNIQUE NOT NULL,
	password VARCHAR(255) NOT NULL, -- For hashed passwords
	role ENUM('admin', 'user') DEFAULT 'user',
	last_login TIMESTAMP NULL,
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Audit table to track user actions
CREATE TABLE audit_log (
	audit_id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	action VARCHAR(50) NOT NULL, -- 'INSERT', 'UPDATE', 'DELETE'
	table_name VARCHAR(50) NOT NULL,
	record_id INT NOT NULL,
	action_details TEXT, -- JSON or description of what changed
	ip_address VARCHAR(45), -- Support IPv4 and IPv6
	user_agent TEXT,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	-- Foreign key to users table
	CONSTRAINT fk_audit_user
		FOREIGN KEY (user_id)
		REFERENCES users(user_id)
		ON DELETE CASCADE
);

-- Rooms table (parent)
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100) NOT NULL,
    theme VARCHAR(100),
    max_capacity INT NOT NULL,
    created_by INT, -- Track who created the room
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign key to track creator
    CONSTRAINT fk_room_creator
        FOREIGN KEY (created_by)
        REFERENCES users(user_id)
        ON DELETE SET NULL
);

-- Players table (child)
CREATE TABLE players (
    player_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    player_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_by INT, -- Track who added the player
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign key (many players → one room)
    CONSTRAINT fk_room
        FOREIGN KEY (room_id)
        REFERENCES rooms(room_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    -- Foreign key to track creator
    CONSTRAINT fk_player_creator
        FOREIGN KEY (created_by)
        REFERENCES users(user_id)
        ON DELETE SET NULL
);