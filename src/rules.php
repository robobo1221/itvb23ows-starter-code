<?php
namespace Game;

use Util\BoardUtil;
require_once("util.php");

class GameRules {
    static function playerHasTile($hand, $piece): bool {
        return (bool)$hand[$piece];
    }

    static function isPositionOccupied($board, $position): bool {
        return isset($board[$position]);
    }

    static function positionHasNeighBour($position, $board): bool {
        return !count($board) || BoardUtil::hasNeighBour($position, $board);
    }

    static function positionHasOpposingNeighBour($hand, $player, $position, $board): bool {
        return !(array_sum($hand) < 11) || BoardUtil::neighboursAreSameColor($player, $position, $board);
    }
}