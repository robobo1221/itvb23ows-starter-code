<?php

use PHPUnit\Framework\TestCase;

include_once "src/dataService.php";
include_once "src/aiService.php";
include_once "src/hive.php";

class AiServiceTest extends TestCase {
    private $aiService;
    private $dataService;
    private $hive;

    protected function setUp(): void {
        parent::setUp();

        $mockMysqli = Mockery::mock(mysqli::class);
        $mockMysqli->shouldReceive('query')->andReturn(true);
        $mockMysqli->shouldReceive('prepare')->andReturn(Mockery::mock(mysqli_stmt::class));
        $mockMysqli->shouldReceive('bind_param')->andReturn(true);
        $mockMysqli->shouldReceive('execute')->andReturn(true);
        $mockMysqli->shouldReceive('fetch')->andReturn(true);
        $mockMysqli->shouldReceive('close')->andReturn(true);
        $mockMysqli->makePartial();

        $this->dataService = Mockery::mock(DataService::class, [$mockMysqli]);
        $this->dataService->shouldReceive("getPreviousGameMoves")->with(1)->andReturn((object)[ "num_rows" => 3 ]);
        $this->dataService->shouldReceive("initGame")->andReturn(1);
        $this->dataService->shouldReceive("registerMove")->andReturn(1);

        $this->aiService = Mockery::mock(AiService::class);

        $this->hive = new Hive($this->dataService, $this->aiService);
    }

    public function testInitialPlayEqualsZero() {
        // Arrange
        $this->hive->restart();
        $this->aiService->shouldReceive('getMove')->once()->andReturn([
            "play", "B", "0,0"
        ]);

        // Act
        $this->hive->playAiMove();

        // Assert
        $this->assertSame([ "0,0" => [[0, "B"]] ], $this->hive->board);
    }

    public function testSecondPlayEqualsOne() {
        // Arrange
        $this->hive->restart();
        $this->aiService->shouldReceive('getMove')->times(2)->andReturn(
            [ "play", "B", "0,0" ],
            [ "play", "B", "0,1" ]
        );

        // Act
        $this->hive->playAiMove();
        $this->hive->playAiMove();

        // Assert
        $this->assertSame([ "0,0" => [[0, "B"]], "0,1" => [[1, "B"]] ], $this->hive->board);
    }

    public function testMovePlayEqualsOne() {
        // Arrange
        $this->hive->restart();
        $this->aiService->shouldReceive('getMove')->times(2)->andReturn(
            [ "play", "B", "0,0" ],
            [ "move", "0,0", "0,1" ]
        );

        // Act
        $this->hive->playAiMove();
        $this->hive->playAiMove();

        // Assert
        $this->assertArrayHasKey("0,1", $this->hive->board);
    }

    public function testMovePlayEqualsTwo() {
        // Arrange
        $this->hive->restart();
        $this->aiService->shouldReceive('getMove')->times(3)->andReturn(
            [ "play", "B", "0,0" ],
            [ "move", "0,0", "0,1" ],
            [ "move", "0,1", "0,2" ]
        );

        // Act
        $this->hive->playAiMove();
        $this->hive->playAiMove();
        $this->hive->playAiMove();

        // Assert
        $this->assertArrayHasKey("0,2", $this->hive->board);
    }

    public function testPassPlayStaysSame() {
        // Arrange
        $this->hive->restart();
        $this->aiService->shouldReceive('getMove')->times(2)->andReturn(
            [ "play", "B", "0,0" ],
            [ "pass", null, null ]
        );

        // Act
        $this->hive->playAiMove();
        $this->hive->playAiMove();

        // Assert
        $this->assertSame([ "0,0" => [[0, "B"]] ], $this->hive->board);
    }
}