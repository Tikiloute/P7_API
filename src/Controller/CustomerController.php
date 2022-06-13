<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerController extends AbstractController
{
    #[Route('/api/customers', name: 'get_customers', methods:'GET')]
    public function getCustomers(
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse
    {
        /** @var User $user */
        // force le type de la variable $user (getCustomers n'est pas reconnu par l'éditeur)
        $user = $this->getUser();
        $customers = $serializer->serialize($user->getCustomers(), 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        
        // For instance, return a Response with encoded Json        
        return new JsonResponse($customers, 200, [], true);
    }

    #[Route('/api/customers/{id}', name: 'get_customer')]
    public function getCustomer(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/createCustomer', name: 'create_customer')]
    public function createCustomer(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/deleteCustomer', name: 'delete_customer')]
    public function deleteCustomer(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
