<?php

namespace ElliottLandsborough\PhpTerminalApp\Tests\Services;

use ElliottLandsborough\TerminalBingo\Commands\CheckBingoBoards;
use ElliottLandsborough\TerminalBingo\Services\Bingo;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Console\Formatter\NullOutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseCronTest extends TestCase
{
    protected $bingo;
    protected $command;
    protected $output;

    protected function setUp(): void
    {
        $this->bingo = new Bingo();
        $this->command = new CheckBingoBoards();
    }

    protected function tearDown(): void
    {
        unset($this->bingo);
        unset($this->command);
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass(CheckBingoBoards::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Tests provided input.
     *
     * @covers ElliottLandsborough\TerminalBingo\Commands\CheckBingoBoards
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testHorizontal1(): void
    {
        $this->bingo->processResource(
            getcwd().'/tests/fixtures/input.txt'
        );

        $generateOutput = self::getMethod('generateOutput');
        $consoleOutput = new CheckBingoBoards();

        ob_start();

        $generateOutput->invokeArgs(
            $consoleOutput,
            [new ConsoleOutput(), $this->bingo]
        );

        $output = ob_get_contents();

        ob_end_clean();

        $expected = <<<'EOD'
A game was played with 27 balls.

There were 3 wins and 0 losses.

The first win after 8 numbers:

+--+--+
| B | 14 21 <comment>17</comment> 24 <info> 4</info> |
| I | 10 16 15 <info> 9</info> 19 |
| N | 18  8 <info>23</info> 26 20 |
| G | 22 <info>11</info> 13  6 <comment> 5</comment> |
| O | <info> 2</info>  0 12  3 <comment> 7</comment> |
+--+--+

Finished.

EOD;

        $this->assertEquals($expected, $output);
    }

    /**
     * Tests a bad input.
     *
     * @covers ElliottLandsborough\TerminalBingo\Commands\CheckBingoBoards
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testBadBoard(): void
    {
        $this->bingo->processResource(
            getcwd().'/tests/fixtures/badBoard1.txt'
        );

        $generateOutput = self::getMethod('generateOutput');
        $consoleOutput = new CheckBingoBoards();

        ob_start();

        $generateOutput->invokeArgs(
            $consoleOutput,
            [new ConsoleOutput(), $this->bingo]
        );

        $haystack = (string) ob_get_contents();

        ob_end_clean();

        $needle = "Cannot process input line: `0  0 ZZ  0  0`.\n";

        $this->assertStringContainsString($needle, $haystack);
    }
}

// Hacky interface implementation for testing.
class ConsoleOutput implements OutputInterface
{
    public function writeln(iterable|string $messages, int $type = self::OUTPUT_NORMAL): void
    {
        echo $messages.PHP_EOL;
    }

    public function getFormatter(): OutputFormatterInterface
    {
        return new NullOutputFormatter();
    }

    public function write(string|iterable $messages, bool $newline = false, int $options = 0)
    {
    }

    public function setVerbosity(int $level)
    {
    }

    public function getVerbosity(): int
    {
    }

    public function isQuiet(): bool
    {
    }

    public function isVerbose(): bool
    {
    }

    public function isVeryVerbose(): bool
    {
    }

    public function isDebug(): bool
    {
    }

    public function setDecorated(bool $decorated)
    {
    }

    public function isDecorated(): bool
    {
    }

    public function setFormatter(OutputFormatterInterface $formatter)
    {
    }
}
