<?php
declare(strict_types=1);
namespace App\Application\Pet\CreatePet;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreatePetCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public ?string $status = 'available',
        public array $photoUrls = [],
        public array $tags = []
    ) {}

    public function toPetDto(): \App\Domain\Pet\PetDto
    {
        return new \App\Domain\Pet\PetDto(
            id: 0,
            name: $this->name,
            status: $this->status,
            photoUrls: $this->photoUrls,
            tags: $this->tags
        );
    }
}
