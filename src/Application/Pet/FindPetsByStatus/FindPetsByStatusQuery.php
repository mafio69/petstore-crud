<?php

declare(strict_types=1);

namespace App\Application\Pet\FindPetsByStatus;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class FindPetsByStatusQuery
{
    public function __construct(
        #[Assert\Choice(['available', 'pending', 'sold'])]
        public string $status = 'available'
    ) {}
}
