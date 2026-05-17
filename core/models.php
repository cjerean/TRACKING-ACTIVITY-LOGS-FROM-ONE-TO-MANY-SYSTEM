<?php
/**
 * Application models for rooms, players, users, and audit logging.
 *
 * Each model method is responsible for a single database operation and
 * optional audit logging for tracking user activity.
 */

class Room {
    public static function getAll($pdo) {
        $sql = "SELECT * FROM rooms";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getById($pdo, $room_id) {
        $sql = "SELECT * FROM rooms WHERE room_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$room_id]);
        return $stmt->fetch();
    }

    public static function insert($pdo, $room_name, $theme, $max_capacity, $created_by = null) {
        $sql = "INSERT INTO rooms (room_name, theme, max_capacity, created_by) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$room_name, $theme, $max_capacity, $created_by]);

        if ($result && $created_by) {
            $room_id = $pdo->lastInsertId();
            User::logAction($pdo, $created_by, 'INSERT', 'rooms', $room_id, "Created room: $room_name");
        }

        return $result;
    }

    public static function update($pdo, $room_name, $theme, $max_capacity, $room_id, $updated_by = null) {
        $sql = "UPDATE rooms SET room_name = ?, theme = ?, max_capacity = ? WHERE room_id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$room_name, $theme, $max_capacity, $room_id]);

        if ($result && $updated_by) {
            User::logAction($pdo, $updated_by, 'UPDATE', 'rooms', $room_id, "Updated room: $room_name");
        }

        return $result;
    }

    public static function delete($pdo, $room_id, $deleted_by = null) {
        // Get room name before deletion for logging
        $room = self::getById($pdo, $room_id);
        $room_name = $room ? $room['room_name'] : 'Unknown';

        $sql = "DELETE FROM rooms WHERE room_id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$room_id]);

        if ($result && $deleted_by) {
            User::logAction($pdo, $deleted_by, 'DELETE', 'rooms', $room_id, "Deleted room: $room_name");
        }

        return $result;
    }

    public static function search($pdo, $keyword, $user_id = null) {
        $sql = "SELECT * FROM rooms WHERE room_name LIKE ? OR theme LIKE ?";
        $stmt = $pdo->prepare($sql);
        $searchParam = "%$keyword%";
        $stmt->execute([$searchParam, $searchParam]);
        $results = $stmt->fetchAll();

        if ($user_id) {
            User::logAction($pdo, $user_id, 'READ', 'rooms', 0, "Searched rooms for: $keyword");
        }

        return $results;
    }
}

class Player {
    public static function getByRoom($pdo, $room_id) {
        $sql = "SELECT * FROM players WHERE room_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$room_id]);
        return $stmt->fetchAll();
    }

    public static function getById($pdo, $player_id) {
        $sql = "SELECT * FROM players WHERE player_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$player_id]);
        return $stmt->fetch();
    }

    public static function insert($pdo, $player_name, $email, $room_id, $created_by = null) {
        $sql = "INSERT INTO players (player_name, email, room_id, created_by) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$player_name, $email, $room_id, $created_by]);

        if ($result && $created_by) {
            $player_id = $pdo->lastInsertId();
            User::logAction($pdo, $created_by, 'INSERT', 'players', $player_id, "Added player: $player_name to room $room_id");
        }

        return $result;
    }

    public static function update($pdo, $player_name, $email, $player_id, $updated_by = null) {
        $sql = "UPDATE players SET player_name = ?, email = ? WHERE player_id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$player_name, $email, $player_id]);

        if ($result && $updated_by) {
            User::logAction($pdo, $updated_by, 'UPDATE', 'players', $player_id, "Updated player: $player_name");
        }

        return $result;
    }

    public static function delete($pdo, $player_id, $deleted_by = null) {
        // Get player name before deletion for logging
        $player = self::getById($pdo, $player_id);
        $player_name = $player ? $player['player_name'] : 'Unknown';

        $sql = "DELETE FROM players WHERE player_id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$player_id]);

        if ($result && $deleted_by) {
            User::logAction($pdo, $deleted_by, 'DELETE', 'players', $player_id, "Deleted player: $player_name");
        }

        return $result;
    }

    public static function search($pdo, $keyword, $user_id = null) {
        $sql = "SELECT p.*, r.room_name FROM players p LEFT JOIN rooms r ON p.room_id = r.room_id WHERE p.player_name LIKE ? OR p.email LIKE ?";
        $stmt = $pdo->prepare($sql);
        $searchParam = "%$keyword%";
        $stmt->execute([$searchParam, $searchParam]);
        $results = $stmt->fetchAll();

        if ($user_id) {
            User::logAction($pdo, $user_id, 'READ', 'players', 0, "Searched players for: $keyword");
        }

        return $results;
    }
}

class User {
    public static function usernameExists($pdo, $username) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    public static function emailExists($pdo, $email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public static function register($pdo, $username, $email, $password) {
        if (self::usernameExists($pdo, $username)) {
            return false;
        }

        if (self::emailExists($pdo, $email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$username, $email, $hashedPassword]);
    }

    public static function login($pdo, $username, $password) {
        $sql = "SELECT user_id, username, email, password, role FROM users WHERE username = ? OR email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $updateSql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$user['user_id']]);

            unset($user['password']); // Remove password from returned data
            return $user;
        }
        return false;
    }

    public static function getById($pdo, $user_id) {
        $sql = "SELECT user_id, username, email, role, last_login, date_added FROM users WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    public static function getAll($pdo) {
        $sql = "SELECT user_id, username, email, role, last_login, date_added FROM users ORDER BY date_added DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function logAction($pdo, $user_id, $action, $table_name, $record_id, $action_details = null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $sql = "INSERT INTO audit_log (user_id, action, table_name, record_id, action_details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$user_id, $action, $table_name, $record_id, $action_details, $ip, $user_agent]);
    }

    public static function getAuditLog($pdo, $limit = 50) {
        $limit = (int) $limit;
        $sql = "SELECT a.*, u.username FROM audit_log a JOIN users u ON a.user_id = u.user_id ORDER BY a.created_at DESC LIMIT " . $limit;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>