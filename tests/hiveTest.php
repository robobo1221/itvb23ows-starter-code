<?php
use PHPUnit\Framework\TestCase;

include_once "src/util.php";
include_once "src/dataService.php";
include_once "src/rules.php";
include_once "src/hive.php";

class HiveTest extends TestCase {
    private $hive;

    // Setup hive game
    protected function setUp(): void {
        parent::setUp();

        $this->hive = new Hive();
    }

    public function testInvalidNonQueenMoveAfterThreeMoves() {
        $this->hive->board = [
            '0,0' => [[0, "B"]],
            '0,1' => [[0, "G"]],
            '0,2' => [[0, "A"]],
        ];
        $this->hive->player = 0;
        $this->hive->hand = [
            0 => ["Q" => 1, "B" => 1, "S" => 1, "A" => 2, "G" => 2],
            1 => ["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0]
        ];

        // act
        $valid = $this->hive->checkValidPlay("A", "0,3");

        // assert
        $this->assertFalse($valid);
    }

    public function testValidQueenMoveAfterThreeMoves() {
        // arrange
        $this->hive->board = [
            '0,0' => [[0, "B"]],
            '0,1' => [[0, "G"]],
            '0,2' => [[0, "A"]],
        ];
        $this->hive->player = 0;
        $this->hive->hand = [
            0 => ["Q" => 1, "B" => 1, "S" => 1, "A" => 2, "G" => 2],
            1 => ["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0]
        ];

        // act
        $valid = $this->hive->checkValidPlay("Q", "0,3");

        // assert
        $this->assertTrue($valid);
    }

    public function testGrasshopperValidMove() {
        // arange and act
        $this->hive->play("G", "0,0");
        $this->hive->play("A", "0,1");
        $this->hive->play("Q", "0,-1");
        $this->hive->play("Q", "0,2");

        $valid = $this->hive->checkValidMove("0,0", "0,3");

        // assert
        $this->assertTrue($valid);
    }

    public function testGrasshopperInvalidLongMove() {
        // arange and act
        $this->hive->restart();
        $this->hive->play("G", "0,0");
        $this->hive->play("A", "0,1");
        $this->hive->play("Q", "0,-1");
        $this->hive->play("Q", "0,2");

        $valid = $this->hive->checkValidMove("0,0", "0,4");

        // assert
        $this->assertFalse($valid);
    }

    public function testGrasshopperInvalidShortMove() {
        // arange and act
        $this->hive->restart();
        $this->hive->play("G", "0,0");
        $this->hive->play("A", "0,1");
        $this->hive->play("Q", "0,-1");
        $this->hive->play("Q", "0,2");

        $valid = $this->hive->checkValidMove("0,0", "0,1");

        // assert
        $this->assertFalse($valid);
    }

    public function testAntValidMove() {
        // arange and act
        $this->hive->restart();
        $this->hive->play("Q", "0,0");
        $this->hive->play("A", "0,1");
        $this->hive->play("B", "-1,0");
        $this->hive->play("Q", "0,2");
        $this->hive->play("B", "-2,1");
        $this->hive->play("B", "-1,2");
        $this->hive->play("S", "0,-1");

        $valid = $this->hive->checkValidMove("0,1", "0,3"); // Ant is black

        // assert
        $this->assertTrue($valid);
    }

    public function testAntInvalidMove() {
        // arange and act
        $this->hive->restart();
        $this->hive->play("Q", "0,0");
        $this->hive->play("A", "0,1");
        $this->hive->play("B", "-1,0");
        $this->hive->play("Q", "0,2");
        $this->hive->play("B", "-2,1");
        $this->hive->play("B", "-1,2");
        $this->hive->play("S", "0,-1");

        $valid = $this->hive->checkValidMove("0,1", "-1,1");    // Ant is black

        // assert
        $this->assertFalse($valid);
    }
}