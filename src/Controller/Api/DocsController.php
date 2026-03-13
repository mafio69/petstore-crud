<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocsController extends AbstractController
{
    #[Route('/api/docs', name: 'api_docs', methods: ['GET'])]
    public function docs(): Response
    {
        return $this->render('api/docs.html.twig');
    }

    #[Route('/api/openapi.json', name: 'api_openapi', methods: ['GET'])]
    public function openapi(): JsonResponse
    {
        return new JsonResponse([
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Petstore CRUD API',
                'version' => '1.0.0',
                'description' => 'REST API for managing pets via Petstore integration.',
            ],
            'servers' => [
                ['url' => '/api', 'description' => 'Local server'],
            ],
            'paths' => [
                '/pets' => [
                    'post' => [
                        'summary' => 'Create a pet',
                        'operationId' => 'createPet',
                        'tags' => ['Pets'],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/PetInput'],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Pet created',
                                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Pet']]],
                            ],
                            '400' => ['description' => 'Validation error', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
                            '500' => ['description' => 'Server error', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
                        ],
                    ],
                ],
                '/pets/{id}' => [
                    'get' => [
                        'summary' => 'Get a pet by ID',
                        'operationId' => 'getPet',
                        'tags' => ['Pets'],
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Pet found',
                                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Pet']]],
                            ],
                            '404' => ['description' => 'Pet not found', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
                        ],
                    ],
                    'put' => [
                        'summary' => 'Update a pet',
                        'operationId' => 'updatePet',
                        'tags' => ['Pets'],
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                        ],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/PetInput'],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Pet updated',
                                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Pet']]],
                            ],
                            '400' => ['description' => 'Validation error', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
                            '500' => ['description' => 'Server error', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
                        ],
                    ],
                    'delete' => [
                        'summary' => 'Delete a pet',
                        'operationId' => 'deletePet',
                        'tags' => ['Pets'],
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                        ],
                        'responses' => [
                            '204' => ['description' => 'Pet deleted'],
                            '500' => ['description' => 'Server error', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
                        ],
                    ],
                ],
            ],
            'components' => [
                'schemas' => [
                    'Pet' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string', 'example' => 'Doggie'],
                            'status' => ['type' => 'string', 'enum' => ['available', 'pending', 'sold'], 'example' => 'available'],
                            'photoUrls' => ['type' => 'array', 'items' => ['type' => 'string']],
                            'tags' => ['type' => 'array', 'items' => ['type' => 'object']],
                        ],
                    ],
                    'PetInput' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string', 'example' => 'Doggie'],
                            'status' => ['type' => 'string', 'enum' => ['available', 'pending', 'sold'], 'example' => 'available'],
                            'photoUrls' => ['type' => 'array', 'items' => ['type' => 'string'], 'example' => []],
                            'tags' => ['type' => 'array', 'items' => ['type' => 'object'], 'example' => []],
                        ],
                    ],
                    'Error' => [
                        'type' => 'object',
                        'properties' => [
                            'error' => ['type' => 'string', 'example' => 'Error message'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
