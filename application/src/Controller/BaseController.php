<?php

namespace App\Controller;

use App\Controller\Traits\VersionableTrait;
use App\Pager\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    use VersionableTrait;

    /**
     * @param Response $response
     * @param Page     $page
     *
     * @return Response
     */
    public function setResponseHeadersForPage(Response $response, Page $page): Response
    {
        $response->headers->set('X-Pager-Page', (string) $page->getCurrentPage());
        $response->headers->set('X-Pager-Total-Pages', (string) $page->getTotalPages());
        $response->headers->set('X-Pager-Total-Items', (string) $page->getTotalItems());
        $response->headers->set('X-Pager-Per-Page', (string) $page->getPerPage());

        return $response;
    }
}
