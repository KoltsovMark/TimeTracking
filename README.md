# TimeTracking
### Dependencies
```
Php 7.4
Mysql 8.0
Docker version 20.10.6, build 370c289
Docker Compose version 1.29.1, build c34c88b2
```
### Init Project
```
Install local dependencies
composer install

Build docker-compose.yml
docker-compose build

Run containers
docker-compose up -d

Stop containers, if needed
docker-compose stop

Create database
docker-compose exec php php bin/console doctrine:database:create

Execute migrations
docker-compose exec php php bin/console doctrine:migration:migrate

Load demo fixtures
docker-compose exec php php bin/console doctrine:fixtures:load --group=demo

Generate JWT auth keys with secret
docker-compose exec php php bin/console lexik:jwt:generate-keypair
```
### Docs
```
http://localhost/api/doc
```