<?php
use PHPUnit\Framework\TestCase;
use Util\BoardUtil;

require_once("src/util.php");

class BoardUtilTest extends TestCase {
    public function testAvailableTiles(): void {
        $hand = ["Q" => 1, "B" => 0, "S" => 2, "A" => 2, "G" => 0]; // arrange
        $availableTiles = BoardUtil::getAvailableTiles($hand);      // act

        $this->assertSame(["Q", "S", "A"], $availableTiles);        // assert
    }

    public function testFirstAvailablePlays(): void {
        $hand = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];             // arrange
        $board = [];
        $player = 0;

        $availablePlays = BoardUtil::getAvailablePlays($hand, $board, $player); // act
        
        $this->assertSame(["0,0"], $availablePlays);                            // assert
    }

    public function testAvailablePlays(): void {
        $hand = ["Q" => 0, "B" => 2, "S" => 2, "A" => 3, "G" => 3];             // arrange
        // Player 0 played Q on position 0,0, then player 1 played S on position 0,1.
        $board = ["0,0" => [[0, "Q"]], "0,1" => [[1, "S"]]];
        $player = 0;

        $availablePlays = BoardUtil::getAvailablePlays($hand, $board, $player); // act
        
        $this->assertSame(["0,-1", "-1,0", "1,-1"], $availablePlays);           // assert
    }

    public function testAvailableFrom(): void {
        // Testing on player 1. Player 1 has only one position on the board.
        $board = ["0,0" => [[0, "Q"]], "0,1" => [[1, "Q"]]];            // arrange
        $player = 1;

        $availableFroms = BoardUtil::getAvailableFrom($board, $player); // act

        $this->assertSame(["0,1"], $availableFroms);                    // assert
    }
}