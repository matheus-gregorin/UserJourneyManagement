deploy:
	@echo "Down any existing containers..."
	docker compose down

	# @echo "Pull on repositorie..."
	# git pull

	@echo "Deploying the containers..."
	docker compose up -d

down:
	@echo "Down any existing containers..."
	docker compose down