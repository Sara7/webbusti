<?php

namespace App\Controller;

use App\Entity\Item;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/cartItems", requirements={"version": "\d+"})
 */
class CartItemController extends BaseController
{
    /**
     * @Route(path="", methods={"GET"})
     *
     * @return Response
     */
    public function listCartItems(): Response
    {
        return JsonResponse::create([
            'items' => [],
            'total' => 0,
        ]);
    }

    /**
     * @Route(path="", methods={"DELETE"})
     *
     * @return Response
     */
    public function resetCart(): Response
    {
        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}", methods={"POST"}, requirements={"id": "\d+"})
     *
     * @param Item $item
     *
     * @return Response
     */
    public function addItemToCart(Item $item): Response
    {
        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     *
     * @param Item $item
     *
     * @return Response
     */
    public function removeItemFromCart(Item $item): Response
    {
        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}/quantity/{quantity}", methods={"POST"}, requirements={"id": "\d+", "quantity": "\d+"})
     *
     * @param Item $item
     * @param int  $quantity
     *
     * @return Response
     */
    public function updateCartItem(Item $item, int $quantity): Response
    {
        return JsonResponse::create([]);
    }
}
