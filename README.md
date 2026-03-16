# Petstore CRUD — Symfony 7

Aplikacja integrująca się z [Swagger Petstore API](https://petstore.swagger.io/). Zapewnia pełne CRUD na zasobie `/pet` przez interfejs webowy (Twig) oraz REST API z dokumentacją Swagger UI.

## Stack

- PHP 8.2+, Symfony 7.1
- Guzzle 7 — HTTP client do Petstore API
- Twig + Bootstrap 5 — frontend
- PHPUnit 13 — testy

## Architektura

Hexagonal Architecture. Warstwy komunikują się przez interfejsy — `PetApiClientInterface` definiuje kontrakt, `PetstoreClient` go implementuje. Podmiana źródła danych nie wymaga zmian poza warstwą Infrastructure.

```
src/
├── Controller/
│   ├── Api/          — REST API (JSON responses)
│   └── Web/          — interfejs webowy (Twig)
├── Application/Pet/  — handlery use-case + Commands/Queries
│   ├── CreatePet/
│   ├── GetPetById/
│   ├── UpdatePet/
│   ├── DeletePet/
│   └── FindPetsByStatus/
├── Domain/Pet/       — PetDto, PetApiClientInterface
└── Infrastructure/   — PetstoreClient (Guzzle)
```

## Uruchomienie

```bash
cd petstore-crud
composer install
cp .env.example .env
php -S localhost:8000 -t public
```

Aplikacja nie wymaga bazy danych — wszystkie dane trafiają do zewnętrznego Petstore API.
Docker Compose dostarcza PostgreSQL jeśli potrzebny (Doctrine skonfigurowany, nieużywany w tym projekcie):

```bash
docker compose up -d
```

## Interfejs webowy

| Ścieżka | Opis |
|---|---|
| `GET /pets` | Lista petów z filtrem po statusie |
| `GET /pets/create` | Formularz dodawania |
| `GET /pets/{id}` | Podgląd |
| `GET /pets/{id}/edit` | Formularz edycji |

## REST API

Swagger UI: [`/api/docs`](http://localhost:8000/api/docs)

| Metoda | Ścieżka | Opis |
|---|---|---|
| `POST` | `/api/pets` | Utwórz peta |
| `GET` | `/api/pets/{id}` | Pobierz peta po ID |
| `PUT` | `/api/pets/{id}` | Zaktualizuj peta |
| `DELETE` | `/api/pets/{id}` | Usuń peta |

```bash
curl -X POST http://localhost:8000/api/pets \
  -H "Content-Type: application/json" \
  -d '{"name": "Rex", "status": "available", "photoUrls": []}'
```

## Testy

```bash
php bin/phpunit
```

## Uwaga o Petstore API

Petstore to publiczne demo API współdzielone przez wszystkich użytkowników. `GET /pet/findByStatus` może zwracać rekordy nieistniejące już pod `GET /pet/{id}`. Aplikacja obsługuje ten przypadek — widok korzysta wtedy z danych przekazanych z listy.

## Czas realizacji

~6 godzin
