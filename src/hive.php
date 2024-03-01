<?php

use Util\BoardUtil;
use Game\GameRules;
use Database\DatabaseConnection;
use Database\GameState;

include_once "database.php";

class Hive {
    private DataHandler $data;
    private $hand;
    private $player;
    private $board;

    public function __construct($data) {
        $this->data = $data;

        session_start();

        if (!isset($_SESSION['board'])) {
            $this->restart();
        }

        $this->player = $_SESSION['player'];
        $this->board = $_SESSION['board'];
        $this->hand = $_SESSION['hand'];
    }

    public function move($from, $to) {
        // Logic for moving a piece on the board
        if (!$this->checkValidMove($from, $to)) {
            return;
        }

        $tile = array_pop($this->board[$from]);

        if (isset($_SESSION['error'])) {
            if (isset($this->board[$from])) {
                array_push($this->board[$from], $tile);
            } else {
                $this->board[$from] = [$tile];
            }
        } else {
            if (isset($this->board[$to])) {
                array_push($this->board[$to], $tile);
            } else {
                $this->board[$to] = [$tile];
            }

            $this->player = 1 - $this->player;
            $this->saveMove($from, $to, 'move');
        }
    }

    public function checkValidMove($from, $to) {
        // Logic for checking if a move is valid
        if (!isset($this->board[$from])) {
            $_SESSION['error'] = 'Board position is empty';
            return false;
        } elseif ($this->board[$from][count($this->board[$from]) - 1][0] != $this->player) {
            $_SESSION['error'] = "Tile is not owned by player";
            return false;
        } elseif ($this->hand[$this->player]['Q']) {
            $_SESSION['error'] = "Queen bee is not played";
            return false;
        } else {
            $tile = array_pop($this->board[$from]);
            if (!BoardUtil::hasNeighBour($to, $this->board)) {
                $_SESSION['error'] = "Move would split hive";
                return false;
            } else {
                $all = array_keys($this->board);
                $queue = [array_shift($all)];
                while ($queue) {
                    $next = explode(',', array_shift($queue));
                    foreach (BoardUtil::$OFFSETS as $pq) {
                        list($p, $q) = $pq;
                        $p += $next[0];
                        $q += $next[1];
                        if (in_array("$p,$q", $all)) {
                            $queue[] = "$p,$q";
                            $all = array_diff($all, ["$p,$q"]);
                        }
                    }
                }
                if ($all) {
                    $_SESSION['error'] = "Move would split hive";
                    return false;
                } else {
                    if ($from == $to) {
                        $_SESSION['error'] = 'Tile must move';
                        return false;
                    } else if (isset($this->board[$to]) && $tile[1] != "B") {
                        $_SESSION['error'] = 'Tile not empty';
                        return false;
                    } else if ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!BoardUtil::slide($this->board, $from, $to)) {
                            $_SESSION['error'] = 'Tile must slide';
                            return false;
                        }  
                    }
                }
            }
        }

        return true;
    }

    public function pass() {
        // Logic for passing the turn

        // TODO: Implement pass check valid
        $this->player = 1 - $this->player;
        $this->saveMove(null, null, 'move');
    }

    public function play($piece, $to) {
        // Logic for playing the game
        if (!$this->checkValidPlay($piece, $to)) {
            return;
        }

        $this->board[$to] = [[$this->player, $piece]];
        $this->hand[$this->player][$piece]--;
        $this->player = 1 - $this->player;
        $this->saveMove($piece, $to, 'play');
    }

    public function checkValidPlay($piece, $to) {
        // Logic for checking if a play is valid
        if (!GameRules::playerHasTile($this->getHand(), $piece)) {
            $_SESSION['error'] = "Player does not have tile";
            return false;
        } elseif (GameRules::isPositionOccupied($this->board, $to)) {
            $_SESSION['error'] = 'Board position is not empty';
            return false;
        } elseif (!GameRules::positionHasNeighBour($this->board, $to)) {
            $_SESSION['error'] = "board position has no neighbour";
            return false;
        } elseif (GameRules::positionHasOpposingNeighBour($this->getHand(), $this->player, $to, $this->board)) {
            $_SESSION['error'] = "Board position has opposing neighbour";
            return false;
        } elseif (GameRules::needsToPlayQueenBee($this->getHand())) {
            $_SESSION['error'] = 'Must play queen bee';
            return false;
        }

        return true;
    }

    public function restart() {
        // Logic for restarting the game
        $this->board = [];
        $this->hand = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $this->player = 0;

        $_SESSION['game_id'] = $this->data->initGame();
    }

    public function undo() {
        // Logic for undoing the last move
    }

    public function getState() {
        // Logic for getting the game state
        return serialize([$this->hand, $this->board, $this->player]);
    }

    public function saveState() {
        // Logic for saving the game state to the database
        $_SESSION['hand'] = $this->hand;
        $_SESSION['board'] = $this->board;
        $_SESSION['player'] = $this->player;
    }

    public function saveMove($from, $to, $type) {
        // Logic for saving a move to the database
        $this->saveState();

        $inset_id = $this->data->registerMove($_SESSION['game_id'], $type, $from, $to, $_SESSION['last_move'], $this->getState());
        $_SESSION['last_move'] = $inset_id;
    }

    public function manageHand() {
        // Logic for managing the player's hand
    }

    public function managePlayer() {
        // Logic for managing the player's information
    }

    public function manageBoard() {
        // Logic for managing the game board
    }

    public function getHand() {
        return $this->hand[$this->player];
    }
}

$db = new DatabaseConnection();
$dataHandler = new DataHandler($db->getMysqli());
$game = new Hive($dataHandler);