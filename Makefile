up: docker-up
init: docker-down-clear docker-pull docker-build docker-up manager-init
test: manager-test

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

manager-init: manager-composer-install

manager-composer-install:
	docker-compose run --rm php-cli composer install

manager-test:
	docker-compose run --rm php-cli php bin/phpunit

console:
	docker-compose run --rm php-cli php bin/console $(c)

require:
	docker-compose run --rm php-cli composer require $(p)