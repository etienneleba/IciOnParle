build-project:
	docker-compose build 

re-build-project: down build-project

up:
	docker-compose up -d $(c)

up-prod:
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d nginx php db etherpad

down: 
	docker-compose down

stop: 
	docker-compose stop $(c)

cmd?=list
console: 
	docker-compose run --rm php php bin/console $(cmd)

schema-update:
	docker-compose run --rm php php bin/console doctrine:schema:update -f 

cache-clear:
	docker-compose run --rm php php bin/console cache:clear 

cache-warm:
	docker-compose run --rm php php bin/console cache:warm 

composer-require:
	docker-compose run --rm php composer require $(req)

composer-require-dev:
	docker-compose run --rm php composer require --dev $(req)

composer-install: 
	docker-compose run --rm php composer install 

composer-install-prod:
	docker-compose run --rm php composer install -o

composer-update:
	docker-compose run --rm php composer update

yarn-install:
	# To be log as the node user on the container and be allowed to use the npm/yarn cache (if you have a better way pls tell me)
	LOCAL_USER=1000 docker-compose run --rm node yarn install 

yarn-build:
	docker-compose run --rm node yarn build

yarn-watch: 
	docker-compose run --rm node yarn watch

new-push: build-symfony-project schema-update

# Change the name of the remote if you need 
deploy-test:
	git push test master

# deploy-prod:
#	git push prod master

build-symfony-project : composer-install yarn-install yarn-build cache-clear cache-warm #if you use symfony encore

build-symfony-project-prod : composer-install-prod yarn-install yarn-build cache-clear cache-warm #if you use symfony encore

maintenance-soft-on:
	docker-compose run --rm php php bin/console corley:maintenance:soft-lock on

maintenance-soft-off:
	docker-compose run --rm php php bin/console corley:maintenance:soft-lock off

maintenance-hard-on:
	docker-compose run --rm php php bin/console corley:maintenance:lock on

maintenance-hard-off:
	docker-compose run --rm php php bin/console corley:maintenance:lock off

nginx-reload:
	docker-compose exec nginx nginx -s reload

