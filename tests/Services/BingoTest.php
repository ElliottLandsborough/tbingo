<?php

namespace ElliottLandsborough\TerminalBingo\Tests\Services;

use ElliottLandsborough\TerminalBingo\Services\Bingo;
use PHPUnit\Framework\TestCase;

class BingoTest extends TestCase
{
    /**
     * Instance of Bingo
     *
     * @var Bingo
     */
    protected $bingo;

    /**
     * Runs at the beginning of a test
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->bingo = new Bingo();
    }

    /**
     * Runs at the end of a test
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->bingo);
    }

    /**
     * Tests nothing
     *
     * @return void
     */
    public function testNothing(): void
    {
    }
}
