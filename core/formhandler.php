<?php
require_once 'dbconfig.php';
require_once 'models.php';
require_once 'validate.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'register':
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);

        if ($password !== $confirm_password) {
            header("Location: ../register.php?error=Passwords do not match");
            exit();
        }

        $passwordValidation = validatePassword($password);
        if ($passwordValidation !== true) {
            header("Location: ../register.php?error=" . urlencode($passwordValidation));
            exit();
        }

        if (User::usernameExists($pdo, $username)) {
            header("Location: ../register.php?error=Username already exists");
            exit();
        }

        if (User::emailExists($pdo, $email)) {
            header("Location: ../register.php?error=Email already exists");
            exit();
        }

        if (User::register($pdo, $username, $email, $password)) {
            header("Location: ../login.php?message=Registration successful");
        } else {
            header("Location: ../register.php?error=Registration failed");
        }
        break;

    case 'login':
        $user = User::login($pdo, sanitizeInput($_POST['username']), $_POST['password']);
        if ($user) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../index.php");
        } else {
            header("Location: ../login.php?error=Invalid credentials");
        }
        break;

    case 'logout':
        session_destroy();
        header("Location: ../login.php");
        break;

    case 'create_room':
        $user_id = $_SESSION['user_id'] ?? null;
        if (Room::insert($pdo, sanitizeInput($_POST['room_name']), sanitizeInput($_POST['theme']), $_POST['max_capacity'], $user_id)) {
            header("Location: ../index.php");
        } else {
            echo "Room creation failed";
        }
        break;

    case 'update_room':
        $user_id = $_SESSION['user_id'] ?? null;
        if (Room::update($pdo, sanitizeInput($_POST['room_name']), sanitizeInput($_POST['theme']), $_POST['max_capacity'], $_POST['room_id'], $user_id)) {
            header("Location: ../index.php");
        } else {
            echo "Room update failed";
        }
        break;

    case 'delete_room':
        $user_id = $_SESSION['user_id'] ?? null;
        if (Room::delete($pdo, $_POST['room_id'], $user_id)) {
            header("Location: ../index.php");
        } else {
            echo "Room deletion failed";
        }
        break;

    case 'create_player':
        $user_id = $_SESSION['user_id'] ?? null;
        if (Player::insert($pdo, sanitizeInput($_POST['player_name']), sanitizeInput($_POST['email']), $_POST['room_id'], $user_id)) {
            header("Location: ../index.php");
        } else {
            echo "Player creation failed";
        }
        break;

    case 'update_player':
        $user_id = $_SESSION['user_id'] ?? null;
        if (Player::update($pdo, sanitizeInput($_POST['player_name']), sanitizeInput($_POST['email']), $_POST['player_id'], $user_id)) {
            header("Location: ../index.php");
        } else {
            echo "Player update failed";
        }
        break;

    case 'delete_player':
        $user_id = $_SESSION['user_id'] ?? null;
        if (Player::delete($pdo, $_POST['player_id'], $user_id)) {
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