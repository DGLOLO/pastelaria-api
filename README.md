# 🍕 API Pastelaria Deliciosa

API RESTful para gerenciamento de pedidos de uma pastelaria desenvolvida com Laravel.

## 📋 Requisitos

- Docker
- Docker Compose
- Git

## 🚀 Instalação e Execução

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
- **API:** http://localhost/api
- **Documentação Swagger:** http://localhost/api/documentation
- **Preview do Email:** http://localhost/email-preview

## 🏗️ Estrutura do Projeto

### Módulos Implementados

#### 📧 Clientes (Customers)
- **CRUD completo** com validações
- **Email único** - não permite duplicatas
- **Soft Delete** implementado
- **Campos:** nome, email, telefone, data_nascimento, endereço, complemento, bairro, cep

#### 🍕 Produtos (Products)
- **CRUD completo** com validações
- **Fotos obrigatórias** para produtos
- **Soft Delete** implementado
- **10 produtos pré-definidos** via seeder
- **Campos:** nome, preço, foto

#### 📦 Pedidos (Orders)
- **CRUD completo** com validações
- **Relacionamento N:N** com produtos
- **Email automático** com detalhes do pedido
- **Soft Delete** implementado
- **Campos:** cliente, produtos, data de criação

## 📧 Funcionalidade de Email

### Email de Confirmação
- **Template HTML** responsivo e bonito
- **Fotos dos produtos** incluídas
- **Detalhes completos** do pedido
- **Dados do cliente** e endereço
- **Cálculo automático** do total

### Preview do Email
- **Visualização em tempo real** do email que será enviado
- **Interface web** para testar diferentes pedidos
- **Design responsivo** que funciona em qualquer dispositivo
- **Dados fictícios** para demonstração
- **Acesso:** http://localhost/email-preview

### Configuração de Email
O projeto está configurado para usar **Mailtrap** em desenvolvimento:
```env
MAIL_MAILER=log
```

Para produção, configure suas credenciais SMTP no arquivo `.env`.

## 🧪 Testes

### Executar todos os testes
```bash
docker compose exec app php artisan test
```

### Testes implementados
- ✅ **CustomerTest** - Testes de criação de clientes
- ✅ **ProductTest** - Testes de criação de produtos  
- ✅ **OrderTest** - Testes de criação de pedidos
- ✅ **OrderEmailTest** - Testes de envio de emails

### Cobertura de testes
- Validações de entrada
- Criação de registros
- Relacionamentos entre entidades
- Envio de emails
- Soft delete
- Validação de email único

## 🐳 Docker

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

## 📚 Documentação da API

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
- `DELETE /api/orders/{id}` - Excluir pedido

### Exemplo de criação de pedido
```bash
curl -X POST http://localhost/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "products": [1, 2, 3]
  }'
```

## 🔧 Tecnologias Utilizadas

- **Laravel 11** - Framework PHP
- **MySQL 8.0** - Banco de dados
- **Docker** - Containerização
- **PHPUnit** - Testes
- **Swagger/OpenAPI** - Documentação
- **Mail** - Sistema de emails

## 📋 Checklist de Requisitos

### ✅ Requisitos Obrigatórios
- [x] **Email único** para clientes
- [x] **Fotos obrigatórias** para produtos
- [x] **Validações** implementadas
- [x] **Produtos pré-definidos** via seeder
- [x] **Pedidos com N produtos**
- [x] **Cliente com N pedidos**
- [x] **Email automático** com detalhes
- [x] **Soft Delete** em todos os módulos

### ✅ Padronização
- [x] **PSR** seguido
- [x] **Nomenclatura americana**
- [x] **Testes unitários** completos
- [x] **Docker** bem configurado

### ✅ Funcionalidades Extras
- [x] **Documentação Swagger**
- [x] **Email HTML responsivo**
- [x] **Fotos nos emails**
- [x] **Validações robustas**
- [x] **Relacionamentos corretos**

## 🚀 Deploy

### Produção
1. Configure as variáveis de ambiente no `.env`
2. Configure o servidor de email
3. Execute as migrations
4. Configure o servidor web (Nginx/Apache)

### Variáveis importantes
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=pastelaria
DB_USERNAME=root
DB_PASSWORD=123456

MAIL_MAILER=smtp
MAIL_HOST=seu-smtp.com
MAIL_PORT=587
MAIL_USERNAME=seu-email
MAIL_PASSWORD=sua-senha
```

## 📞 Suporte

Para dúvidas ou problemas, abra uma issue no repositório.

---

**Desenvolvido com ❤️ usando Laravel**
