<?php
use PHPUnit\Framework\TestCase;
use Util\BoardUtil;

include_once "src/util.php";

class UtilTest extends TestCase {
    //public function

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
}