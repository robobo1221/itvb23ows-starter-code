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
}