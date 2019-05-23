<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Formatter\FeatureFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/features", requirements={"version": "\d+"})
 */
class FeatureController extends BaseController
{
    /** @var FeatureFormatter $formatter */
    protected $formatter;

    /**
     * FeatureController constructor.
     *
     * @param FeatureFormatter $formatter
     */
    public function __construct(FeatureFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @Route(path="", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listFeatures(Request $request): Response
    {
        // Caso 1: GET['category_id'] === 0
        //      Sottocaso: GET['restrict']
        //      Sottocaso: else
        // Caso 2: GET['product_code']

        return JsonResponse::create([
            'features' => [],
        ]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"})
     *
     * @param Feature $feature
     *
     * @return Response
     */
    public function fetchFeature(Feature $feature): Response
    {
        return JsonResponse::create($this->formatter->format($feature));
    }
}
