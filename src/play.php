<?php
namespace Game;
session_start();

use Util\BoardUtil;
use Game\GameRules;
use Database\DatabaseConnection;
use Database\GameState;

include_once 'util.php';
include_once 'database.php';
include_once 'rules.php';

class Play {
    private $db;

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->db = $databaseConnection->getMysqli();
    }

    public function playMove($piece, $to) {
        $player = $_SESSION['player'];
        $board = $_SESSION['board'];
        $hand = $_SESSION['hand'][$player];

        if (!GameRules::playerHasTile($hand, $piece)) {
            $_SESSION['error'] = "Player does not have tile";
        } elseif (GameRules::isPositionOccupied($board, $to)) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (!GameRules::positionHasNeighBour($board, $to)) {
            $_SESSION['error'] = "board position has no neighbour";
        } elseif (GameRules::positionHasOpposingNeighBour($hand, $player, $to, $board)) {
            $_SESSION['error'] = "Board position has opposing neighbour";
        } elseif (GameRules::needsToPlayQueenBee($hand)) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
            $_SESSION['hand'][$player][$piece]--;
            $_SESSION['player'] = 1 - $_SESSION['player'];
            $sql_statement = 'insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "play", ?, ?, ?, ?)';
            $stmt = $this->db->prepare($sql_statement);
            $state = GameState::getState();
            $stmt->bind_param('issis', $_SESSION['game_id'], $piece, $to, $_SESSION['last_move'], $state);
            $stmt->execute();
            $_SESSION['last_move'] = $this->db->insert_id;
        }
    }
}

$databaseConnection = new DatabaseConnection();
$play = new Play($databaseConnection);

$piece = $_POST['piece'];
$to = $_POST['to'];
$play->playMove($piece, $to);
header('Location: index.php');
