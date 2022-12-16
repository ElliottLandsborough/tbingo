<?php

namespace ElliottLandsborough\TerminalBingo\Services;

use Exception;

class Bingo
{
    /**
     * Undocumented variable.
     *
     * @var array<int, array<int, array<int>>>
     */
    protected $winners = [];

    /**
     * Undocumented variable.
     *
     * @var array<int, array<int, array<int>>>
     */
    protected $losers = [];

    /**
     * Undocumented variable.
     *
     * @var array<int>
     */
    protected $balls = [];

    /**
     * Undocumented variable.
     *
     * @var array<int, string>
     */
    protected $errors = [];

    /**
     * Undocumented variable.
     *
     * @var array<int, array<int>>
     */
    protected $board = [];

    /**
     * Undocumented function.
     *
     * @return array<int, array<int, array<int>>>
     */
    public function getWinners(): array
    {
        return $this->winners;
    }

    /**
     * Undocumented function.
     *
     * @return array<int, array<int, array<int>>>
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
     * Parse stdin line by line
     *
     * @param resource $resource File resource
     *
     * @return \Generator
     */
    protected function stdinStream($resource): \Generator
    {
        try {
            while (true) {
                $line = stream_get_line($resource, 1024, PHP_EOL);

                if ($line === false) {
                    break;
                }

                $length = strlen($line);

                while ($length > 0
                       && ($line[$length - 1] === "\r" || $line[$length - 1] === "\n")
                ) {
                    $length--;
                }

                $line = substr($line, 0, $length);

                yield trim($line);
            }

            if (!feof($resource)) {
                $message = error_get_last()['message'];

                throw new Exception($message);
            }
        } finally {
            fclose($resource);
        }
    }

    /**
     * Process a resource line by line
     *
     * @param resource $resource The input, multiline text
     *
     * @return void
     */
    public function processResource($resource): void
    {
        foreach ($this->stdinStream($resource) as $line) {
            // Skip newlines without any other content.
            if (strlen($line) === 0 && str_contains($line, "\n") === true) {
                continue;
            }

            // Was a ballset defined?
            if (str_contains($line, ',') === true) {
                $this->balls = $this->processBallLine($line);
                continue;
            }

            // Line is longer than 0 chars, line does not contain comma.
            if (strlen($line) > 0) {
                $boardRow = $this->processBoardRowLine($line);
                $this->board[] = $boardRow;

                // Did we reach the board row limit of 5?
                if (count($this->board) === 5) {
                    $this->extractResults();
                }

                // Did we get some numbers back?
                if (count($boardRow)) {
                    // Skip to next line.
                    continue;
                }
            }

            // Last condition, we don't know what this is.
            if (strlen($line) > 0) {
                $this->errors[] = "Cannot process input line: `$line`.";
            }
        }
    }

    /**
     * Works out who has won and who has lost.
     *
     * @return void
     */
    public function extractResults(): void
    {
        // Process board.
        $winPosition = $this->checkForWinner($this->board);

        if ($winPosition > 0) {
            $this->winners[$winPosition] = $this->board;
            ksort($this->winners);
        }

        if ($winPosition === 0) {
            $this->losers[$winPosition] = $this->board;
        }

        // Reset board and continue.
        $this->board = [];
    }

    /**
     * Process ball line
     *
     * @param string $line Line to be processed
     *
     * @return array<int>
     */
    public function processBallLine(string $line): array
    {
        $balls = [];

        $exploded = explode(',', $line);

        foreach ($exploded as $number) {
            $balls[] = (int) trim($number);
        }

        return $balls;
    }

    /**
     * Process row of board
     *
     * @param string $line Line to be processed
     *
     * @return array<int>
     */
    public function processBoardRowLine(string $line): array
    {
        // Split line by multiple spaces, run through intval.
        $parts = array_map('intval', preg_split('/\s+/', $line));

        // If they are all digits and we have five of them.
        if (ctype_digit(implode('', $parts)) === true
            && count($parts) === 5
        ) {
            return $parts;
        }

        return [];
    }

    /**
     * Generate an array of winning rows
     *
     * @param array<int, array<int>> $board A bingo board
     *
     * @return array<int, array<int>>
     */
    protected function generateWinningRows(array $board): array
    {
        $horizontal = $board;
        $vertical = $this->generateVerticalRows($board);
        $diagonal = $this->generateDiagonalRows($board);

        return array_merge($horizontal, $vertical, $diagonal);
    }

    /**
     * Generate vertical winning rows
     *
     * @param array<int, array<int>> $board A bingo board
     *
     * @return array<int, array<int>>
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
     * Generate diagonal winning rows
     *
     * @param array<int, array<int>> $board A bingo board
     *
     * @return array<int, array<int>>
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
     * Check for a winning row
     *
     * Returns the point in the game where this board won.
     * Returning zero means this board never won.
     *
     * @param array<int, array<int>> $board A bingo board
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
                    if (count($winningRows[$rKey]) === 0) {
                        // Return the position in the game it wins at.
                        return $gamePosition;
                    }
                }
            }
        }

        return 0;
    }
}
