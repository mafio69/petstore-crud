<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure;

use App\Domain\Pet\PetDto;
use App\Infrastructure\DoctrinePetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrinePetRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private DoctrinePetRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->repository = static::getContainer()->get(DoctrinePetRepository::class);

        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->rollBack();

        parent::tearDown();
    }

    public function testCreatePetPersistsAndReturnsWithId(): void
    {
        $dto = new PetDto(
            id: 0,
            name: 'Burek',
            status: 'available',
            photoUrls: ['http://example.com/burek.jpg'],
            tags: [['id' => 1, 'name' => 'dog']],
        );

        $created = $this->repository->createPet($dto);

        $this->assertGreaterThan(0, $created->id);
        $this->assertSame('Burek', $created->name);
        $this->assertSame('available', $created->status);
        $this->assertSame(['http://example.com/burek.jpg'], $created->photoUrls);
        $this->assertSame([['id' => 1, 'name' => 'dog']], $created->tags);
    }

    public function testGetPetByIdReturnsCorrectPet(): void
    {
        $dto = new PetDto(id: 0, name: 'Filemon', status: 'pending');

        $created = $this->repository->createPet($dto);
        $this->em->clear();

        $fetched = $this->repository->getPetById($created->id);

        $this->assertSame($created->id, $fetched->id);
        $this->assertSame('Filemon', $fetched->name);
        $this->assertSame('pending', $fetched->status);
    }

    public function testGetPetByIdThrowsWhenNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Pet not found/');

        $this->repository->getPetById(999999);
    }

    public function testCreatePetWithMinimalData(): void
    {
        $dto = new PetDto(id: 0, name: 'Azor');

        $created = $this->repository->createPet($dto);

        $this->assertGreaterThan(0, $created->id);
        $this->assertSame('Azor', $created->name);
        $this->assertNull($created->status);
        $this->assertSame([], $created->photoUrls);
        $this->assertSame([], $created->tags);
    }
}
