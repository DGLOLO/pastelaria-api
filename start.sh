#!/bin/bash

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE} API Pastelaria Deliciosa - Setup${NC}"
echo "=================================="

# Função para verificar se comando existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}



if ! command_exists docker; then
    echo -e "${RED}❌ Docker não está instalado. Instale o Docker primeiro.${NC}"
    exit 1
fi

if ! command_exists docker-compose && ! docker compose version >/dev/null 2>&1; then
    echo -e "${RED}❌ Docker Compose não está instalado. Instale o Docker Compose primeiro.${NC}"
    exit 1
fi



if lsof -Pi :80 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo -e "${RED}❌ Porta 80 já está em uso. Pare o serviço que está usando a porta 80.${NC}"
    exit 1
fi

if lsof -Pi :3306 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo -e "${YELLOW}⚠️  Porta 3306 já está em uso. Isso pode causar conflitos.${NC}"
fi


# Passo 1: Criar o .env
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}📄 Criando .env a partir do .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✅ Arquivo .env criado${NC}"
else
    echo -e "${GREEN}✅ Arquivo .env já existe${NC}"
fi

# Passo 2: Parar containers existentes
echo -e "${YELLOW} Parando containers existentes...${NC}"
docker compose down --remove-orphans

# Passo 3: Subir os containers em segundo plano
echo -e "${YELLOW} Subindo containers (Docker Compose)...${NC}"
docker compose up -d --build

# Passo 4: Esperar banco de dados ficar pronto
echo -e "${YELLOW} Aguardando MySQL iniciar...${NC}"
until docker compose exec db mysqladmin ping -h "localhost" --silent; do
    echo -n "."
    sleep 2
done
echo ""
echo -e "${GREEN} MySQL está pronto${NC}"

# Passo 5: Instalar dependências PHP
echo -e "${YELLOW} Instalando dependências com Composer...${NC}"
docker compose run --rm app composer install --optimize-autoloader

# Passo 6: Gerar chave da aplicação
echo -e "${YELLOW} Gerando APP_KEY...${NC}"
docker compose exec app php artisan key:generate --force

# Passo 7: Configurar permissões
echo -e "${YELLOW} Configurando permissões...${NC}"
docker compose exec app chown -R www-data:www-data /var/www/storage
docker compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/storage
docker compose exec app chmod -R 775 /var/www/bootstrap/cache

# Passo 8: Limpar caches
echo -e "${YELLOW} Limpando caches...${NC}"
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Passo 9: Rodar migrations
echo -e "${YELLOW} Rodando migrations e seeders (migrate:fresh --seed)...${NC}"
docker compose exec app php artisan migrate:fresh --seed --force

# Passo 11: Verificar se tudo está funcionando
echo -e "${YELLOW} Verificando se a aplicação está funcionando...${NC}"
sleep 5

if curl -s http://localhost >/dev/null; then
    echo -e "${GREEN} Aplicação está funcionando!${NC}"
else
    echo -e "${YELLOW}  Aplicação pode demorar alguns segundos para ficar disponível${NC}"
fi

echo ""
echo -e "${GREEN} Setup concluído com sucesso!${NC}"
echo "=================================="
echo -e "${BLUE} Acesse:${NC}"
echo -e "    API: ${GREEN}http://localhost/api${NC}"
echo -e "    Documentação: ${GREEN}http://localhost/api/documentation${NC}"
echo ""
echo -e "${BLUE} Para executar os testes:${NC}"
echo -e "   docker compose exec app php artisan test"
echo ""
echo -e "${BLUE} Para ver os logs:${NC}"
echo -e "   docker compose logs -f app"
echo ""
echo -e "${BLUE} Para parar a aplicação:${NC}"
echo -e "   docker compose down"
