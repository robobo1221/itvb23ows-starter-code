<?php
namespace Game;
session_start();

use Database\GameState;
use Database\DatabaseConnection;

include_once 'util.php';
include_once 'database.php';

class Undo {
    private $db;

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->db = $databaseConnection->getMysqli();
    }

    public function undoMove() {
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE id = ' . $_SESSION['last_move']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array();
        $_SESSION['last_move'] = $result[5];
        GameState::setState($result[6]);
    }
}

$databaseConnection = new DatabaseConnection();
$undo = new Undo($databaseConnection);
$undo->undoMove();
header('Location: index.php');
