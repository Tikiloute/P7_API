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

class ProductController extends AbstractController
{
    #[Route('/product', name: 'products', methods:['GET'])]
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

    #[Route('/product/{id}', name: 'product', methods:['GET'])]
    public function getProduct(
        Product $product,
        SerializerInterface $serializer
    ): JsonResponse
    {

        $jsonContent = $serializer->serialize($product, 'json');

        return $this->json([
            'message' => 'Intégralité des produits',
            'products' => $jsonContent,
            'path' => 'src/Controller/ProductController.php',
        ]);
    }

    #[Route('/product/add', name: 'addProduct', methods:['POST'])]
    public function addProduct(
        Request $request,
        EntityManagerInterface $em,
        ProductRepository $productRepository
    ): JsonResponse
    {

        $data = json_decode($request->getContent());
        $name = $data->name;
        $description = $data->description;
        $price = $data->price;
        $stock = $data->stock;
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        $product->setStock($stock);
        $response = new Response();
        $productExist = $productRepository->findOneBy(['name' => $name]);

        if($response->getStatusCode() === 200 && $productExist === null){

            $em->persist($product);
            $em->flush();

            return $this->json([
                'message' => 'Produit crée avec le status :'.$response->getStatusCode(),
                'code' => $response->getStatusCode(),
                'path' => 'src/Controller/AuthController.php',
            ]);
        } else {
            return $this->json([
                'message' => 'Problème de création de produit  status :'.$response->getStatusCode(),
                'code' => $response->getStatusCode(),
                'path' => 'src/Controller/AuthController.php',
           ]);
        }
    }


}
