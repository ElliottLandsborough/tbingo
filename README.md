# tbingo

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![CircleCI](https://circleci.com/gh/ElliottLandsborough/tbingo.svg?style=svg)](https://circleci.com/gh/ElliottLandsborough/tbingo)
[![Code Style](https://github.styleci.io/repos/578703772/shield?style=flat&branch=main)](https://github.styleci.io/repos/578703772)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/1b90d4c402fa4eacbb1d3b90a56a0f0c)](https://www.codacy.com/gh/ElliottLandsborough/tbingo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ElliottLandsborough/tbingo&amp;utm_campaign=Badge_Grade)

Input specs:

 - Any line with a comma in it is considered a new game.
 - Any line with 5 numbers in it and no commas is considered a card row.
 - Cards have to be separated by at least one new line.
 - Each game element has to be separated by (at least) one new line.
 - Each board can be a maximum of 5 rows. Extra rows get dropped until the next blank line.
 - Only one game at a time.