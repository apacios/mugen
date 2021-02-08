.DEFAULT_GOAL:=help
SHELL:=/bin/bash
APP_IP = `docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' mugen_app`

##@ Application

app-launch: docker-up docker-setup app-fixtures app-browser ## Start & setup containers and launch browser

app-test: ## Use PHP Static Analysis Tool
	docker-compose exec app sh -c "vendor/bin/phpstan analyse -c phpstan.neon src --level 7"

app-browser: ## Open app into the browser
	@xdg-open http://${APP_IP} | exit

app-build_db: ## Build databse
	docker-compose exec app sh -c "bin/console d:s:u -f"

app-fixtures: ## Load fixtures
	docker-compose exec app sh -c "bin/console d:f:l -n"

app-composer: ## Install composer packages
	docker-compose exec app sh -c "composer install -o --prefer-dist --classmap-authoritative --no-progress --no-ansi --no-interaction"

app-clear_cache: ## Clear Symfony cache
	docker-compose exec app sh -c "php bin/console cache:clear"

##@ Docker

docker-up: ## Start docker containers
	docker-compose -f docker-compose.yml up -d --build

docker-setup: app-composer app-build_db app-clear_cache ## Setup/Build PHP & (node)JS dependencies

docker-ssh: ## Start SSH connexion to mugen_app
	docker exec -ti mugen_app bash

docker-down: ## Stop docker containers
	docker-compose down

docker-remove: ## Remove mugen_app mugen_db mugen_maildev containers
	@docker rm -f mugen_app mugen_db mugen_maildev
	@printf "${COLOR_INFO}Containers mugen_app mugen_db mugen_maildev removed.${COLOR_RESET}\n"

##@ Yarn

yarn-install: ## Build assets with yarn
	docker-compose exec app sh -c "yarn"

yarn-dev: ## Watch building assets with dev option
	docker-compose exec app sh -c "yarn dev --watch"

yarn-build: ## Build assets for prod
	docker-compose exec app sh -c "yarn build"

##@ Helpers

help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)