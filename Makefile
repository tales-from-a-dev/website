# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console
PHPUNIT  = $(PHP_CONT) bin/phpunit

# Misc
.DEFAULT_GOAL = help

##
## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
##
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help

##
## —— Docker 🐳 ————————————————————————————————————————————————————————————————
##
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache
.PHONY: build

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach
.PHONY: up

up-dev: ## Start the docker hub in detached mode (no logs) for debugging
	@XDEBUG_MODE=debug $(DOCKER_COMP) up --detach
.PHONY: up-dev

up-test: ## Start the docker hub in detached mode (no logs) for testing
	@XDEBUG_MODE=coverage $(DOCKER_COMP) -f docker-compose.yml -f docker-compose.override.yml -f docker-compose.test.yml up --detach
.PHONY: up-test

up-prod: ## Start the docker hub in detached mode (no logs) for production
	@$(DOCKER_COMP) -f docker-compose.yml -f docker-compose.prod.yml up --detach
.PHONY: up-prod

start: build up ## Build and start the containers
.PHONY: start

stop: ## Stop the docker hub
	@$(DOCKER_COMP) stop
.PHONY: stop

down: ## Stop the docker hub and remove all containers, networks, volumes, and images
	@$(DOCKER_COMP) down --remove-orphans
.PHONY: down

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow
.PHONY: logs

config: ## Parse, resolve and render compose file
	@$(eval f ?=)
	@$(DOCKER_COMP) $(f) config
.PHONY: config

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh
.PHONY: sh

##
## —— Composer 🧙 ——————————————————————————————————————————————————————————————
##
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)
.PHONY: composer

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer
.PHONY: vendor

##
## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
##
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)
.PHONY: sf

cc: c=c:c ## Clear the cache
cc: sf
.PHONY: cc

container: c=debug:container ## Display all possible services in the container
container: sf
.PHONY: container

envs: c=debug:container --env-vars ## Display all environments variables
envs: sf
.PHONY: envs

parameters: c=debug:container --parameters ## Display all available parameters
parameters: sf
.PHONY: parameters

router: c=debug:router ## Display all available route
router: sf
.PHONY: router

##
## —— Database 🔮 ——————————————————————————————————————————————————————————————
##
db: db-create db-update db-load ## Create the database and load the fixtures
.PHONY: db

db-create: ## Create database
	@$(SYMFONY) doctrine:database:drop --if-exists --force
	@$(SYMFONY) doctrine:database:create --if-not-exists
.PHONY: db-create

db-diff: ## Generate a migration by comparing your current database to your mapping information
	@$(SYMFONY) doctrine:migration:diff
.PHONY: db-diff

db-migrate: ## Migrate database schema to the latest available version
	@$(SYMFONY) doctrine:migration:migrate -n
.PHONY: db-migrate

db-load: ## Reset the database fixtures
	@$(SYMFONY) doctrine:fixtures:load --no-interaction --purge-with-truncate
.PHONY: db-load

db-update: ## Force database update
	@$(SYMFONY) doctrine:schema:update --complete --force
.PHONY: db-update

db-validate: ## Check the ORM mapping
	@$(SYMFONY) doctrine:schema:validate
.PHONY: db-validate

db-test: ## Create test database
	@$(SYMFONY) --env=test doctrine:database:drop --if-exists --force
	@$(SYMFONY) --env=test doctrine:database:create --if-not-exists
	@$(SYMFONY) --env=test doctrine:schema:update --complete --force
.PHONY: db-test

##
## —— Linter 💫 ————————————————————————————————————————————————————————————————
##
phpcsfixer-dry: ## Check coding style in dry mode
	@$(DOCKER_COMP) exec php ./vendor/bin/php-cs-fixer fix --dry-run --diff --verbose --ansi
.PHONY: phpcsfixer-dry

phpcsfixer: ## Check coding style
	@$(DOCKER_COMP) exec php ./vendor/bin/php-cs-fixer fix --verbose --ansi
.PHONY: phpcsfixer

phpstan: ## Perform static analysis
	@$(PHP_CONT) ./vendor/bin/phpstan analyse --memory-limit 256M
.PHONY: phpstan

phpstan-baseline: ## Update baseline file
	@$(PHP_CONT) ./vendor/bin/phpstan analyse --memory-limit 256M --generate-baseline
.PHONY: phpstan-baseline

rector-dry: ## Perform code migration/refactoring with Rector in dry mode
	@$(PHP_CONT) ./vendor/bin/rector process --dry-run
.PHONY: rector-dry

rector: ## Perform code migration/refactoring with Rector
	@$(PHP_CONT) ./vendor/bin/rector process
.PHONY: rector

twigcsfixer-dry: ## Check Twig coding style in dry mode
	@$(PHP_CONT) ./vendor/bin/twig-cs-fixer lint
.PHONY: twigcsfixer-dry

twigcsfixer: ## Check Twig coding style
	@$(PHP_CONT) ./vendor/bin/twig-cs-fixer lint --fix
.PHONY: twigcsfixer

fixer: phpcsfixer twigcsfixer ## Check PHP/Twig coding style
.PHONY: fixer

linter: ## Twig / Yaml & check DB mapping
	@$(SYMFONY) lint:twig templates/ --format=github
	@$(SYMFONY) lint:yaml translations/ config/ --format=github
	@$(SYMFONY) doctrine:schema:validate --skip-sync
.PHONY: linter

##
## —— Tests ⚗️ ————————————————————————————————————————————————————————————————
##
test: ## Run tests with code coverage or pass the parameter "f=" to test a specific file, example: make test f=tests/Unit/Entity/ProjectTest.php
	@$(eval f ?=)
	@$(DOCKER_COMP) exec -e XDEBUG_MODE=off -e APP_ENV=test php ./bin/phpunit $(f)
.PHONY: test

coverage: ## Run tests with code coverage
	@$(DOCKER_COMP) exec -e XDEBUG_MODE=coverage -e APP_ENV=test php ./bin/phpunit
.PHONY: coverage
