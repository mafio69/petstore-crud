<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Application\Pet\CreatePet\CreatePetCommand;
use App\Application\Pet\CreatePet\CreatePetHandler;
use App\Application\Pet\GetPetById\GetPetByIdCommand;
use App\Application\Pet\GetPetById\GetPetByIdHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pets', name: 'api_pets')]
class PetController extends AbstractController
{
    public function __construct(
        private readonly CreatePetHandler $createPetHandler,
        private readonly GetPetByIdHandler $getPetHandler
    ) {}

    #[Route('', name: '_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $command = new CreatePetCommand(
                name: $data['name'] ?? '',
                status: $data['status'] ?? 'available',
                photoUrls: $data['photoUrls'] ?? []
            );
            $pet = $this->createPetHandler->handle($command);
            return new JsonResponse($pet->toArray(), Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: '_get', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        try {
            $pet = $this->getPetHandler->handle(new GetPetByIdCommand($id));
            return new JsonResponse($pet->toArray());
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
