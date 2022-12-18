# Terminal Bingo

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![CircleCI](https://circleci.com/gh/ElliottLandsborough/tbingo.svg?style=svg)](https://circleci.com/gh/ElliottLandsborough/tbingo)
[![Code Style](https://github.styleci.io/repos/578703772/shield?style=flat&branch=main)](https://github.styleci.io/repos/578703772)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/1b90d4c402fa4eacbb1d3b90a56a0f0c)](https://www.codacy.com/gh/ElliottLandsborough/tbingo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ElliottLandsborough/tbingo&amp;utm_campaign=Badge_Grade)
[![codecov](https://codecov.io/gh/ElliottLandsborough/tbingo/branch/main/graph/badge.svg?token=MTITW2TF2F)](https://codecov.io/gh/ElliottLandsborough/tbingo)

## Requirements

[Composer](https://getcomposer.org/)

[PHP](https://www.php.net/) 8+

[Docker](https://www.docker.com/) (optional)

## How to run

```bash
make
```

## How to test

```bash
make test
```

## How to test with docker

```bash
make docker
```

## Output

Numbers are highlighted when run in bash. Visible in the `Execute` step of a [recent completed CircleCI build](https://app.circleci.com/pipelines/github/ElliottLandsborough/tbingo/123/workflows/ce3041f9-a185-49df-8e6a-95507031a201/jobs/123).

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
