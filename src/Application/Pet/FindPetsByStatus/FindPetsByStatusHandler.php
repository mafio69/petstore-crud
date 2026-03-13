<?php

declare(strict_types=1);

namespace App\Application\Pet\FindPetsByStatus;

use App\Domain\Pet\PetApiClientInterface;

class FindPetsByStatusHandler
{
    public function __construct(private readonly PetApiClientInterface $petApiClient) {}

    public function handle(FindPetsByStatusQuery $query): array
    {
        return $this->petApiClient->findByStatus($query->status);
    }
}
