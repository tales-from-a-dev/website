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
.PHONY        : help build up up-dev start down logs sh composer vendor sf cc db dbc dbd dbm dbl dbu dbv dbt cs static lint test

##
## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
##
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##
## —— Docker 🐳 ————————————————————————————————————————————————————————————————
##
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

up-dev: ## Start the docker hub in detached mode (no logs) for debugging
	@XDEBUG_MODE=debug $(DOCKER_COMP) up --detach

up-test: ## Start the docker hub in detached mode (no logs) for testing
	@XDEBUG_MODE=coverage $(DOCKER_COMP) -f docker-compose.yml -f docker-compose.override.yml -f docker-compose.test.yml up --detach

start: build up ## Build and start the containers

stop: ## Stop the docker hub
	@$(DOCKER_COMP) stop

down: ## Stop the docker hub and remove all containers, networks, volumes, and images
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

##
## —— Composer 🧙 ——————————————————————————————————————————————————————————————
##
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

##
## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
##
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

container: c=debug:container ## Display all possible services in the container
container: sf

envs: c=debug:container --env-vars ## Display all environments variables
envs: sf

parameters: c=debug:container --parameters ## Display all available parameters
parameters: sf

router: c=debug:router ## Display all available route
router: sf

##
## —— Database 🔮 ——————————————————————————————————————————————————————————————
##
db: dbc dbu dbl ## Create the database and load the fixtures

dbc: ## Create database
	@$(SYMFONY) doctrine:database:drop --if-exists --force
	@$(SYMFONY) doctrine:database:create --if-not-exists

dbd: ## Generate a migration by comparing your current database to your mapping information
	@$(SYMFONY) doctrine:migration:diff

dbm: ## Migrate database schema to the latest available version
	@$(SYMFONY) doctrine:migration:migrate -n

dbl: ## Reset the database fixtures
	@$(SYMFONY) doctrine:fixtures:load --no-interaction --purge-with-truncate

dbu: ## Force database update
	@$(SYMFONY) doctrine:schema:update --force

dbv: ## Check the ORM mapping
	@$(SYMFONY) doctrine:schema:validate

dbt: ## Create test database
	@$(SYMFONY) --env=test doctrine:database:drop --if-exists --force
	@$(SYMFONY) --env=test doctrine:database:create --if-not-exists
	@$(SYMFONY) --env=test doctrine:schema:update --force

##
## —— Linter 💫 ————————————————————————————————————————————————————————————————
##
cs-dry: ## Check coding style in dry mode
	@$(PHP_CONT) ./vendor/bin/php-cs-fixer fix --dry-run --diff --verbose --ansi

cs: ## Check coding style
	@$(PHP_CONT) ./vendor/bin/php-cs-fixer fix --verbose --ansi

static: ## Perform static analysis
	@$(PHP_CONT) ./vendor/bin/phpstan analyse --memory-limit 256M

lint: cs static ## Check coding style and perform static analysis

##
## —— Tests ⚗️ ————————————————————————————————————————————————————————————————
##
test: ## Run tests with code coverage or pass the parameter "f=" to test a specific file, example: make test f=tests/Unit/Entity/ProjectTest.php
	@$(eval f ?=)
	@$(DOCKER_COMP) exec -e XDEBUG_MODE=coverage php ./vendor/bin/simple-phpunit $(f)
