<?php
use PHPUnit\Framework\TestCase;
use Util\BoardUtil;

include_once "src/util.php";

class UtilTest extends TestCase {
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

    public function testGrasshopperDiagonalDownValidMove() {
        // arrange
        $board = ["0,0" => [[0, "G"]], "0,1" => [[1, "A"]], "0,2" => [[0, "Q"]], "0,3" => [[1, "Q"]]];

        // act
        $valid = BoardUtil::grassHopper($board, "0,0", "0,3");

        // assert
        $this->assertTrue($valid);
    }

    public function testGrasshopperDiagonalUpValidMove() {
        // arrange
        $board = ["0,0" => [[0, "G"]], "0,1" => [[1, "A"]], "0,2" => [[0, "Q"]], "0,3" => [[1, "Q"]]];

        // act
        $valid = BoardUtil::grassHopper($board, "0,3", "0,0");

        // assert
        $this->assertTrue($valid);
    }

    public function testGrasshopperHorizontalValidMove() {
        // arrange
        $board = ["-1,0" => [[0, "G"]], "0,1" => [[1, "Q"]], "0,0" => [[0, "Q"]], "1,0" => [[1, "B"]]];

        // act
        $valid = BoardUtil::grassHopper($board, "-1,0", "2,0");

        // assert
        $this->assertTrue($valid);
    }

    public function testGrasshopperNoConnectingInvalidMove() {
        // arrange
        $board = ["0,0" => [[0, "G"]], "0,1" => [[1, "A"]], "0,2" => [[0, "Q"]], "0,3" => [[1, "Q"]]];

        // act
        $valid = BoardUtil::grassHopper($board, "0,0", "0,-1");

        // assert
        $this->assertFalse($valid);
    }

    public function testAntValidMoveBorder() {
        // arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "A"]],
            "-1,0" => [[0, "B"]],
            "0,2" => [[1, "Q"]],
            "-2,1" => [[0, "B"]],
            "-1,2" => [[1, "B"]],
            "0,-1" => [[0, "S"]]
        ];

        // act
        $valid = BoardUtil::ant($board, "0,1", "0,3");

        // assert
        $this->assertTrue($valid);
    }

    public function testAntInvalidMoveSurrounded() {
        // arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "A"]],
            "-1,0" => [[0, "B"]],
            "0,2" => [[1, "Q"]],
            "-2,1" => [[0, "B"]],
            "-1,2" => [[1, "B"]],
            "0,-1" => [[0, "S"]]
        ];

        // act
        $valid = BoardUtil::ant($board, "0,1", "-1,1");

        // assert
        $this->assertFalse($valid);
    }

    public function testSpiderValidThreeMoves() {
        // arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "S"]],
            "-1,0" => [[0, "S"]],
            "-1,2" => [[1, "Q"]],
            "1,-1" => [[0, "B"]],
            "-2,3" => [[1, "B"]]
        ];

        // act
        $valid = BoardUtil::spider($board, "-1,0", "-3,3");

        // assert
        $this->assertTrue($valid);
    }

    public function testSpiderInvalidLessThanThreeMoves() {
        // arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "S"]],
            "-1,0" => [[0, "S"]],
            "-1,2" => [[1, "Q"]],
            "1,-1" => [[0, "B"]],
            "-2,3" => [[1, "B"]]
        ];

        // act
        $valid = BoardUtil::spider($board, "-1,0", "0,-1");

        // assert
        $this->assertFalse($valid);
    }

    public function testSpiderInvalidMoreThanThreeMoves() {
        // arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "S"]],
            "-1,0" => [[0, "S"]],
            "-1,2" => [[1, "Q"]],
            "1,-1" => [[0, "B"]],
            "-2,3" => [[1, "B"]]
        ];

        // act
        $valid = BoardUtil::spider($board, "-1,0", "1,0");

        // assert
        $this->assertFalse($valid);
    }

    public function testSpiderInvalidOverlappingMove() {
        // arrange
        $board = [
            "0,0" => [[0, "Q"]],
            "0,1" => [[1, "S"]],
            "-1,0" => [[0, "S"]],
            "-1,2" => [[1, "Q"]],
            "1,-1" => [[0, "B"]],
            "-2,3" => [[1, "B"]]
        ];

        // act
        $valid = BoardUtil::spider($board, "-1,0", "-1,0");

        // assert
        $this->assertFalse($valid);
    }

    public function testPlayerLostTrue() {
        // arrange
        $board = [
            '0,0' => [[0, "Q"]],
            '0,1' => [[1, "Q"]],
            '1,0' => [[1, "B"]],
            '1,-1' => [[1, "B"]],
            '-1,0' => [[1, "B"]],
            '0,-1' => [[1, "A"]],
            '-1,1' => [[1, "A"]]
        ];

        // act
        $lost = BoardUtil::lost(0, $board);

        // assert
        $this->assertTrue($lost);
    }

    public function testPlayerLostFalse() {
        // arrange
        $board = [
            '0,-2' => [[0, "Q"]],
            '0,1' => [[1, "Q"]],
            '1,0' => [[1, "B"]],
            '1,-1' => [[1, "B"]],
            '-1,0' => [[1, "B"]],
            '0,-1' => [[1, "A"]],
            '-1,1' => [[1, "A"]]
        ];

        // act
        $lost = BoardUtil::lost(0, $board);

        // assert
        $this->assertFalse($lost);
    }

    public function testDrawTrue() {
        // arrange
        $board = [
            '0,0' => [[0, "Q"]],
            '0,1' => [[1, "A"]],
            '1,0' => [[1, "B"]],
            '-1,0' => [[1, "B"]],
            '0,-1' => [[1, "A"]],
            '-1,1' => [[1, "A"]],
            '0,-1' => [[1, "Q"]],
            '0,-2' => [[0, "B"]],
            '1,-2' => [[0, "B"]],
            '-1,-1' => [[0, "B"]],
            '1,-1' => [[0, "G"]],
        ];

        // act
        $lost = BoardUtil::draw($board);

        // assert
        $this->assertTrue($lost);
    }

    public function testDrawFalse() {
        // arrange
        $board = [
            '0,0' => [[0, "Q"]],
            '0,1' => [[1, "A"]],
            '1,0' => [[1, "B"]],
            '-1,0' => [[1, "B"]],
            '0,-1' => [[1, "A"]],
            '-1,1' => [[1, "A"]],
            '0,-3' => [[1, "Q"]],
            '0,-2' => [[0, "B"]],
            '1,-2' => [[0, "B"]],
            '-1,-1' => [[0, "B"]],
            '1,-1' => [[0, "G"]],
        ];

        // act
        $lost = BoardUtil::draw($board);

        // assert
        $this->assertFalse($lost);
    }
}