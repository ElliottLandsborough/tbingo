<?php

namespace ElliottLandsborough\TerminalBingo\Services;

class Bingo
{
    /**
     * Undocumented variable.
     *
     * @var array<int, array<int, array<int>>
     */
    protected $winners = [];

    /**
     * Undocumented variable.
     *
     * @var array<int, array<int, array<int>>
     */
    protected $losers = [];

    /**
     * Undocumented variable.
     *
     * @var array<int>
     */
    protected $balls = [];

    /**
     * Undocumented function.
     *
     * @return array<int>
     */
    public function getWinners(): array
    {
        return $this->winners;
    }

    /**
     * Undocumented function.
     *
     * @return array<int>
     */
    public function getLosers(): array
    {
        return $this->losers;
    }

    /**
     * Undocumented function.
     *
     * @return array<int>
     */
    public function getBalls()
    {
        return $this->balls;
    }

    /**
     * Undocumented function.
     *
     * @return ??
     */
    protected function stdinStream()
    {
        while ($line = fgets(STDIN)) {
            yield $line;
        }
    }

    /**
     * Undocumented function.
     *
     * @param resource $resource
     *
     * @return void
     */
    public function start(): void
    {
        $board = [];
        $lineNumber = 0;

        foreach ($this->stdinStream() as $line) {
            $lineNumber++;
            $line = trim($line);

            // Skip newlines without any other content.
            if (strlen($line) === 0 && str_contains($line, "\n")) {
                continue;
            }

            // Was a ballset defined?
            if (str_contains($line, ',')) {
                $exploded = explode(',', $line);

                $balls = [];

                foreach ($exploded as $number) {
                    $balls[] = (int) trim($number);
                }

                $this->balls = $balls;

                continue;
            }

            // Line is longer than 0 chars, line does not contain comma.
            if (strlen($line) > 0) {
                // Split line by multiple spaces, run through intval.
                $parts = array_map('intval', preg_split('/\s+/', $line));

                // If they are all digits and we have five of them.
                if (ctype_digit(implode('', $parts)) === true
                    && count($parts) === 5
                ) {
                    $board[] = $parts;

                    // Did we reach the board row limit of 5?
                    if (count($board) === 5) {
                        // Process board.
                        $winPosition = $this->checkForWinner($board);

                        if ($winPosition > 0) {
                            $this->winners[$winPosition] = $board;
                        }

                        if ($winPosition === 0) {
                            $this->losers[$winPosition] = $board;
                        }

                        // Reset board and continue.
                        $board = [];
                    }

                    continue;
                }
            }

            // Last condition, we don't know what this is.
            if (strlen($line)) {
                fwrite(STDOUT, "Cannot process L$lineNumber of input: '$line'");
            }
        }

        // Sort winners by key.
        ksort($this->winners);
    }

    /**
     * Undocumented function.
     *
     * @param array $board A bingo board
     *
     * @return array
     */
    protected function generateWinningRows(array $board): array
    {
        $horizontal = $board;
        $vertical = $this->generateVerticalRows($board);
        $diagonal = $this->generateDiagonalRows($board);

        return $horizontal + $vertical + $diagonal;
    }

    /**
     * Undocumented function.
     *
     * @param array $board A bingo board
     *
     * @return array
     */
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

    /**
     * Undocumented function.
     *
     * @param array $board A bingo board
     *
     * @return array
     */
    protected function generateDiagonalRows(array $board): array
    {
        $diagonalRows = [];

        for ($i = 0; $i <= 4; $i++) {
            // Diagonal row 1.
            $diagonalRows[0][] = $board[$i][$i];

            // Diagonal row 2.
            $diagonalRows[1][] = $board[$i][(4 - $i)];
        }

        return $diagonalRows;
    }

    /**
     * Undocumented function.
     *
     * @param array $board A bingo board
     *
     * @return int
     */
    protected function checkForWinner(array $board): int
    {
        $winningRows = $this->generateWinningRows($board);

        $gamePosition = 0;

        // Each game ball, in order.
        foreach ($this->balls as $bNumber) {
            // Increment game position.
            $gamePosition++;

            // Each row that counts as a winner.
            foreach ($winningRows as $rKey => $row) {
                // Each number from this row.
                foreach ($row as $nKey => $rNumber) {
                    // If game ball is the same as row number.
                    if ($bNumber === $rNumber) {
                        // Remove it from the winning row.
                        unset($winningRows[$rKey][$nKey]);
                    }
                    // If the winning row is empty.
                    if (count($winningRows[$rKey] === false)) {
                        // Return the position in the game it wins at.
                        return $gamePosition;
                    }
                }
            }
        }

        return 0;
    }
}
