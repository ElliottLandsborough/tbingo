<?php

namespace ElliottLandsborough\TerminalBingo\Commands;

use ElliottLandsborough\TerminalBingo\Services\Bingo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

#[AsCommand(name: 'checkBoards', description: 'Check some bingo input')]
class CheckBingoBoards extends Command
{
    protected string $path = 'php://stdin';

    public function setPath(string $path)
    {
        if (file_exists($path) === false) {
            throw new Exception("Error: file does not exist `$path`.");
        }

        $this->path = $path;
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input  The terminal input
     * @param OutputInterface $output The terminal output
     *
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $bingo = new Bingo();
        $bingo->processResource($this->path);
        $this->generateOutput($output, $bingo);

        return Command::SUCCESS;
    }

    /**
     * Generated the terminal output.
     *
     * @param OutputInterface $output Output interface
     * @param Bingo           $bingo  Bingo instance
     *
     * @return void
     */
    protected function generateOutput(OutputInterface $output, Bingo $bingo): void
    {
        $errors = $bingo->getErrors();

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $output->writeln("Cannot process input line: `$error`.");
            }
        }

        $winners = $bingo->getWinners();
        $losers = $bingo->getLosers();
        $balls = $bingo->getBalls();

        $winCount = count($winners);
        $lossCount = count($losers);
        $ballCount = count($balls);

        $output->writeln("A game was played with $ballCount balls.\n");

        $output->writeln("There were $winCount wins and $lossCount losses.\n");

        $rows = [];

        foreach ($winners as $position => $board) {
            $output->writeln("The first win after $position numbers:\n");
            $decoration = 'BINGO';
            $progress = array_slice($balls, 0, $position);

            foreach ($board as $key => $numbers) {
                $rowStrings = [];

                foreach ($numbers as $number) {
                    $prefix = '';
                    $suffix = '';

                    if (in_array($number, $progress) === true) {
                        $prefix = '<comment>';
                        $suffix = '</comment>';

                        $winningRows = $bingo->getWinningRows();

                        $winningRow = $winningRows[$position];

                        if (in_array($number, $winningRow) === true) {
                            $prefix = '<info>';
                            $suffix = '</info>';
                        }
                    }

                    $padded = str_pad((string) $number, 2, ' ', STR_PAD_LEFT);

                    $rowStrings[] = $prefix.$padded.$suffix;
                }

                $rows[] = [
                    $decoration[$key],
                    implode(' ', $rowStrings),
                ];
            }

            break;
        }

        $table = new Table($output);
        $table->setRows($rows);
        $table->render();

        if (count($winners) > 0) {
            $output->writeln('');
        }

        $output->writeln('Finished.');
    }
}
