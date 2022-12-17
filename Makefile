.PHONY: php test docker
default: php

php:
	composer install
	./bin/app checkBoards < tests/fixtures/input.txt

test:
	composer install
	./vendor/bin/phpunit

docker:
	docker-compose up