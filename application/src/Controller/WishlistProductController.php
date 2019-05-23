<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/wishlistProducts", requirements={"version": "\d+"})
 */
class WishlistProductController extends BaseController
{
    /**
     * @Route(path="", methods={"GET"})
     *
     * @return Response
     */
    public function listWishlistProducts(): Response
    {
        return JsonResponse::create([
            'products' => [],
        ]);
    }

    /**
     * @Route(path="", methods={"DELETE"})
     *
     * @return Response
     */
    public function resetWishlist(): Response
    {
        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}", methods={"POST"}, requirements={"id": "\d+"})
     *
     * @param Product $product
     *
     * @return Response
     */
    public function addProductToWishlist(Product $product): Response
    {
        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     *
     * @param Product $product
     *
     * @return Response
     */
    public function removeProductFromWishlist(Product $product): Response
    {
        return JsonResponse::create([]);
    }
}
