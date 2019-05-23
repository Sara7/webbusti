<?php

namespace App\Controller;

use App\Formatter\MediaFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/media", requirements={"version": "\d+"})
 */
class MediaController extends BaseController
{
    /** @var MediaFormatter */
    protected $formatter;

    /**
     * @Route(path="", methods={"GET"})
     *
     * @return Response
     */
    public function listMedia(): Response
    {
        $media = [];

        $mediaToReturn = [];
        foreach ($media as $medium) {
            $mediaToReturn[] = $this->formatter->format($medium);
        }

        return JsonResponse::create([
            'media' => $mediaToReturn,
        ]);
    }
}
