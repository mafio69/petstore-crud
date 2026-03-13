<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Pet\PetApiClientInterface;
use App\Domain\Pet\PetDto;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class PetstoreClient implements PetApiClientInterface
{
    private Client $client;

    public function __construct(string $baseUrl)
    {
        $this->client = new Client(['base_uri' => $baseUrl]);
    }

    public function getPetById(int $id): PetDto
    {
        try {
            $response = $this->client->get("/v2/pet/{$id}");
            return PetDto::fromArray(json_decode($response->getBody()->getContents(), true));
        } catch (RequestException $e) {
            throw new \RuntimeException("Pet not found (ID: {$id})");
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to fetch pet (ID: {$id}): " . $e->getMessage());
        }
    }

    public function createPet(PetDto $pet): PetDto
    {
        try {
            $response = $this->client->post('/v2/pet', [
                'json' => $pet->toArray(),
                'headers' => ['Accept' => 'application/json'],
            ]);
            return PetDto::fromArray(json_decode($response->getBody()->getContents(), true));
        } catch (RequestException $e) {
            throw new \RuntimeException('Failed to create pet: ' . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to create pet: ' . $e->getMessage());
        }
    }

    public function updatePet(PetDto $pet): PetDto
    {
        try {
            $response = $this->client->put('/v2/pet', [
                'json' => $pet->toArray(),
                'headers' => ['Accept' => 'application/json'],
            ]);
            return PetDto::fromArray(json_decode($response->getBody()->getContents(), true));
        } catch (RequestException $e) {
            throw new \RuntimeException('Failed to update pet: ' . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to update pet: ' . $e->getMessage());
        }
    }

    public function deletePet(int $id): void
    {
        try {
            $this->client->delete("/v2/pet/{$id}");
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to delete pet (ID: {$id}): " . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to delete pet (ID: {$id}): " . $e->getMessage());
        }
    }

    public function findByStatus(string $status): array
    {
        try {
            $response = $this->client->get('/v2/pet/findByStatus', [
                'query' => ['status' => $status],
                'headers' => ['Accept' => 'application/json'],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return array_map(fn(array $item) => PetDto::fromArray($item), $data);
        } catch (RequestException $e) {
            throw new \RuntimeException('Failed to fetch pets: ' . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Failed to fetch pets: ' . $e->getMessage());
        }
    }
}
