<?php

namespace ElliottLandsborough\TerminalBingo\Tests\Services;

use ElliottLandsborough\TerminalBingo\Services\Bingo;
use PHPUnit\Framework\TestCase;

class BingoTest extends TestCase
{
    protected $bingo;

    // runs per test
    public function setUp(): void
    {
        $this->bingo = new Bingo();
    }

    // runs after each test
    public function tearDown(): void
    {
        $this->bingo = null;
    }

    public function testNothing()
    {
    }
}
