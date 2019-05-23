<?php

namespace App\Controller;

use App\Entity\Category;
use App\Formatter\CategoryFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/categories", requirements={"version": "\d+"})
 */
class CategoryController extends BaseController
{
    /** @var CategoryFormatter */
    protected $formatter;

    /**
     * @Route(path="", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listCategories(Request $request): Response
    {
        // Caso 1: GET['category_code'] === 0
        // Caso 2: GET['category_code'] && GET['for_website']
        // Caso 3: else

        // GET['countProducts']

        return JsonResponse::create([]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"})
     *
     * @param Category $category
     *
     * @return Response
     */
    public function fetchCategory(Category $category): Response
    {
        return JsonResponse::create($this->formatter->format($category));
    }
}
