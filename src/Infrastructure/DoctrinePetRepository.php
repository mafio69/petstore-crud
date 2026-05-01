<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Pet\PetApiClientInterface;
use App\Domain\Pet\PetDto;
use App\Entity\Pet;
use Doctrine\ORM\EntityManagerInterface;

class DoctrinePetRepository implements PetApiClientInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function createPet(PetDto $dto): PetDto
    {
        $pet = new Pet();
        $pet->setName($dto->name);
        $pet->setStatus($dto->status);
        $pet->setPhotoUrls($dto->photoUrls);
        $pet->setTags($dto->tags);

        $this->em->persist($pet);
        $this->em->flush();

        return $this->toDto($pet);
    }

    public function getPetById(int $id): PetDto
    {
        $pet = $this->em->find(Pet::class, $id);

        if ($pet === null) {
            throw new \RuntimeException("Pet not found (ID: {$id})");
        }

        return $this->toDto($pet);
    }

    public function updatePet(PetDto $dto): PetDto
    {
        $pet = $this->em->find(Pet::class, $dto->id);

        if ($pet === null) {
            throw new \RuntimeException("Pet not found (ID: {$dto->id})");
        }

        $pet->setName($dto->name);
        $pet->setStatus($dto->status);
        $pet->setPhotoUrls($dto->photoUrls);
        $pet->setTags($dto->tags);

        $this->em->flush();

        return $this->toDto($pet);
    }

    public function deletePet(int $id): void
    {
        $pet = $this->em->find(Pet::class, $id);

        if ($pet === null) {
            throw new \RuntimeException("Pet not found (ID: {$id})");
        }

        $this->em->remove($pet);
        $this->em->flush();
    }

    public function findByStatus(string $status): array
    {
        $pets = $this->em->getRepository(Pet::class)->findBy(['status' => $status]);

        return array_map($this->toDto(...), $pets);
    }

    private function toDto(Pet $pet): PetDto
    {
        return new PetDto(
            id: $pet->getId(),
            name: $pet->getName(),
            status: $pet->getStatus(),
            photoUrls: $pet->getPhotoUrls(),
            tags: $pet->getTags(),
        );
    }
}
