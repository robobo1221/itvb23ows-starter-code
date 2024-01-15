<?php

use Database;

session_start();

$db = include_once 'database.php';
$sql_statement = 'insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "pass", null, null, ?, ?)';
$stmt = $db->prepare($sql_statement);
$stmt->bind_param('iis', $_SESSION['game_id'], $_SESSION['last_move'], Database\getState());
$stmt->execute();
$_SESSION['last_move'] = $db->insert_id;
$_SESSION['player'] = 1 - $_SESSION['player'];

header('Location: index.php');
