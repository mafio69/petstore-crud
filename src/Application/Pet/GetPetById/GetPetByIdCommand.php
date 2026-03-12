<?php

declare(strict_types=1);

namespace App\Application\Pet\GetPetById;

final readonly class GetPetByIdCommand
{
    public function __construct(public int $id) {}
}
