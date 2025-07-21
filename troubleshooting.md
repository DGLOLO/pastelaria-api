# 🔧 Guia de Troubleshooting

Este guia ajuda a resolver problemas comuns ao executar o projeto.

## ❌ Problemas Comuns

### 1. Docker não está instalado
**Erro:** `docker: command not found`

**Solução:**
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install docker.io docker-compose

# macOS
brew install docker docker-compose

# Windows
# Baixe Docker Desktop do site oficial
```

### 2. Porta 80 já está em uso
**Erro:** `Porta 80 já está em uso`

**Solução:**
```bash
# Verificar o que está usando a porta 80
sudo lsof -i :80

# Parar o serviço (exemplo: Apache)
sudo systemctl stop apache2

# Ou usar outra porta no docker-compose.yml
# Alterar "80:80" para "8080:80"
```

### 3. Porta 3306 já está em uso
**Erro:** `Porta 3306 já está em uso`

**Solução:**
```bash
# Verificar se MySQL local está rodando
sudo systemctl status mysql

# Parar MySQL local
sudo systemctl stop mysql

# Ou usar outra porta no docker-compose.yml
# Alterar "3306:3306" para "3307:3306"
```

### 4. Permissões negadas
**Erro:** `Permission denied`

**Solução:**
```bash
# Adicionar usuário ao grupo docker
sudo usermod -aG docker $USER

# Fazer logout e login novamente
# Ou executar com sudo
sudo ./start.sh
```

### 5. Container não inicia
**Erro:** `Container failed to start`

**Solução:**
```bash
# Ver logs do container
docker compose logs app

# Reconstruir containers
docker compose down
docker compose up --build

# Limpar volumes (cuidado: perde dados)
docker compose down -v
```

### 6. Banco de dados não conecta
**Erro:** `SQLSTATE[HY000] [2002] Connection refused`

**Solução:**
```bash
# Verificar se MySQL está rodando
docker compose ps

# Aguardar MySQL inicializar
docker compose logs db

# Testar conexão
docker compose exec db mysql -u root -p123456 -e "SELECT 1;"
```

### 7. APP_KEY não gerada
**Erro:** `No application encryption key has been specified`

**Solução:**
```bash
# Gerar APP_KEY manualmente
docker compose exec app php artisan key:generate

# Verificar se .env existe
ls -la .env
```

### 8. Migrations falham
**Erro:** `SQLSTATE[42S01]: Base table or view already exists`

**Solução:**
```bash
# Resetar banco
docker compose exec app php artisan migrate:fresh --seed

# Ou apenas rollback
docker compose exec app php artisan migrate:rollback
```

### 9. Composer não instala dependências
**Erro:** `Composer dependencies failed to install`

**Solução:**
```bash
# Limpar cache do Composer
docker compose exec app composer clear-cache

# Reinstalar dependências
docker compose exec app composer install --no-dev

# Verificar composer.json
cat composer.json
```

### 10. API não responde
**Erro:** `Connection refused` ou `404 Not Found`

**Solução:**
```bash
# Verificar se containers estão rodando
docker compose ps

# Verificar logs do nginx
docker compose logs nginx

# Verificar logs da aplicação
docker compose logs app

# Testar endpoint
curl http://localhost/api/products
```

## 🔍 Comandos Úteis

### Verificar status
```bash
# Status dos containers
docker compose ps

# Logs em tempo real
docker compose logs -f

# Logs de um serviço específico
docker compose logs -f app
```

### Manutenção
```bash
# Parar todos os containers
docker compose down

# Parar e remover volumes
docker compose down -v

# Reconstruir containers
docker compose up --build

# Limpar cache do Laravel
docker compose exec app php artisan cache:clear
```

### Banco de dados
```bash
# Acessar MySQL
docker compose exec db mysql -u root -p123456 pastelaria

# Backup do banco
docker compose exec db mysqldump -u root -p123456 pastelaria > backup.sql

# Restaurar backup
docker compose exec -T db mysql -u root -p123456 pastelaria < backup.sql
```

### Testes
```bash
# Executar todos os testes
docker compose exec app php artisan test

# Executar teste específico
docker compose exec app php artisan test --filter=OrderTest

# Ver cobertura de testes
docker compose exec app php artisan test --coverage
```

## 📞 Suporte

Se nenhuma das soluções acima resolver seu problema:

1. **Verifique os logs:** `docker compose logs`
2. **Verifique a versão do Docker:** `docker --version`
3. **Verifique o sistema operacional:** `uname -a`
4. **Abra uma issue** no repositório com:
   - Erro completo
   - Sistema operacional
   - Versão do Docker
   - Logs relevantes

## 🆘 Emergência

Se nada funcionar, você pode:

```bash
# Resetar tudo
docker compose down -v
docker system prune -a
./start.sh
```

**⚠️ Cuidado:** Isso remove todos os dados e containers! 