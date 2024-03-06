<?php
use PHPUnit\Framework\TestCase;

include_once "src/util.php";
include_once "src/dataService.php";
include_once "src/rules.php";
include_once "src/hive.php";

class HiveTest extends TestCase {
    public function testInvalidNonQueenMoveAfterThreeMoves() {
         // arrange
        $hive = new Hive();
        $hive->board = [
            '0,0' => [[0, "B"]],
            '0,1' => [[0, "G"]],
            '0,2' => [[0, "A"]],
        ];
        $hive->player = 0;
        $hive->hand = [
            0 => ["Q" => 1, "B" => 1, "S" => 1, "A" => 2, "G" => 2],
            1 => ["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0]
        ];

        // act
        $valid = $hive->checkValidPlay("A", "0,3");

        // assert
        $this->assertFalse($valid);
    }

    public function testValidQueenMoveAfterThreeMoves() {
        // arrange
        $hive = new Hive();
        $hive->board = [
            '0,0' => [[0, "B"]],
            '0,1' => [[0, "G"]],
            '0,2' => [[0, "A"]],
        ];
        $hive->player = 0;
        $hive->hand = [
            0 => ["Q" => 1, "B" => 1, "S" => 1, "A" => 2, "G" => 2],
            1 => ["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0]
        ];

        // act
        $valid = $hive->checkValidPlay("Q", "0,3");

        // assert
        $this->assertTrue($valid);
    }
}