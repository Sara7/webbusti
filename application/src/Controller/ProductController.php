<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/products", requirements={"version": "\d+"})
 */
class ProductController extends BaseController
{
    /**
     * @Route(path="", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listProducts(Request $request): Response
    {
        $products = [];

        // Caso 1: GET['product_category']
        // Caso 2: else

        return JsonResponse::create([
            'products' => $products,
        ]);
    }

    /**
     * @Route(path="", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createProduct(Request $request): Response
    {
        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, requirements={"id": "\d+"})
     *
     * @param Product $product
     *
     * @return Response
     */
    public function fetchProduct(Product $product): Response
    {
        return JsonResponse::create([
            'id' => $product->getId(),
        ]);
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     *
     * @param Product $product
     *
     * @return Response
     */
    public function removeProduct(Product $product): Response
    {
        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}/featured", methods={"POST"}, requirements={"id": "\d+"})
     *
     * @param Product $product
     *
     * @return Response
     */
    public function setFeatured(Product $product): Response
    {
        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}/featured", methods={"DELETE"}, requirements={"id": "\d+"})
     *
     * @param Product $product
     *
     * @return Response
     */
    public function unsetFeatured(Product $product): Response
    {
        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}/features", methods={"POST"}, requirements={"id": "\d+"})
     *
     * @param Product $product
     *
     * @return Response
     */
    public function setFeatures(Product $product): Response
    {
        // Set features

        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}/featured", methods={"GET"}, requirements={"id": "\d+"})
     *
     * @param Product $product
     *
     * @return Response
     */
    public function fetchFeatured(Product $product): Response
    {
        return JsonResponse::create([
            'featured' => [],
        ]);
    }
}
