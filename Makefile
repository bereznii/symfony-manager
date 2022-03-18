up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear manager-clear docker-pull docker-build docker-up manager-init
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

manager-clear:
	docker run --rm -v ${PWD}:/app --workdir=/app alpine rm -f .ready

manager-init: manager-composer-install manager-assets-install manager-wait-db manager-migrations manager-fixtures manager-ready

manager-composer-install:
	docker-compose run --rm php-cli composer install

manager-assets-install:
	docker-compose run --rm node yarn install
	docker-compose run --rm node npm rebuild node-sass

manager-wait-db:
	until docker-compose exec -T postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

manager-migrations:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

manager-fixtures:
	docker-compose run --rm php-cli php bin/console doctrine:fixtures:load --no-interaction

manager-ready:
	docker run --rm -v ${PWD}:/app --workdir=/app alpine touch .ready

manager-test:
	docker-compose run --rm php-cli php bin/phpunit

console:
	docker-compose run --rm php-cli php bin/console $(c)

require:
	docker-compose run --rm php-cli composer require $(p)