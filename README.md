# Terminal Bingo

https://github.com/ElliottLandsborough/tbingo

Part solution to https://adventofcode.com/2021/day/4

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![CircleCI](https://circleci.com/gh/ElliottLandsborough/tbingo.svg?style=svg)](https://circleci.com/gh/ElliottLandsborough/tbingo)
[![Code Style](https://github.styleci.io/repos/578703772/shield?style=flat&branch=main)](https://github.styleci.io/repos/578703772)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/1b90d4c402fa4eacbb1d3b90a56a0f0c)](https://www.codacy.com/gh/ElliottLandsborough/tbingo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ElliottLandsborough/tbingo&amp;utm_campaign=Badge_Grade)
[![codecov](https://codecov.io/gh/ElliottLandsborough/tbingo/branch/main/graph/badge.svg?token=MTITW2TF2F)](https://codecov.io/gh/ElliottLandsborough/tbingo)

## How to run

```bash
composer install
./exec.sh
```

## How to test

```bash
composer install
./vendor/bin/phpunit
```

## How to test with docker

```bash
docker-compose up
```

## Output

```bash
There were 3 wins and 0 losses.

The first win after 8 numbers:

+---+----------------+
| B | 14 21 17 24  4 |
| I | 10 16 15  9 19 |
| N | 18  8 23 26 20 |
| G | 22 11 13  6  5 |
| O |  2  0 12  3  7 |
+---+----------------+
```

## Input Specs

```
 - Any line with comma separated numbers is considered a new game (e.g 1,2,3,4,5,6)
 - Any line with 5 numbers in it and no commas is considered a card row (e.g 1 2 3 4 5)
 - Cards have to be separated by at least one new line
 - Each game element has to be separated by (at least) one new line
 - Boards are counted 5 rows at a time. A board with too many rows will offset the next board.
 - Only one game per execution
```
