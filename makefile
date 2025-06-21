deploy:
	@echo "Down bott_manager..."
	docker stop bott_manager

	@echo "Pull on repository..."
	git pull

	@echo "Deploying bott_manager..."
	docker start bott_manager

stop:
	@echo "Down bott_manager container..."
	docker stop bott_manager

queue-restart:
	@echo "Reiniciando a fila em background no container bott_manager..."
	docker exec -it bott_manager php artisan queue:restart
	@echo "Comando de reinício da fila enviado para o background."

down:
	@echo "Down all containers..."
	docker composer down

up:
	@echo "Down all containers..."
	docker composer up -d
