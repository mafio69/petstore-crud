<?php

declare(strict_types=1);

namespace App\Domain\Pet;

interface PetApiClientInterface
{
    public function createPet(PetDto $pet): PetDto;
    public function getPetById(int $id): PetDto;
    public function updatePet(PetDto $pet): PetDto;
    public function deletePet(int $id): void;
    /** @return PetDto[] */
    public function findByStatus(string $status): array;
}
