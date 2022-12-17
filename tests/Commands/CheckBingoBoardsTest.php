<?php

namespace ElliottLandsborough\PhpTerminalApp\Tests\Services;

use ElliottLandsborough\TerminalBingo\Commands\CheckBingoBoards;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CheckBingoBoardsTest extends TestCase
{
    /**
     * Instance of `CheckBingoBoards`.
     *
     * @var CheckBingoBoards
     */
    protected CheckBingoBoards $command;

    /**
     * Runs every time a test starts.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->command = new CheckBingoBoards();
    }

    /**
     * Runs every time a test stops.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->command);
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
        $application = new Application();
        $this->command->setPath(getcwd().'/tests/fixtures/badBoard1.txt');
        $application->add($this->command);
        $command = $application->find('checkBoards');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $haystack = trim($commandTester->getDisplay());

        $needle = "Cannot process input line: `0  0 ZZ  0  0`.\n";

        $this->assertStringContainsString($needle, $haystack);
    }

    /**
     * Executes full command.
     *
     * @covers ElliottLandsborough\TerminalBingo\Commands\CheckBingoBoards
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testExecution(): void
    {
        $application = new Application();
        $this->command->setPath(getcwd().'/tests/fixtures/input.txt');
        $application->add($this->command);
        $command = $application->find('checkBoards');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = trim($commandTester->getDisplay());

        $expected = file_get_contents('./tests/fixtures/output.txt');

        $this->assertStringContainsString($output, $expected);
    }

    /**
     * Try bad path.
     *
     * @covers ElliottLandsborough\TerminalBingo\Commands\CheckBingoBoards
     * @covers ElliottLandsborough\TerminalBingo\Services\Bingo
     *
     * @return void
     */
    public function testBadPath(): void
    {
        $this->expectExceptionMessage(
            'Error: file does not exist `./BADPATH/IS/MISSING`.'
        );

        $application = new Application();
        $this->command->setPath('./BADPATH/IS/MISSING');
        $application->add($this->command);
        $command = $application->find('checkBoards');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
