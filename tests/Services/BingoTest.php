<?php

namespace ElliottLandsborough\TerminalBingo\Tests\Services;

use ElliottLandsborough\TerminalBingo\Services\Bingo;
use PHPUnit\Framework\TestCase;

class BingoTest extends TestCase
{
    /**
     * Instance of `Bingo`.
     *
     * @var Bingo
     */
    protected $bingo;

    /**
     * Runs at the beginning of a test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->bingo = new Bingo();
    }

    /**
     * Runs at the end of a test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->bingo);
    }

    /**
     * Tests a horizontal row.
     *
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testHorizontal1(): void
    {
        $this->bingo->processResource(
            getcwd().'/tests/fixtures/horizontal1.txt'
        );

        $winners = $this->bingo->getWinners();

        $this->assertArrayHasKey(10, $winners);
        $this->assertArrayHasKey(2, $winners[10]);

        $this->assertEquals(
            [
                5, 4, 3, 2, 1,
            ],
            $winners[10][2],
        );

        // Test getBalls.
        $balls = $this->bingo->getBalls();

        $this->assertEquals(
            [
                12, 13, 14, 1, 2, 56, 55, 3, 4, 5,
            ],
            $balls,
        );

        // Test getWinningRows.
        $winningRows = $this->bingo->getWinningRows();
        $this->assertArrayHasKey(10, $winningRows);

        $this->assertEquals(
            [
                5, 4, 3, 2, 1,
            ],
            $winningRows[10],
        );

        // Test getLosers.
        $losers = $this->bingo->getLosers();
        $this->assertArrayHasKey(0, $losers);

        $this->assertEquals(
            [
                9, 9, 9, 9, 9,
            ],
            $losers[0][3],
        );

        // Test getWinningRowFromNumbers.
        $this->assertSame([], $this->bingo->getWinningRowFromNumbers([]));
    }

    /**
     * Tests a horizontal row.
     *
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testHorizontal2(): void
    {
        $this->bingo->processResource(
            getcwd().'/tests/fixtures/horizontal2.txt'
        );

        $winners = $this->bingo->getWinners();

        $this->assertArrayHasKey(10, $winners);
        $this->assertArrayHasKey(0, $winners[10]);

        $this->assertEquals(
            [
                1, 2, 3, 4, 5,
            ],
            $winners[10][0]
        );
    }

    /**
     * Tests a vertical row.
     *
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testVertical1(): void
    {
        $this->bingo->processResource(
            getcwd().'/tests/fixtures/vertical1.txt'
        );

        $winners = $this->bingo->getWinners();

        $this->assertArrayHasKey(10, $winners);
        $this->assertArrayHasKey(0, $winners[10]);

        $this->assertEquals($winners[10][0][1], 1);
        $this->assertEquals($winners[10][1][1], 2);
        $this->assertEquals($winners[10][2][1], 3);
        $this->assertEquals($winners[10][3][1], 4);
        $this->assertEquals($winners[10][4][1], 5);
    }

    /**
     * Tests a vertical row.
     *
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testVertical2(): void
    {
        $this->bingo->processResource(
            getcwd().'/tests/fixtures/vertical2.txt'
        );

        $winners = $this->bingo->getWinners();

        $this->assertArrayHasKey(7, $winners);
        $this->assertArrayHasKey(0, $winners[7]);

        $this->assertEquals($winners[7][0][3], 14);
        $this->assertEquals($winners[7][1][3], 13);
        $this->assertEquals($winners[7][2][3], 12);
        $this->assertEquals($winners[7][3][3], 56);
        $this->assertEquals($winners[7][4][3], 55);
    }

    /**
     * Tests a diagonal row.
     *
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testDiagonal1(): void
    {
        $this->bingo->processResource(
            getcwd().'/tests/fixtures/diagonal1.txt'
        );

        $winners = $this->bingo->getWinners();

        $this->assertArrayHasKey(10, $winners);
        $this->assertArrayHasKey(0, $winners[10]);

        $this->assertEquals($winners[10][0][0], 1);
        $this->assertEquals($winners[10][1][1], 2);
        $this->assertEquals($winners[10][2][2], 3);
        $this->assertEquals($winners[10][3][3], 4);
        $this->assertEquals($winners[10][4][4], 5);
    }

    /**
     * Tests a diagonal row.
     *
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testDiagonal2(): void
    {
        $this->bingo->processResource(
            getcwd().'/tests/fixtures/diagonal2.txt'
        );

        $winners = $this->bingo->getWinners();

        $this->assertArrayHasKey(10, $winners);
        $this->assertArrayHasKey(0, $winners[10]);

        $this->assertEquals($winners[10][0][4], 1);
        $this->assertEquals($winners[10][1][3], 2);
        $this->assertEquals($winners[10][2][2], 3);
        $this->assertEquals($winners[10][3][1], 4);
        $this->assertEquals($winners[10][4][0], 5);
    }

    /**
     * Tests a bad board.
     *
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testBadBoard1(): void
    {
        $this->bingo->processResource(
            getcwd().'/tests/fixtures/badBoard1.txt'
        );

        $errors = $this->bingo->getErrors();

        $this->assertArrayHasKey(0, $errors);
        $this->assertEquals($errors[0], '0  0 ZZ  0  0');
    }

    /**
     * Tests a bad game.
     *
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testBadGame1(): void
    {
        $this->expectExceptionMessage(
            'Invalid game: `76` is out of range 0-75.'
        );

        $this->bingo->processResource(
            getcwd().'/tests/fixtures/badGame1.txt'
        );
    }

    /**
     * Tests a bad game.
     *
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testBadGame2(): void
    {
        $this->expectExceptionMessage(
            'Invalid game: `abc` is not a number.'
        );

        $this->bingo->processResource(
            getcwd().'/tests/fixtures/badGame2.txt'
        );
    }
}
