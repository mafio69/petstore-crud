<?php

namespace App\Infrastructure;


use App\Domain\Pet\PetApiClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use App\Domain\Pet\PetDto;

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
            $response = $this->client->get("/pet/{$id}");  // ← było /v2/pet/{$id}
            return PetDto::fromArray(json_decode($response->getBody()->getContents(), true));
        } catch (RequestException $e) {
            throw new \RuntimeException("Pet not found (ID: {$id})");
        }
    }

    public function createPet(PetDto $pet): PetDto
    {
        try {
            $response = $this->client->post('/pet', [  // ← było /v2/pet
                'json' => $pet->toArray(),
                'headers' => ['Accept' => 'application/json']
            ]);
            return PetDto::fromArray(json_decode($response->getBody()->getContents(), true));
        } catch (RequestException $e) {
            throw new \RuntimeException('Failed to create pet: ' . $e->getMessage());
        }
    }

    public function updatePet(PetDto $pet): PetDto
    {
        try {
            $response = $this->client->put('/pet', [  // ← było /v2/pet
                'json' => $pet->toArray()
            ]);
            return PetDto::fromArray(json_decode($response->getBody()->getContents(), true));
        } catch (RequestException $e) {
            throw new \RuntimeException('Failed to update pet: ' . $e->getMessage());
        }
    }

    public function deletePet(int $id): void
    {
        try {
            $this->client->delete("/pet/{$id}");  // ← było /v2/pet/{$id}
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to delete pet (ID: {$id})");
        } catch (GuzzleException $e) {
        }
    }
}

