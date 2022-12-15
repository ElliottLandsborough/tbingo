<?php

namespace ElliottLandsborough\TerminalBingo\Commands;

use ElliottLandsborough\TerminalBingo\Services\Bingo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'checkBoards', description: 'Check some bingo input')]
class CheckBingoBoards extends Command
{
    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $bingo = new Bingo();

        $bingo->start(STDIN);

        return Command::SUCCESS;
    }
}
