#!/usr/bin/env bash

set -o errexit
set -o nounset
set -o pipefail
if [[ "${TRACE-0}" == "1" ]]; then
    set -o xtrace
fi

if [[ "${1-}" =~ ^-*h(elp)?$ ]]; then
    echo 'Usage: ./exec.sh'
    exit
fi

cd "$(dirname "$0")"

main() {
    ./bin/app checkBoards < input.txt
}

main "$@"