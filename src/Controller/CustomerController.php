<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class CustomerController extends AbstractController
{
    #[Route('/api/customers', name: 'get_customers', methods:'GET')]
    public function getCustomers(
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

    #[Route('/api/customers/{id}', name: 'get_customer', methods: ['GET'])]
    public function getCustomer(
        CustomerRepository $customerRepository,
        SerializerInterface $serializer,
        int $id
    ): JsonResponse
    {
        /** @var User $user */
        // force le type de la variable $user (getCustomers n'est pas reconnu par l'éditeur)
        $user = $this->getUser();

        $customer = $customerRepository->findOneBy(['id' => $id, 'user' => $user]);

        if ($customer === null){
            return new JsonResponse('customer non trouvé', Response::HTTP_NOT_FOUND);
        }
        
        // For instance, return a Response with encoded Json        
        return new JsonResponse($serializer->serialize($customer, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['customers'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]), 200, [], true);
        
    }

    #[Route('/api/createCustomer', name: 'create_customer', methods: ['POST'])]
    public function createCustomer( 
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse
    {

        $customer =  $serializer->deserialize(
            $request->getContent(),
            Customer::class,
            JsonEncoder::FORMAT,
            [
                
            ]
        );

        $errors = $validator->validate($customer);

        if (count($errors) > 0) {
        
            $errorsString = (string) $errors;

            return $this->json($errorsString, 400);
        }
        
        $customer->setUser($this->getUser());

        $em->persist($customer);
        $em->flush();

        return new JsonResponse($serializer->serialize($customer, JsonEncoder::FORMAT, [
            //ici on ignore l'attribut 'user' pour ne pas avoir la liste des users en réponse
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]), Response::HTTP_CREATED, [], true);
    }



    
    #[Route('/api/deleteCustomer/{id}', name: 'delete_customer', methods: ['DELETE'])]
    public function deleteCustomer(
        CustomerRepository $customerRepository,
        int $id,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $user = $this->getUser();
        $customer = $customerRepository->findOneBy(['id'=> $id, 'user' => $user ]);

        if ($customer !== null){
            $customerRepository->remove($customer, true);
        } else {
            return new JsonResponse('customer non trouvé', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($serializer->serialize($customer, JsonEncoder::FORMAT, [
            //ici on ignore l'attribut 'user' pour ne pas avoir la liste des users en réponse
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]), Response::HTTP_OK, [], true);
    }
}
