<?php
namespace Game;
session_start();

use Database\DatabaseConnection;

include_once 'util.php';
include_once 'database.php';

class Restart {
    private $db;

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->db = $databaseConnection->getMysqli();
    }

    public function restartGame() {
        $_SESSION['board'] = [];
        $hand = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $_SESSION['hand'] = $hand;
        $_SESSION['player'] = 0;

        $this->db->prepare('INSERT INTO games VALUES ()')->execute();
        $_SESSION['game_id'] = $this->db->insert_id;
    }
}

$databaseConnection = new DatabaseConnection();
$restart = new Restart($databaseConnection);
$restart->restartGame();
header('Location: index.php');
