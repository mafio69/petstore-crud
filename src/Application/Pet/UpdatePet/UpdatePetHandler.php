<?php

declare(strict_types=1);

namespace App\Application\Pet\UpdatePet;

use App\Domain\Pet\PetApiClientInterface;
use App\Domain\Pet\PetDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdatePetHandler
{
    public function __construct(
        private readonly PetApiClientInterface $petApiClient,
        private readonly ValidatorInterface $validator
    ) {}

    public function handle(UpdatePetCommand $command): PetDto
    {
        $violations = $this->validator->validate($command);
        if (count($violations) > 0) {
            throw new \InvalidArgumentException((string) $violations);
        }

        return $this->petApiClient->updatePet($command->toPetDto());
    }
}
