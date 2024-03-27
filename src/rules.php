<?php
namespace Game;

use Util\BoardUtil;
require_once "util.php";

class GameRules {
    public static function playerHasTile($hand, $piece): bool {
        return (bool)$hand[$piece];
    }

    public static function isPositionOccupied($board, $position): bool {
        return isset($board[$position]);
    }

    public static function positionHasNeighBour($board, $position): bool {
        return !count($board) || BoardUtil::hasNeighBour($position, $board);
    }

    public static function positionHasOpposingNeighBour($hand, $player, $position, $board): bool {
        return array_sum($hand) < 11 && !BoardUtil::neighboursAreSameColor($player, $position, $board);
    }
    
    public static function needsToPlayQueenBee($hand): bool {
        return array_sum($hand) <= 8 && $hand['Q'];
    }

    public static function isValidListedMove($hand, $board, $player, $to): bool {
        return !(
            self::isPositionOccupied($board, $to) ||
            !self::positionHasNeighBour($board, $to) ||
            self::positionHasOpposingNeighBour($hand, $player, $to, $board) ||
            self::needsToPlayQueenBee($hand) && $hand['Q'] != 1
        );
    }
}