<?php

declare(strict_types=1);

namespace App\Application\Pet\DeletePet;

final readonly class DeletePetCommand
{
    public function __construct(public int $id) {}
}
