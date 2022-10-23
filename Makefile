# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start start-debug down logs sh composer vendor sf cc cs static lint

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

start: build up ## Build and start the containers

start-debug: ## Build and start the containers with Xdebug enable
start-debug: XDEBUG_MODE=debug
start-debug: build up

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

##
## —— Linter 🔬 ————————————————————————————————————————————————————————————————
##
cs: ## Check coding style
	@$(PHP_CONT) ./vendor/bin/php-cs-fixer fix --verbose --ansi

static: ## Perform static analysis
	@$(PHP_CONT) ./vendor/bin/phpstan analyse --memory-limit 256M

lint: cs static ## Check coding style and perform static analysis
