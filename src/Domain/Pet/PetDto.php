<?php

namespace App\Domain\Pet;

final class PetDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $status = null,
        public readonly array $photoUrls = [],
        public readonly array $tags = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            status: $data['status'] ?? null,
            photoUrls: $data['photoUrls'] ?? [],
            tags: $data['tags'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'status'    => $this->status,
            'photoUrls' => $this->photoUrls,
            'tags'      => $this->tags,
        ];
    }
}