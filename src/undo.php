<?php
session_start();

use Database\GameState;
use Database\DatabaseConnection;

include_once 'util.php';
include_once 'database.php';

$databaseConnection = new DatabaseConnection();
$db = $databaseConnection->getMysqli();

$stmt = $db->prepare('SELECT * FROM moves WHERE id = '.$_SESSION['last_move']);
$stmt->execute();
$result = $stmt->get_result()->fetch_array();
$_SESSION['last_move'] = $result[5];
GameState::setState($result[6]);

header('Location: index.php');
