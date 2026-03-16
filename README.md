# Petstore CRUD - Symfony 7

Application that integrates with [Swagger Petstore API](https://petstore.swagger.io/). Provides full CRUD on the `/pet` resource via web interface (Twig) and REST API with Swagger UI documentation.

## Stack

- PHP 8.2+, Symfony 7.1
- Guzzle 7 - HTTP client for Petstore API
- Twig + Bootstrap 5 - frontend
- PHPUnit 13 - tests

## Architecture

Hexagonal Architecture. The layers communicate through interfaces - `PetApiClientInterface` defines the contract, `PetstoreClient` implements it. Replacing the data source does not require changes outside the Infrastructure layer.

```
src/
├── Controller/
│ ├── Api/ - REST API (JSON responses)
│ └── Web/ - web interface (Twig)
├── Application/Pet/ — use-case handlers + Commands/Queries
│ ├── CreatePet/
│ ├── GetPetById/
│ ├── UpdatePet/
│ ├── DeletePet/
│ └── FindPetsByStatus/
├── Domain/Pet/ — PetDto, PetApiClientInterface
└── Infrastructure/ — PetstoreClient (Guzzle)
```

## Startup

```bash
cd petstore-crud
composer install
cp .env.example .env
php -S localhost:8000 -t public
```

The application does not require a database - all data goes to the external Petstore API.
Docker Compose provides PostgreSQL if needed (Doctrine configured, not used in this project):

```bash
docker compose up -d
```

## Web interface

| Path | Description |
|---|---|
| `GET /pets` | List of pets filtered by status |
| `GET /pets/create` | Add form |
| `GET /pets/{id}` | Preview |
| `GET /pets/{id}/edit` | Edit form |

## REST API

Swagger UI: [`/api/docs`](http://localhost:8000/api/docs)

| Method | Path | Description |
|---|---|---|
| `POST` | `/api/pets` | Create a pet |
| `GET` | `/api/pets/{id}` | Download pet by ID |
| `PUT` | `/api/pets/{id}` | Update pet |
| `DELETE` | `/api/pets/{id}` | Delete pet |

```bash
curl -X POST http://localhost:8000/api/pets\
  -H "Content-Type: application/json" \
  -d '{"name": "Rex", "status": "available", "photoUrls": []}'
```

## Tests

```bash
php bin/phpunit
```

## Note about Petstore API

Petstore is a public demo API shared by all users. `GET /pet/findByStatus` can return records that no longer exist under `GET /pet/{id}`. The application supports this case - the view then uses the data passed from the list.

## Lead time

~6 hours
