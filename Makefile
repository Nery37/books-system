.PHONY: install up down build migrate seed fresh test clean logs

# Comando principal para instalação completa
install: build up composer-install migrate seed
	@echo "✅ Sistema instalado com sucesso!"
	@echo "🌐 API disponível em: http://localhost:8000"
	@echo "🗄️  phpMyAdmin disponível em: http://localhost:8080"
	@echo ""
	@echo "Para o frontend, execute:"
	@echo "cd frontend && npm install && npm run serve"

# Build dos containers
build:
	@echo "🔨 Building containers..."
	docker-compose build

# Subir os containers
up:
	@echo "🚀 Starting containers..."
	docker-compose up -d

# Parar os containers
down:
	@echo "🛑 Stopping containers..."
	docker-compose down

# Instalar dependências do Composer
composer-install:
	@echo "📦 Installing Composer dependencies..."
	docker-compose exec app composer install

# Executar migrations
migrate:
	@echo "🗄️  Running migrations..."
	docker-compose exec app php artisan migrate

# Executar seeds
seed:
	@echo "🌱 Running seeds..."
	docker-compose exec app php artisan db:seed

# Fresh migrate + seed
fresh:
	@echo "🔄 Fresh migration with seeds..."
	docker-compose exec app php artisan migrate:fresh --seed

# Executar testes
test:
	@echo "🧪 Running tests..."
	docker-compose exec app php artisan test

# Limpar cache e otimizar
clean:
	@echo "🧹 Cleaning cache..."
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear
	docker-compose exec app composer dump-autoload

# Ver logs
logs:
	docker-compose logs -f

# Gerar key da aplicação
key:
	docker-compose exec app php artisan key:generate

# Acesso ao container
shell:
	docker-compose exec app bash

# Backup do banco
backup:
	@echo "💾 Creating database backup..."
	docker-compose exec db mysqldump -u root -proot livros_db > backup_$(shell date +%Y%m%d_%H%M%S).sql

# Restaurar backup (uso: make restore FILE=backup_file.sql)
restore:
	@echo "🔄 Restoring database from $(FILE)..."
	docker-compose exec -T db mysql -u root -proot livros_db < $(FILE)
