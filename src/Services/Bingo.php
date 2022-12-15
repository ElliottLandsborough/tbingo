<?php

namespace ElliottLandsborough\TerminalBingo\Services;

use Exception;

class Bingo
{
    protected function stdinStream()
    {
        while ($line = fgets(STDIN)) {
            yield $line;
        }
    }

    public function start($resource)
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
                        $this->checkForWinner($balls, $board);

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
    }

    protected function checkForWinner($balls, $board)
    {
        print_r($balls);
        print_r($board);
    }
}
