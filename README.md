#  API Pastelaria 

API RESTful para gerenciamento de pedidos de uma pastelaria desenvolvida com Laravel.

##  Requisitos

- Docker
- Docker Compose
- Git

##  Instalação e Execução

### 1. Clone o repositório
```bash
git clone git@github.com:DGLOLO/pastelaria-api.git
cd pastelaria-api
```

### 2. Torna o script executável
```bash
chmod +x start.sh
```

### 3. Execute o projeto
```bash
./start.sh
```

Este script irá:
- Construir as imagens Docker
- Iniciar os containers
- Executar as migrations
- Executar os seeders
- Instalar dependências

### 4. Acesse a aplicação
- **API:** http://localhost
- **Documentação Swagger:** http://localhost/api/documentation
- **Preview do Email:** http://localhost/email-preview

### 5. Comando que para a aplicação
```bash
docker compose down

### 6. Comando para reiniciar a aplicação
```bash
docker compose up -d



### Configuração de Email
O projeto está configurado para usar **Mailtrap** em desenvolvimento:
```env
MAIL_MAILER=log
```
Para produção, configure suas credenciais SMTP no arquivo `.env`.

##  Testes

### Executar todos os testes
```bash
docker compose exec app php artisan test
```

### Executar testes com cobertura
```bash
docker compose exec app vendor/bin/phpunit --coverage-html coverage/html --coverage-text
```

Após executar, acesse o relatório HTML em: `coverage/html/index.html`

### Testes implementados
-  **CustomerTest** - 10 testes (criação, listagem, atualização, exclusão, validações)
-  **ProductTest** - 9 testes (criação, listagem, atualização, exclusão, validações)
-  **OrderTest** - 19 testes:
  - Criação de pedido com um ou mais produtos
  - Listagem de pedidos (com e sem paginação)
  - Detalhamento de pedido (existente e inexistente)
  - Atualização de pedido (status, itens, ou ambos)
  - Exclusão de pedido (soft delete)
  - Validações (sem produtos, sem cliente, cliente inválido, campos obrigatórios)
  - Envio de emails (2 testes: `test_pedido_criado_email_enviado` e `test_email_mensagem_sucesso`)
  - Validação de preço e relacionamentos

### Testes negativos implementados

#### CustomerTest
- ❌ Email ausente
- ❌ Email inválido
- ❌ Email duplicado
- ❌ Cliente não encontrado
- ❌ Criar cliente sem dados obrigatórios

#### ProductTest
- ❌ Foto ausente
- ❌ Foto inválida (ex: PDF ao invés de imagem)
- ❌ Preço inválido
- ❌ Campos obrigatórios ausentes

#### OrderTest
- ❌ Pedido sem produtos (`test_falha_ao_criar_pedido_sem_produtos`)
- ❌ Pedido sem cliente (`test_criar_pedido_sem_cliente`)
- ❌ Cliente inválido (`test_criar_pedido_com_cliente_invalido`)
- ❌ Campos obrigatórios faltando (`test_Campos_obrigadorios_do_pedido_faltado`)
- ❌ Pedido inexistente (`test_detalha_pedido_inexistente`)
- ❌ Status inválido na atualização (`test_falha_ao_atualizar_pedido_com_status_invalido`)
- ❌ Produto inexistente na atualização (`test_falha_ao_atualizar_pedido_com_produto_inexistente`)
- ❌ Pedido inexistente na atualização (`test_falha_ao_atualizar_pedido_inexistente`)

### Cobertura de código
A cobertura de código está configurada no `phpunit.xml`. O Xdebug está instalado.

**Gerar relatório de cobertura:**
```bash
./coverage.sh
```

**Visualizar no navegador:**
```bash
./open-coverage.sh
```

Os relatórios são gerados em:
- HTML: `coverage/html/index.html` (abrir no navegador)
- Clover XML: `coverage/clover.xml`
- Texto: `coverage/coverage.txt`

##  Docker

### Containers
- **app** - Laravel PHP 8.2
- **db** - MySQL 8.0
- **nginx** - Servidor web

### Comandos úteis
```bash
# Ver logs
docker compose logs -f app

# Acessar container
docker compose exec app bash

# Executar migrations
docker compose exec app php artisan migrate

# Executar seeders
docker compose exec app php artisan db:seed

# Limpar cache
docker compose exec app php artisan cache:clear
```

##  Documentação da API

### Endpoints Principais

#### Clientes
- `GET /api/customers` - Listar clientes
- `POST /api/customers` - Criar cliente
- `GET /api/customers/{id}` - Buscar cliente
- `PUT /api/customers/{id}` - Atualizar cliente
- `DELETE /api/customers/{id}` - Excluir cliente

#### Produtos
- `GET /api/products` - Listar produtos
- `POST /api/products` - Criar produto
- `GET /api/products/{id}` - Buscar produto
- `PUT /api/products/{id}` - Atualizar produto
- `DELETE /api/products/{id}` - Excluir produto

#### Pedidos
- `GET /api/orders` - Listar pedidos
- `POST /api/orders` - Criar pedido
- `GET /api/orders/{id}` - Buscar pedido
- `PUT /api/orders/{id}` - Atualizar pedido (status e/ou itens)
- `DELETE /api/orders/{id}` - Excluir pedido



##  Tecnologias Utilizadas

- **Laravel 11** - Framework PHP
- **MySQL 8.0** - Banco de dados
- **Docker** - Containerização
- **PHPUnit** - Testes
- **Swagger/OpenAPI** - Documentação
- **Mail** - Sistema de emails


