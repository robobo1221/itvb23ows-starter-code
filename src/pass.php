<?php
session_start();

use Database\DatabaseConnection;
use Database\GameState;

include_once 'database.php';

$databaseConnection = new DatabaseConnection();
$db = $databaseConnection->getMysqli();

$sql_statement = 'insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "pass", null, null, ?, ?)';
$stmt = $db->prepare($sql_statement);
$gameState = GameState::getState();
$stmt->bind_param('iis', $_SESSION['game_id'], $_SESSION['last_move'], $gameState);
$stmt->execute();
$_SESSION['last_move'] = $db->insert_id;
$_SESSION['player'] = 1 - $_SESSION['player'];

header('Location: index.php');
