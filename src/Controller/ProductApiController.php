<?php

namespace App\Controller;


use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Serializer\ProductSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductApiController extends AbstractController
{

    public function __construct(private ProductRepository $productRepository,
                                private ProductSerializer $serializer,
                                private ValidatorInterface $validator,
                                private EntityManagerInterface $entityManager)
    {

    }


    /*
     * Metoda pro získání všech produktů
     */
    #[Route('/api/v1/product', name: 'api_v1_allproducts', methods: ['GET'])]
    public function getAll(): JsonResponse
    {

        $products = $this->productRepository->findAll();

        if (empty($products))
            return new JsonResponse(['message' => 'Nebyl nalezen žádný produkt'], 404);


        $data = $this->serializer->serialize($products, 'json');

        return new JsonResponse($data, 200, [], true);

    }

    #[Route('/api/v1/product', name: 'api_v1_product_post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {

        $productDTO = new ProductDTO();

        /*
         * Deserializuji požadavek na DTO objekt
         */
        $this->serializer->deserialize($request->getContent(), ProductDTO::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $productDTO]);

        /*
         * Validuji DTO objekt vůči constraintům definovaným v DTO objektu
         * Pokud existuje alespon jedna chyba vracím chybovou odpověď
         */
        if (($validationResult = $this->isValid($productDTO)) !== true)
            return new JsonResponse($validationResult, Response::HTTP_BAD_REQUEST);

        /*
         * Vytvořím entitu Produktu z DTO objektu
         */
        $product = Product::createFromDTO($productDTO);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse($this->serializer->serialize($product, 'json'), 201, [], true);

    }


    /*
     * Metoda pro získání produktu podle ID
     */
    #[Route('/api/v1/product/{id}', name: 'api_v1_product_get', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {

        $product = $this->productRepository->find($id);

        if (null === $product)
            return new JsonResponse(['message' => 'Produkt nebyl nalzen'], 404);

        $data = $this->serializer->serialize($product, 'json');

        return new JsonResponse($data, 200, [], true);

    }


    /*
     * Metoda pro aktualizaci produktu
     */
    /*
     * @Param int $id
     * @Param Request $request
     * @Return JsonResponse
     */
    #[Route('/api/v1/product/{id}', name: 'api_v1_product_put_id', methods: ['PUT'])]
    #[Route('/api/v1/product', name: 'api_v1_product_put', methods: ['PUT'])]
    public function put(Request $request): JsonResponse
    {

        /*
         * Id je buď v URL nebo v těle požadavku
         */
        $id = $request->get('id') ?? $request->getPayload()->get('id');

        if (null === $id)
            return new JsonResponse(['message' => 'ID produktu nebylo nalezeno v požadavku'], Response::HTTP_BAD_REQUEST);

        $productDTO = new ProductDTO();

        /*
         * Získám produkt podle ID
         */
        /* @var Product $product */
        $product = $this->productRepository->find($id);
        if (null === $product)
            return new JsonResponse(['message' => 'Produkt nebyl nalzen'], 404);

        /*
         * Deserializuji požadavek na DTO objekt
         * Jelikož je to PUT metoda, tak očekávám všechny atributy produktu
         */
        $this->serializer->deserialize($request->getContent(), ProductDTO::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $productDTO]);

        /*
         * Validuji DTO objekt vůči constraintům definovaným v DTO objektu
         * Pokud existuje alespon jedna chyba vracím chybovou odpověď
         */
        if (($validationResult = $this->isValid($productDTO)) !== true)
            return new JsonResponse($validationResult, Response::HTTP_BAD_REQUEST);

        /*
         * Aktualizuji entitu produktu z DTO objektu
         */
        $product->changeFromDTO($productDTO);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse($this->serializer->serialize($product, 'json'), Response::HTTP_ACCEPTED, [], true);

    }



    /*
     * Metoda pro odstranění produktu
     */
    /*
     * @Param int $id
     * @Return JsonResponse
     */
    #[Route('/api/v1/product/{id}', name: 'api_v1_product_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {

        $product = $this->productRepository->find($id);

        if (null === $product)
            return new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Product deleted'], Response::HTTP_OK); // HTTP_NO_CONTENT

    }


    /*
     * Metoda pro validaci DTO objektu
     * Vrátí true pokud je validní, jinak pole s chybovými zprávami
     */
    /*
     * @Param ProductDTO $productDTO
     * @Return true|array
     */
    private function isValid(ProductDTO $productDTO): true|array
    {

        $violations = $this->validator->validate($productDTO);

        if ($violations->count() > 0) {
            $response = [];

            foreach ($violations AS $violation) {
                $response[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return $response;
        }

        return true;

    }

}