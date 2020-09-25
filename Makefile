build-project:
	docker-compose build 

re-build-project: down build-project

up:
	docker-compose up -d $(c)

down: 
	docker-compose down $(c)

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

build-symfony-project : composer-install yarn-install yarn-build cache-clear #if you use symfony encore

