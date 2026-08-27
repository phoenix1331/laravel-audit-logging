.PHONY: help up down build shell migrate seed test logs fresh

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "%-10s %s\n", $$1, $$2}'

up: ## Start containers in the background
	@docker compose up -d

down: ## Stop and remove containers
	@docker compose down

build: ## Build the app image
	@docker compose build

shell: ## Open a shell in the app container
	@docker compose exec app sh

migrate: ## Run database migrations
	@docker compose exec app php artisan migrate

seed: ## Seed the database
	@docker compose exec app php artisan db:seed

test: ## Run the test suite
	@docker compose exec app php artisan test

logs: ## Tail app container logs
	@docker compose logs -f app

fresh: ## Drop all tables and re-run migrations with seeding
	@docker compose exec app php artisan migrate:fresh --seed
