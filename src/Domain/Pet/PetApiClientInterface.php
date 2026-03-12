<?php
declare(strict_types=1);
namespace App\Domain\Pet;
use App\Domain\Pet\PetDto;

interface PetApiClientInterface
{
    public function createPet(PetDto $pet): PetDto;
    public function getPetById(int $id): PetDto;
    public function updatePet(PetDto $pet): PetDto;
    public function deletePet(int $id): void;
}