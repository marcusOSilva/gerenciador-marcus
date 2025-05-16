# Gerenciador de Tarefas - Laravel 9 (API RESTful)

Este projeto é uma API RESTful com Laravel 9, dockerizada, com autenticação via JWT, controle de permissões, logs em MongoDB e uso de filas com RabbitMQ.

---

## Tecnologias Utilizadas

- Laravel 9
- Docker + Docker Compose
- PostgreSQL
- MongoDB (logs)
- RabbitMQ (filas)
- JWT Auth (`tymon/jwt-auth`)
- Repository Pattern
- Service Layer
- Policies de autorização

---

## Como rodar

### Pré-requisitos

- Docker Desktop
- Docker Compose

### Passo a passo

1. Clone o projeto:

```bash
git clone https://github.com/marcusOSilva/gerenciador-marcus
cd gerenciador-marcus
```

2. Copie o arquivo `.env`:

```bash
cp .env.example .env
```

3. Suba os containers:

```bash
docker-compose up -d --build
```

Certifique-se de que o seu `Dockerfile` usa PHP 8.2:

```Dockerfile
FROM php:8.2-fpm
```

4. Acesse o container da aplicação:

```bash
docker exec -it laravel_app bash
```

5. Gere a chave da aplicação:

```bash
php artisan key:generate
```

6. Rode as migrations e seeders:

```bash
php artisan migrate --seed
```

7. A API estará disponível em:

```
http://localhost:8000
```

---

## Testes Automatizados

Este projeto já vem com testes automatizados utilizando mocks para:

- Autenticação (registro e login)
- Criação, edição e exclusão de projetos
- Criação de tarefas (com fila e log)

### Rodar os testes:

```bash
php artisan test tests/Feature
```

Todos os testes devem passar com sucesso, simulando chamadas ao `LogService` e `Queue`.

---

## Teste com Postman

- Será fornecido um arquivo de collection `.json` com todas as rotas da API para importação no Postman.

### Endpoints implementados:

#### Autenticação
- `POST /api/register`
- `POST /api/login`
- `GET /api/me`
- `POST /api/logout`

#### Projetos
- `GET /api/projects`
- `POST /api/projects`
- `GET /api/projects/{id}`
- `PUT /api/projects/{id}`
- `DELETE /api/projects/{id}`

#### Tarefas
- `GET /api/projects/{id}/tasks`
- `POST /api/projects/{id}/tasks`
- `GET /api/tasks/{id}`
- `PUT /api/tasks/{id}`
- `DELETE /api/tasks/{id}`

---

## Observações

- Logs de autenticação, criação e edição de projetos/tarefas são armazenados no MongoDB.
- Notificações simuladas são enfileiradas com RabbitMQ para cada criação/edição de tarefa.
- A aplicação segue padrão Repository e Service Layer.
- Uso de Policies para controle de permissões (ex: dono do projeto).
