<?php
session_start();

use Database\DatabaseConnection;

include_once 'util.php';
include_once 'database.php';

$databaseConnection = new DatabaseConnection();
$db = $databaseConnection->getMysqli();

$_SESSION['board'] = [];
$hand = [0=>["Q"=>1,"B"=>2,"S"=>2,"A"=>3,"G"=>3],1=>["Q"=>1,"B"=>2,"S"=>2,"A"=>3,"G"=>3]];
$_SESSION['hand'] = $hand;
$_SESSION['player'] = 0;

$db->prepare('INSERT INTO games VALUES ()')->execute();
$_SESSION['game_id'] = $db->insert_id;

header('Location: index.php');
