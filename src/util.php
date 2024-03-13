<?php
namespace Util;

use Game\GameRules;
include_once "rules.php";

class BoardUtil {
    public static $OFFSETS = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

    public static function isNeighbour($a, $b) {
        $a = explode(',', $a);
        $b = explode(',', $b);
        
        return ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) ||
               ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) ||
               ($a[0] + $a[1] == $b[0] + $b[1]);
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

        if (!isset($board[$common[0]]) || !isset($board[$common[1]]) || !isset($board[$from]) || !isset($board[$to])) {
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

    public static function getAllPlays($board) {
        $to = [];
        foreach (self::$OFFSETS as $pq) {
            foreach (array_keys($board) as $pos) {
                $pq2 = explode(',', $pos);
                $to[] = ($pq[0] + $pq2[0]).','.($pq[1] + $pq2[1]);
            }
        }
        
        $to = array_unique($to);
        if (!count($to)) {
            $to[] = '0,0';
        }

        return $to;
    }

    public static function getAvailablePlays($hand, $board, $player) {
        $to = [];
        foreach (self::getAllPlays($board) as $tempTo) {
            if (GameRules::isValidListedMove($hand, $board, $player, $tempTo)) {
                $to[] = $tempTo;
            }
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

    public static function grassHopper($board, $from, $to) {
        $posFrom = explode(',', $from);
        $posTo = explode(',', $to);

        $diffx = $posTo[0] - $posFrom[0];
        $diffy = $posTo[1] - $posFrom[1];

        // Check if the move is a straight vertical or diagonal line.
        if (abs($diffx) != abs($diffy) && $diffx != 0 && $diffy != 0) {
            return false;
        }

        if ($diffx == 0) {
            $diffx = 0;
        } else {
            $diffx = $diffx / abs($diffx);
        }

        if ($diffy == 0) {
            $diffy = 0;
        } else {
            $diffy = $diffy / abs($diffy);
        }

        $nextPosX = $posFrom[0];
        $nextPosY = $posFrom[1];

        $valid = false;
        while ($nextPosX != $posTo[0] || $nextPosY != $posTo[1]) {
            $pos = ($posFrom[0] + $diffx).','.($posFrom[1] + $diffy);

            if (isset($board[$pos])) {
                $valid = true;
            }

            $nextPosX += $diffx;
            $nextPosY += $diffy;
        }

        return $valid;
    }

    public static function ant($board, $from, $to) {
        if ($from == $to) {
            return false;
        }

        // Check count of neighbours. More than 5 is invalid.
        $neighbours = 0;

        foreach (self::$OFFSETS as $pq) {
            $p = explode(',', $to)[0] + $pq[0];
            $q = explode(',', $to)[1] + $pq[1];
            if (isset($board["$p,$q"])) {
                $neighbours++;
            }
        }

        if ($neighbours > 0 && $neighbours < 5) {
            return true;
        }

        return false;
    }

    public static function spider($board, $from, $to) {
        if ($from == $to) {
            return false;
        }

        $explored = [];
        $queue = [[$from, 0]];

        while (count($queue) > 0) {
            $current = array_shift($queue);
            $pos = $current[0];
            $distance = $current[1];

            if ($pos == $to && $distance == 3) {
                return true;
            }

            $explored[] = $pos;

            foreach (self::$OFFSETS as $pq) {
                $p = explode(',', $pos)[0] + $pq[0];
                $q = explode(',', $pos)[1] + $pq[1];
                $newPos = "$p,$q";

                if (!in_array($newPos, $explored) && !isset($board[$newPos]) && ($distance + 1) <= 3 && self::hasNeighBour($newPos, $board)) {
                    $queue[] = [$newPos, $distance + 1];
                }
            }
        }

        return false;
    }

    public static function lost($player, $board) {
        foreach ($board as $pos => $tile) {
            $mainTile = $tile[count($tile) - 1];

            if ($mainTile[1] != "Q" || $mainTile[0] != $player) {
                continue;
            }

            $count = 0;
            foreach (self::$OFFSETS as $pq) {
                $p = explode(',', $pos)[0] + $pq[0];
                $q = explode(',', $pos)[1] + $pq[1];
                if (isset($board["$p,$q"])) {
                    $count++;
                }
            }

            if ($count == 6) {
                return true;
            }
        }

        return false;
    }

    public static function draw($board) {
        return self::lost(0, $board) && self::lost(1, $board);
    }
}
