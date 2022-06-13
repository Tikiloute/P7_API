<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products', methods:['GET'])]
    public function getProducts(
        ProductRepository $productRepository,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $products = $productRepository->findAll();
        $jsonContent = $serializer->serialize($products, 'json');

            return $this->json([
                'message' => 'Intégralité des produits',
                'products' => $jsonContent,
                'path' => 'src/Controller/ProductController.php',
            ]);
    }

    #[Route('/api/products/{id}', name: 'product', methods:['GET'])]
    public function getProduct(
        Product $product,
        SerializerInterface $serializer
    ): JsonResponse
    {

        $jsonContent = $serializer->serialize($product, 'json');
        dd($jsonContent);
        return $this->json([
            'products' => $jsonContent,
        ]);
    }

    #[Route('/api/products/add', name: 'addProduct', methods:['POST'])]
    public function addProduct(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $product =  $serializer->deserialize(
            $request->getContent(),
            Product::class,
            'json'
        );

        $errors = $validator->validate($product);

        if (count($errors) > 0) {
           
            $errorsString = (string) $errors;
    
            return $this->json($errorsString, 400);
        }

        $em->persist($product);
        $em->flush();

        return $this->json($product, Response::HTTP_CREATED);
    }


}
