<?php
/**
 * Database configuration and PDO connection setup.
 *
 * This file starts the session and creates a shared PDO instance
 * for the application to interact with the MySQL database.
 */
session_start();

// MySQL connection settings
$host = "localhost";
$user = "root";
$password = "";
$dbname = "onetomany";
$dsn = "mysql:host={$host};dbname={$dbname}";

// Initialize PDO and configure timezone for database queries
$pdo = new PDO($dsn, $user, $password);
$pdo->exec("SET time_zone = '+08:00';");

?>