<?php
namespace Game;
session_start();

use Database\DatabaseConnection;
use Database\GameState;

include_once 'database.php';

class Pass {
    private $db;

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->db = $databaseConnection->getMysqli();
    }

    public function passMove() {
        $sql_statement = 'insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "pass", null, null, ?, ?)';
        $stmt = $this->db->prepare($sql_statement);
        $gameState = GameState::getState();
        $stmt->bind_param('iis', $_SESSION['game_id'], $_SESSION['last_move'], $gameState);
        $stmt->execute();
        $_SESSION['last_move'] = $this->db->insert_id;
        $_SESSION['player'] = 1 - $_SESSION['player'];
    }
}

$databaseConnection = new DatabaseConnection();
$pass = new Pass($databaseConnection);
$pass->passMove();
header('Location: index.php');