<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/auth', name: 'app_auth')]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthController.php',
        ]);
    }

    #[Route('/auth/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $encoder,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {

        $user =  $serializer->deserialize(
            $request->getContent(),
            User::class,
            'json'
        );

        $user->setPassword($encoder->hashPassword($user, $user->getPassword()));

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
           
            $errorsString = (string) $errors;
    
            return $this->json($errorsString, 400);
        }

        $em->persist($user);
        $em->flush();
        
        return $this->json([
            'message' => 'Compte crée'
        ]);
    }

    #[Route('/api/users', name: 'get_users', methods: ['GET'])]
    public function users(
        UserRepository $userRepository
    ): JsonResponse {

        return $this->json($userRepository->findAll());

    }
}
