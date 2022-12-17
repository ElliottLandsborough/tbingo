<?php

namespace ElliottLandsborough\TerminalBingo\Services;

use Exception;
use Generator;

class Bingo
{
    /**
     * The winning bingo boards.
     *
     * @var array<int, array<int, array<int>>>
     */
    protected $winners = [];

    /**
     * The losing bingo boards.
     *
     * @var array<int, array<int, array<int>>>
     */
    protected $losers = [];

    /**
     * The winning row.
     *
     * @var array<int, array<int>>
     */
    protected $winningRows = [];

    /**
     * The bingo game balls in order drawn.
     *
     * @var array<int>
     */
    protected $balls = [];

    /**
     * Any board errors.
     *
     * @var array<int, string>
     */
    protected $errors = [];

    /**
     * The current board being processed.
     *
     * @var array<int, array<int>>
     */
    protected $board = [];

    /**
     * Gets the winners.
     *
     * @return array<int, array<int, array<int>>>
     */
    public function getWinners(): array
    {
        return $this->winners;
    }

    /**
     * Gets the losers.
     *
     * @return array<int, array<int, array<int>>>
     */
    public function getLosers(): array
    {
        return $this->losers;
    }

    /**
     * Gets the winning rows.
     *
     * @return array<int, array<int>>
     */
    public function getWinningRows(): array
    {
        return $this->winningRows;
    }

    /**
     * Gets the drawn balls.
     *
     * @return array<int>
     */
    public function getBalls()
    {
        return $this->balls;
    }

    /**
     * Gets the errors.
     *
     * @return array<int, string>
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Stream file line by line.
     *
     * @param resource $resource File resource
     *
     * @return Generator
     */
    protected function fileStream($resource): Generator
    {
        try {
            while (true) {
                if (feof($resource) === true) {
                    break;
                }

                $line = stream_get_line($resource, 1024, PHP_EOL);
                $length = strlen($line);
                $line = substr($line, 0, $length);
                yield trim($line);
            }
        } finally {
            fclose($resource);
        }
    }

    /**
     * Process a resource line by line.
     *
     * @param string $path Path to input file (or stdin)
     *
     * @return void
     */
    public function processResource(string $path): void
    {
        foreach ($this->fileStream(fopen($path, 'r')) as $yield) {
            $line = (string) $yield;

            // Skip newlines without any other content.
            if (strlen($line) === 0) {
                continue;
            }

            // Was a ballset defined?
            if (str_contains($line, ',') === true) {
                $this->balls = $this->processBallLine($line);
                continue;
            }

            // Line is longer than 0 chars and returned 5 results.
            if (strlen($line) > 0
                && count($this->processBoardRowAndExtractResults($line)) === 5
            ) {
                // Skip to next line.
                continue;
            }

            // Last condition, we don't know what this is.
            if (strlen($line) > 0) {
                $this->errors[] = $line;
            }
        }
    }

    /**
     * Process the board row and if it is 5 numbers long check if it has won.
     *
     * @param string $line Line to be processed
     *
     * @return array<int>
     */
    public function processBoardRowAndExtractResults(string $line): array
    {
        $boardRow = $this->processBoardRowLine($line);

        // Did we get 5 numbers back?
        if (count($boardRow) === 5) {
            $this->board[] = $boardRow;

            // Did we reach the board row limit of 5?
            if (count($this->board) === 5) {
                $this->extractResults();
            }
        }

        return $boardRow;
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

        // This board won, add it to winners, sort winners by position
        if ($winPosition > 0) {
            $this->winners[$winPosition] = $this->board;
            ksort($this->winners);
        }

        // Board lost, add to losers.
        if ($winPosition === 0) {
            $this->losers[] = $this->board;
        }

        // Reset board and continue.
        $this->board = [];
    }

    /**
     * Process ball line.
     *
     * @param string $line Line to be processed
     *
     * @return array<int>
     */
    public function processBallLine(string $line): array
    {
        $balls = [];

        $exploded = explode(',', $line);

        foreach ($exploded as $text) {
            $potentialNumber = trim($text);

            // Only accept numeric chars.
            if (ctype_digit($potentialNumber) === false) {
                $message = "Invalid game: `$potentialNumber` is not a number.";

                throw new Exception($message);
            }

            $number = (int) $potentialNumber;

            if ($number > 75 || $number < 0) {
                $message = "Invalid game: `$number` is out of range 0-75.";

                throw new Exception($message);
            }

            $balls[] = (int) $number;
        }

        return $balls;
    }

    /**
     * Process row of board.
     *
     * @param string $line Line to be processed
     *
     * @return array<int>
     */
    public function processBoardRowLine(string $line): array
    {
        // Split line by multiple spaces, run through intval.
        $exploded = preg_split('/\s+/', $line);

        // If they are all digits and we have five of them.
        if (ctype_digit(implode('', $exploded)) === true
            && count($exploded) === 5
        ) {
            $parts = array_map('intval', $exploded);

            return $parts;
        }

        return [];
    }

    /**
     * Generate an array of winning rows.
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
     * Generate vertical winning rows.
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
     * Generate diagonal winning rows.
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
     * Check for a winning row.
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
        $potentialWins = $this->generateWinningRows($board);

        $gamePosition = 0;

        $winningNumbers = [];

        // Each game ball, in order.
        foreach ($this->balls as $bNumber) {
            // Increment game position.
            $gamePosition++;

            // Each row that counts as a winner.
            foreach ($potentialWins as $rKey => $row) {
                // Each number from this row.
                foreach ($row as $nKey => $rNumber) {
                    // If game ball is the same as row number.
                    if ($bNumber === $rNumber) {
                        // Add it to the winning rows array.
                        $winningNumbers[$rKey][$nKey] = $potentialWins[$rKey][$nKey];

                        // Remove it from the current row.
                        unset($potentialWins[$rKey][$nKey]);
                    }

                    // If the winning row is empty.
                    if (count($potentialWins[$rKey]) === 0) {
                        $winner = $this->getWinningRowFromNumbers($winningNumbers);
                        $this->winningRows[$gamePosition] = $winner;

                        // Return the position in the game it wins at.
                        return $gamePosition;
                    }
                }
            }
        }

        return 0;
    }

    /**
     * Returns the winning row if there was one.
     *
     * @param array<int, array<int>> $winningNumbers Arrays of winning numbers
     *
     * @return array<int>
     */
    public function getWinningRowFromNumbers(array $winningNumbers): array
    {
        foreach ($winningNumbers as $numbers) {
            if (count($numbers) === 5) {
                return $numbers;
            }
        }

        return [];
    }
}
