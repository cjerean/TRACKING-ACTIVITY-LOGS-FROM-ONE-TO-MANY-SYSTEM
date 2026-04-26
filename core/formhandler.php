<?php

require_once 'dbconfig.php';
require_once 'models.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create_room':
        if (Room::insert($pdo, $_POST['room_name'], $_POST['theme'], $_POST['max_capacity'])) {
            header("Location: ../index.php");
        } else {
            echo "Room creation failed";
        }
        break;

    case 'update_room':
        if (Room::update($pdo, $_POST['room_name'], $_POST['theme'], $_POST['max_capacity'], $_POST['room_id'])) {
            header("Location: ../index.php");
        } else {
            echo "Room update failed";
        }
        break;

    case 'delete_room':
        if (Room::delete($pdo, $_POST['room_id'])) {
            header("Location: ../index.php");
        } else {
            echo "Room deletion failed";
        }
        break;

    case 'create_player':
        if (Player::insert($pdo, $_POST['player_name'], $_POST['email'], $_POST['room_id'])) {
            header("Location: ../index.php");
        } else {
            echo "Player creation failed";
        }
        break;

    case 'update_player':
        if (Player::update($pdo, $_POST['player_name'], $_POST['email'], $_POST['player_id'])) {
            header("Location: ../index.php");
        } else {
            echo "Player update failed";
        }
        break;

    case 'delete_player':
        if (Player::delete($pdo, $_POST['player_id'])) {
            header("Location: ../index.php");
        } else {
            echo "Player deletion failed";
        }
        break;

    default:
        echo "Invalid action";
        break;
}

?>