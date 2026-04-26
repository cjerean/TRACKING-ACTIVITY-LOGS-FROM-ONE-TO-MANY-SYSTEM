<?php

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

    public static function insert($pdo, $room_name, $theme, $max_capacity) {
        $sql = "INSERT INTO rooms (room_name, theme, max_capacity) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$room_name, $theme, $max_capacity]);
    }

    public static function update($pdo, $room_name, $theme, $max_capacity, $room_id) {
        $sql = "UPDATE rooms SET room_name = ?, theme = ?, max_capacity = ? WHERE room_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$room_name, $theme, $max_capacity, $room_id]);
    }

    public static function delete($pdo, $room_id) {
        $sql = "DELETE FROM rooms WHERE room_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$room_id]);
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

    public static function insert($pdo, $player_name, $email, $room_id) {
        $sql = "INSERT INTO players (player_name, email, room_id) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$player_name, $email, $room_id]);
    }

    public static function update($pdo, $player_name, $email, $player_id) {
        $sql = "UPDATE players SET player_name = ?, email = ? WHERE player_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$player_name, $email, $player_id]);
    }

    public static function delete($pdo, $player_id) {
        $sql = "DELETE FROM players WHERE player_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$player_id]);
    }
}

?>