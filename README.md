# TimeTracking

### Dependencies
* Php 7.4 
* Mysql 8.0 
* Docker version 20.10.6, build 370c289 
* Docker Compose version 1.29.1, build c34c88b2

### Init Project

* Install local dependencies
```
composer install
```
* Configure .env.local,
* Generate JWT auth keys with secret
    * (optional for local) fill JWT_PASSPHRASE in env file
    * execute command
```
php bin/console lexik:jwt:generate-keypair
```
* Copy and rename docker/.env.dist to docker/.env
* Build docker-compose.yml
```
docker-compose --env-file=./docker/.env build
```
* Run containers
```
docker-compose --env-file=./docker/.env up -d
```
* (Optional) Stop containers, if needed
```
docker-compose  --env-file=./docker/.env stop
```
* Create database
```
docker-compose --env-file=./docker/.env exec php php bin/console doctrine:database:create
```
* Execute migrations
```
docker-compose --env-file=./docker/.env exec php php bin/console doctrine:migration:migrate
```
* Load demo fixtures
```
docker-compose --env-file=./docker/.env exec php php bin/console doctrine:fixtures:load --group=demo
```

### Docs
```
http://localhost/api/doc
```

### Tests
* For run phpunit tests
```
docker-compose --env-file=./docker/.env exec php composer phpunit
```
* Run phpunit with CS check
```
docker-compose --env-file=./docker/.env exec php composer test
```