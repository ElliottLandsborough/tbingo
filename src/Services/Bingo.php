<?php

namespace ElliottLandsborough\TerminalBingo\Services;

use Exception;

class Bingo
{
    protected $winners = [];
    protected $losers = [];

    protected function stdinStream()
    {
        while ($line = fgets(STDIN)) {
            yield $line;
        }
    }

    public function start($resource): void
    {
        $balls = [];
        $board = [];
        $lineNumber = 0;

        foreach ($this->stdinStream() as $line) {
            $lineNumber++;
            $line = trim($line);

            // Skip newlines without any other content
            if (strlen($line) === 0 && str_contains($line, "\n")) {
                continue;
            }

            // Was a ballset defined?
            if (str_contains($line, ",")) {
                $balls = explode(",", $line);
                continue;
            }

            // Line is longer than 0 chars, line does not contain comma
            if (strlen($line) > 0 && !str_contains($line, ",")) {
                // Split line by multiple spaces
                $parts = preg_split('/\s+/', $line);

                // If they are all digits and we have five of them
                if (ctype_digit(implode('', $parts)) && count($parts) === 5) {
                    $board[] = $parts;

                    // Did we reach the board row limit of 5?
                    if (count($board) === 5) {
                        // Process board
                        $winPosition = $this->checkForWinner($balls, $board);

                        if ($winPosition > 0) {
                            $this->winners[$winPosition] = $board;
                        }

                        if ($winPosition === 0) {
                            $this->losers[$winPosition] = $board;
                        }

                        // Reset board and continue
                        $board = [];
                    }

                    continue;
                }
            }

            // Last condition, we don't know what this is
            if (strlen($line)) {
                fwrite(STDOUT, "Cannot process L$lineNumber of input: '$line'");
            }
        }

        ksort($this->winners);
        print_r($this->winners);
        print_r($this->losers);
    }

    protected function generateWinningRows(array $board): array
    {
        $horizontal = $board;
        $vertical = $this->generateVerticalRows($board);
        $diagonal = $this->generateDiagonalRows($board);

        return $horizontal + $vertical + $diagonal;
    }

    protected function generateVerticalRows(array $board): array
    {
        $verticalRows = [];

        foreach ($board as $row) {
            foreach ($row as $key => $number) {
                $verticalRows[$key][] = $number;
            }
        }

        return $verticalRows;
    }

    protected function generateDiagonalRows(array $board): array
    {
        $diagonalRows = [];

        for ($i = 0; $i <= 4; $i++) {
            // Diagonal row 1
            $diagonalRows[0][] =  $board[$i][$i];

            // Diagonal row 2
            $diagonalRows[1][] =  $board[$i][4 - $i];
        }

        return $diagonalRows;
    }

    protected function checkForWinner(array $balls, array $board): int
    {
        $winningRows = $this->generateWinningRows($board);

        $gamePosition = 0;

        // Each game ball, in order...
        foreach ($balls as $bNumber) {
            // Increment game position
            $gamePosition++;

            // Each row that counts as a winner...
            foreach ($winningRows as $rKey => $row) {
                // Each number from this row...
                foreach ($row as $nKey => $rNumber) {
                    // If game ball is the same as row number
                    if ($bNumber === $rNumber) {
                        // Remove it from the winning row
                        unset($winningRows[$rKey][$nKey]);
                    }
                    // If the winning row is empty
                    if (!count($winningRows[$rKey])) {
                        return $gamePosition;
                    }
                }
            }
        }

        return 0;
    }
}
