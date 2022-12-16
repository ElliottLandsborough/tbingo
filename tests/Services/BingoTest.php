<?php

namespace ElliottLandsborough\TerminalBingo\Tests\Services;

use ElliottLandsborough\TerminalBingo\Services\Bingo;
use PHPUnit\Framework\TestCase;

class BingoTest extends TestCase
{
    /**
     * Instance of Bingo.
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
     * Tests a horizontal row
     *
     * @return void
     */
    public function testHorizontal1(): void
    {
        $this->bingo->processResource(
            getcwd() . '/tests/fixtures/horizontal1.txt'
        );

        $winners = $this->bingo->getWinners();

        $this->assertArrayHasKey(10, $winners);
        $this->assertArrayHasKey(2, $winners[10]);

        $this->assertEquals(
            [5, 4, 3, 2, 1],
            $winners[10][2]
        );
    }
}
