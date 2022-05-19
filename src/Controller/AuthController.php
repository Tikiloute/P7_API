<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/auth/register', name: 'register', methods:['POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $encoder,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): JsonResponse
    {

        $data = json_decode($request->getContent());
        $password = $data->password;
        $login = $data->login;
        $user = new User();
        $user->setPassword($encoder->hashPassword($user, $password));
        $user->setLogin($login);
        $user->setRoles(['ROLE_USER']);
        $response = new Response();
        $userExist = $userRepository->findOneBy(['login' => $login]);

        if($response->getStatusCode() === 200 && $userExist === null){
            $em->persist($user);
            $em->flush();
            return $this->json([
                'message' => 'Compte crée avec le status :'.$response->getStatusCode(),
                'code' => $response->getStatusCode(),
                'path' => 'src/Controller/AuthController.php',
            ]);
        } else {
            return $this->json([
                'message' => 'Erreur le code status est '.$response->getStatusCode().' le compte ne
                peut pas être crée',
                'path' => 'src/Controller/AuthController.php',
            ]);
        }
        
    }
}
