<?php
namespace Util;

use Game\GameRules;
include_once "rules.php";

class BoardUtil {
    public static $OFFSETS = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

    public static function isNeighbour($a, $b) {
        $a = explode(',', $a);
        $b = explode(',', $b);

        $returnStatement = $a[0] == $b[0] && abs($a[1] - $b[1]) == 1;
        $returnStatement = $returnStatement || ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1);
        $returnStatement = $returnStatement || ($a[0] + $a[1] == $b[0] + $b[1]);

        return $returnStatement;
    }

    public static function hasNeighBour($a, $board) {
        foreach (array_keys($board) as $b) {
            if (self::isNeighbour($a, $b)) {
                return true;
            }
        }
    }

    public static function neighboursAreSameColor($player, $a, $board) {
        foreach ($board as $b => $st) {
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $player && self::isNeighbour($a, $b)) {
                return false;
            }
        }
        return true;
    }

    public static function len($tile) {
        return $tile ? count($tile) : 0;
    }

    public static function slide($board, $from, $to) {
        if (!self::hasNeighBour($to, $board) || !self::isNeighbour($from, $to)) {
            return false;
        }
        $b = explode(',', $to);
        $common = [];
        foreach (self::$OFFSETS as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if (self::isNeighbour($from, $p.",".$q)) {
                $common[] = $p.",".$q;
            }
        }
        print_r($board);
        if (!$board[$common[0]] && !$board[$common[1]] && !$board[$from] && !$board[$to]) {
            return false;
        }
        return min(self::len($board[$common[0]]), self::len($board[$common[1]])) <= max(self::len($board[$from]), self::len($board[$to]));
    }

    public static function getAvailableTiles($hand) {
        $tiles = [];

        foreach ($hand as $tile => $ct) {
            // Make sure we don't add the tiles once there are no one left inside of the array.
            if ($ct == 0) {
                continue;
            }

            $tiles[] = $tile;
        }

        return $tiles;
    }

    public static function getAvailablePlays($hand, $board, $player) {
        $to = [];
        foreach (self::$OFFSETS as $pq) {
            foreach (array_keys($board) as $pos) {
                $pq2 = explode(',', $pos);
                $tempTo = ($pq[0] + $pq2[0]).','.($pq[1] + $pq2[1]);
                
                // Make sure we only add the move to the list if it's valid.
                if (GameRules::isValidListedMove($hand, $board, $player, $tempTo)) {
                    $to[] = $tempTo;
                }
            }
        }
        
        $to = array_unique($to);
        if (!count($to)) {
            $to[] = '0,0';
        }

        return $to;
    }

    public static function getAvailableFrom($board, $player) {
        $from = [];

        foreach ($board as $pos => $playerPiece) {
            // Make sure discard all the pieces that aren't of the current player.
            if ($player !== $playerPiece[0][0]) {
                continue;
            }

            $from[] = $pos;
        }

        return $from;
    }
}
