<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Application\Pet\CreatePet\CreatePetCommand;
use App\Application\Pet\CreatePet\CreatePetHandler;
use App\Application\Pet\DeletePet\DeletePetCommand;
use App\Application\Pet\DeletePet\DeletePetHandler;
use App\Application\Pet\FindPetsByStatus\FindPetsByStatusHandler;
use App\Application\Pet\FindPetsByStatus\FindPetsByStatusQuery;
use App\Application\Pet\GetPetById\GetPetByIdCommand;
use App\Application\Pet\GetPetById\GetPetByIdHandler;
use App\Application\Pet\UpdatePet\UpdatePetCommand;
use App\Application\Pet\UpdatePet\UpdatePetHandler;
use App\Domain\Pet\PetDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PetController extends AbstractController
{
    public function __construct(
        private readonly CreatePetHandler $createPetHandler,
        private readonly GetPetByIdHandler $getPetByIdHandler,
        private readonly UpdatePetHandler $updatePetHandler,
        private readonly DeletePetHandler $deletePetHandler,
        private readonly FindPetsByStatusHandler $findPetsByStatusHandler
    ) {}

    #[Route('/', name: 'home')]
    public function home(): RedirectResponse
    {
        return $this->redirectToRoute('web_pets_index');
    }

    #[Route('/pets', name: 'web_pets_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $status = $request->query->get('status', 'available');
        $pets = [];
        $error = null;

        try {
            $pets = $this->findPetsByStatusHandler->handle(new FindPetsByStatusQuery($status));
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return $this->render('pet/index.html.twig', [
            'pets' => $pets,
            'status' => $status,
            'error' => $error,
        ]);
    }

    #[Route('/pets/create', name: 'web_pets_create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('pet/create.html.twig');
    }

    #[Route('/pets/create', name: 'web_pets_create_post', methods: ['POST'])]
    public function createPost(Request $request): Response
    {
        try {
            $command = new CreatePetCommand(
                name: $request->request->get('name', ''),
                status: $request->request->get('status', 'available'),
                photoUrls: $this->extractPhotoUrls($request->request->get('photoUrl', '')),
            );
            $pet = $this->createPetHandler->handle($command);
            $this->addFlash('success', "Pet \"{$pet->name}\" created successfully (ID: {$pet->id}).");
            return $this->redirectToRoute('web_pets_show', ['id' => $pet->id]);
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->render('pet/create.html.twig', ['data' => $request->request->all()]);
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Failed to create pet: ' . $e->getMessage());
            return $this->render('pet/create.html.twig', ['data' => $request->request->all()]);
        }
    }

    #[Route('/pets/{id}', name: 'web_pets_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, Request $request): Response
    {
        $pet = $this->fetchPetOrBuildFromRequest($id, $request);
        return $this->render('pet/show.html.twig', ['pet' => $pet]);
    }

    #[Route('/pets/{id}/edit', name: 'web_pets_edit', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $pet = $this->fetchPetOrBuildFromRequest($id, $request);
        return $this->render('pet/edit.html.twig', ['pet' => $pet]);
    }

    #[Route('/pets/{id}/edit', name: 'web_pets_edit_post', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function editPost(int $id, Request $request): Response
    {
        try {
            $command = new UpdatePetCommand(
                id: $id,
                name: $request->request->get('name', ''),
                status: $request->request->get('status', 'available'),
                photoUrls: $this->extractPhotoUrls($request->request->get('photoUrl', '')),
            );
            $pet = $this->updatePetHandler->handle($command);
            $this->addFlash('success', "Pet \"{$pet->name}\" updated successfully.");
            return $this->redirectToRoute('web_pets_show', ['id' => $pet->id]);
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
            try {
                $pet = $this->getPetByIdHandler->handle(new GetPetByIdCommand($id));
                return $this->render('pet/edit.html.twig', ['pet' => $pet]);
            } catch (\Throwable) {
                return $this->redirectToRoute('web_pets_index');
            }
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Failed to update pet: ' . $e->getMessage());
            return $this->redirectToRoute('web_pets_show', ['id' => $id]);
        }
    }

    #[Route('/pets/{id}/delete', name: 'web_pets_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(int $id, Request $request): RedirectResponse
    {
        try {
            $this->deletePetHandler->handle(new DeletePetCommand($id));
            $this->addFlash('success', "Pet with ID {$id} deleted successfully.");
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Failed to delete pet: ' . $e->getMessage());
        }

        $status = $request->request->get('status', 'available');
        return $this->redirectToRoute('web_pets_index', ['status' => $status]);
    }

    private function fetchPetOrBuildFromRequest(int $id, Request $request): PetDto
    {
        try {
            return $this->getPetByIdHandler->handle(new GetPetByIdCommand($id));
        } catch (\Throwable) {
            return new PetDto(
                id: $id,
                name: $request->query->get('name', ''),
                status: $request->query->get('status', 'available'),
                photoUrls: $this->extractPhotoUrls($request->query->get('photoUrl', '')),
            );
        }
    }

    private function extractPhotoUrls(string $photoUrl): array
    {
        return array_values(array_filter([trim($photoUrl)]));
    }
}
