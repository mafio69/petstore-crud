<?php

declare(strict_types=1);

namespace App\Application\Pet\DeletePet;

use App\Domain\Pet\PetApiClientInterface;

class DeletePetHandler
{
    public function __construct(private readonly PetApiClientInterface $petApiClient) {}

    public function handle(DeletePetCommand $command): void
    {
        $this->petApiClient->deletePet($command->id);
    }
}
