#!/bin/bash

echo "🚀 Iniciando setup do projeto..."

# Passo 1: Criar o .env (se ainda não existir)
if [ ! -f ".env" ]; then
  echo "📄 Criando .env a partir do .env.example..."
  cp .env.example .env
fi

# Passo 2: Subir os containers em segundo plano
echo "🐳 Subindo containers (Docker Compose)..."
docker compose up -d --build

# Passo 3: Esperar banco de dados ficar pronto
echo "⏳ Aguardando MySQL iniciar..."
until docker compose exec db mysqladmin ping -h "localhost" --silent; do
  sleep 2
done

# Passo 4: Instalar dependências PHP
echo "📦 Instalando dependências com Composer..."
docker compose run --rm app composer install --no-dev --optimize-autoloader

# Passo 5: Gerar chave da aplicação
echo "🔑 Gerando APP_KEY..."
docker compose run --rm app php artisan key:generate

# Passo 6: Rodar migrations
echo "🧱 Rodando migrations..."
docker compose exec app php artisan migrate

echo "✅ Projeto pronto! Acesse: http://localhost"
