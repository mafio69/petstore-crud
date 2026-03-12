<?php

declare(strict_types=1);

namespace App\Application\Pet\GetPetById;

use App\Domain\Pet\PetApiClientInterface;
use App\Domain\Pet\PetDto;

readonly class GetPetByIdHandler
{
    public function __construct(private PetApiClientInterface $petApiClient) {}

    public function handle(GetPetByIdCommand $command): PetDto
    {
        return $this->petApiClient->getPetById($command->id);
    }
}
