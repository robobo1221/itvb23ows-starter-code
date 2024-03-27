<?php

use Util\BoardUtil;
use Game\GameRules;
use SebastianBergmann\Type\TypeName;

class Hive {
    public ?DataService $data;
    public ?AiService $ai;
    public $hand;
    public $player;
    public $board;

    public function __construct($data = null, $ai = null) {
        $this->data = $data;
        $this->ai = $ai;

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
        $this->board[$to] = [$tile];

        $this->player = 1 - $this->player;
        $this->saveMove($from, $to, 'move');
    }

    public function checkValidMove($from, $to) {
        // Logic for checking if a move is valid
        if (!isset($this->board[$from])) {
            $_SESSION['error'] = 'Board position is empty';
        } elseif (isset($this->board[$from][count($this->board[$from]) - 1][0]) && $this->board[$from][count($this->board[$from]) - 1][0] != $this->player) {
            $_SESSION['error'] = "Tile is not owned by player";
        } elseif ($this->hand[$this->player]['Q']) {
            $_SESSION['error'] = "Queen bee is not played";
        } else {
            $board = $this->board;
            $tile = array_pop($board[$from]);

            if (!BoardUtil::hasNeighBour($to, $board)) {
                $_SESSION['error'] = "Move would split hive";
            } else {
                $all = array_keys($board);
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
                } else {
                    if ($from == $to) {
                        $_SESSION['error'] = 'Tile must move';
                    } elseif (isset($board[$to]) && $tile[1] != "B") {
                        $_SESSION['error'] = 'Tile not empty';
                    } elseif (($tile[1] == "Q" || $tile[1] == "B")) {
                        if (!BoardUtil::slide($board, $from, $to)) {
                            $_SESSION['error'] = 'Tile must slide';
                        }  
                    } elseif ($tile[1] == "G" && !BoardUtil::grassHopper($board, $from, $to)) {
                        $_SESSION['error'] = 'Grasshopper must jump over other tiles';
                    } elseif ($tile[1] == "A" && !BoardUtil::ant($board, $from, $to)) {
                        $_SESSION['error'] = 'Ant must move to border and not be surrounded by other tiles or pushed by other tiles';
                    } elseif ($tile[1] == "S" && !BoardUtil::spider($board, $from, $to)) {
                        $_SESSION['error'] = 'Spider must move exactly three spaces or cannot explore same space twice.';
                    } else {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function pass() {
        // Logic for passing the turn
        if (!$this->checkPass()) {
            return;
        }

        $this->player = 1 - $this->player;
        $this->saveMove(null, null, 'move');
    }

    public function checkPass() {
        if (array_sum($this->hand[$this->player]) > 0 && count($this->getPossibleMoves()) > 0) {
            return false;
        }

        foreach ($this->board as $pos => $tile) {
            if ($tile[0][0] == $this->player) {
                $moves = $this->getAllMoves();

                foreach ($moves as $move) {
                    if ($this->checkValidMove($pos, $move)) {
                        return false;
                    }
                }
            }
        }

        return true;
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
        $isValid = true;
        $errorMessage = "";
    
        // Logic for checking if a play is valid
        if (!GameRules::playerHasTile($hand = $this->getHand($this->player), $piece)) {
            $errorMessage = "Player does not have tile";
        } elseif (GameRules::isPositionOccupied($this->board, $to)) {
            $errorMessage = 'Board position is not empty';
        } elseif (!GameRules::positionHasNeighBour($this->board, $to)) {
            $errorMessage = "board position has no neighbour";
        } elseif (GameRules::positionHasOpposingNeighBour($hand, $this->player, $to, $this->board)) {
            $errorMessage = "Board position has opposing neighbour";
        } elseif (GameRules::needsToPlayQueenBee($hand) && $piece != "Q") {
            $errorMessage = 'Must play queen bee';
        } else {
            return true;
        }
    
        $_SESSION['error'] = $errorMessage;
    
        return false;
    }

    public function restart() {
        // Logic for restarting the game
        $this->board = [];
        $this->hand = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $this->player = 0;

        $this->saveState();
        if ($this->data !== null) {
            $_SESSION['game_id'] = $this->data->initGame();
        }
    }

    public function undo() {
        // Logic for undoing the last move
        if ($this->data == null || !isset($_SESSION['last_move']) || !isset($_SESSION['game_id'])) {
            return;
        }

        $restoreMove = $this->data->getMove($_SESSION['last_move']-1);

        if ($restoreMove === null) {
            // No moves to undo
            $this->restart();
            return;
        }

        if ($_SESSION['game_id'] != $restoreMove['game_id']) {
            $this->restart();
            return;
        }

        $lastState = unserialize($restoreMove['state']);
        $this->hand = $lastState[0];
        $this->board = $lastState[1];
        $this->player = $lastState[2];

        $this->saveState();

        $this->data->deleteMove($_SESSION['last_move']);
        $_SESSION['last_move'] = $restoreMove['id'];
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

        if (!isset($_SESSION['last_move'])){
            $_SESSION['last_move'] = $this->data->getLastMove()[0];
        }

        if ($this->data !== null) {
            $inset_id = $this->data->registerMove($_SESSION['game_id'], $type, $from, $to, $_SESSION['last_move'], $this->getState());
            $_SESSION['last_move'] = $inset_id;
        }
    }

    public function renderBoard() {
        // Logic for rendering the game board
        $min_p = 1000;
        $min_q = 1000;
        foreach ($this->board as $pos => $tile) {
            $pq = explode(',', $pos);
            if ($pq[0] < $min_p) {
                $min_p = $pq[0];
            }

            if ($pq[1] < $min_q) {
                $min_q = $pq[1];
            }
        }
        foreach (array_filter($this->board) as $pos => $tile) {
            $pq = explode(',', $pos);
            $pq[0];
            $pq[1];
            $h = count($tile);
            echo '<div class="tile player';
            echo $tile[$h-1][0];
            if ($h > 1) {
                echo ' stacked';
            }
            echo '" style="left: ';
            echo ($pq[0] - $min_p) * 4 + ($pq[1] - $min_q) * 2;
            echo 'em; top: ';
            echo ($pq[1] - $min_q) * 4;
            echo "em;\">($pq[0],$pq[1])<span>";
            echo $tile[$h-1][1];
            echo '</span></div>';
        }
    }

    public function printWinner() {
        if (BoardUtil::lost(0, $this->board)) {
            echo "Black wins!";
        } elseif (BoardUtil::lost(1, $this->board)) {
            echo "White wins!";
        } elseif (BoardUtil::draw($this->board)) {
            echo "Draw!";
        }
    }

    public function playAiMove() {
        // Logic for processing the AI
        $moves = $this->data->getPreviousGameMoves($_SESSION['game_id']);
        $moveNum = $moves->num_rows;

        $ai = $this->ai->getMove($this->board, $this->hand, $this->player, $moveNum);

        if (!isset($ai)) {
            $_SESSION['error'] = "AI failed to make a move";
            return;
        }

        $moveType = $ai[0];
        $moveFrom = $ai[1];
        $moveTo = $ai[2];

        if ($moveType == "play") {
            $this->board[$moveTo] = [[$this->player, $moveFrom]];
            $this->hand[$this->player][$moveFrom]--;
            $this->player = 1 - $this->player;
            $this->saveMove($moveFrom, $moveTo, 'play');
        } elseif ($moveType == "move") {
            $tile = array_pop($this->board[$moveFrom]);
            $this->board[$moveTo] = [$tile];
            $this->player = 1 - $this->player;
            $this->saveMove($moveFrom, $moveTo, 'move');
        } elseif ($moveType == "pass") {
            $this->player = 1 - $this->player;
            $this->saveMove(null, null, 'move');
        }
    }

    public function getAllMoves() {
        // Logic for getting all the moves
        return BoardUtil::getAllPlays($this->board);
    }

    public function getPossibleMoves() {
        // Logic for getting the possible moves
        return BoardUtil::getAvailablePlays($this->hand[$this->player], $this->board, $this->player);
    }

    public function getPossibleFromMoves() {
        // Logic for getting the possible from moves
        return BoardUtil::getAvailableFrom($this->board, $this->player);
    }

    public function getAvailableTiles() {
        // Logic for getting the available tiles
        return BoardUtil::getAvailableTiles($this->hand[$this->player]);
    }

    public function getBoard() {
        return $this->board;
    }

    public function getPlayer() {
        return $this->player;
    }

    public function getHand($player) {
        return $this->hand[$player];
    }
}