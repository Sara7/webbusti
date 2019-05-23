<?php

namespace App\Controller;

use App\Entity\Country;
use App\Formatter\CountryFormatter;
use App\Repository\CountryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/countries", requirements={"version": "\d+"})
 */
class CountryController extends BaseController
{
    /** @var CountryRepository */
    protected $repository;

    /** @var CountryFormatter */
    protected $formatter;

    /**
     * CountryController constructor.
     *
     * @param CountryRepository $repository
     * @param CountryFormatter  $formatter
     */
    public function __construct(CountryRepository $repository, CountryFormatter $formatter)
    {
        $this->repository = $repository;
        $this->formatter  = $formatter;
    }

    /**
     * @Route(path="", methods={"GET"})
     * @Security("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
     *
     * @return Response
     */
    public function listCountries(): Response
    {
        $countries = $this->repository->getCountrys();

        $countriesToReturn = [];
        foreach ($countries as $country) {
            $countriesToReturn[] = $this->formatter->format($country);
        }

        return JsonResponse::create([
            'countries' => $countriesToReturn,
        ]);
    }

    /**
     * @Route(path="", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createCountry(Request $request): Response
    {
        $country = new Country();
        $country->setName($request->request->get('name'));
        $country->setCode($request->request->get('code'));

        $this->repository->save($country);

        return JsonResponse::create($this->formatter->format($country));
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Country $country
     *
     * @return Response
     */
    public function fetchCountry(Country $country): Response
    {
        return JsonResponse::create($this->formatter->format($country));
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request           $request
     * @param Country $country
     *
     * @return Response
     */
    public function editCountry(Request $request, Country $country): Response
    {
        $country->setName($request->request->get('name'));
        $country->setCode($request->request->get('code'));

        $this->repository->save($country);

        return JsonResponse::create($this->formatter->format($country));
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Country $country
     *
     * @return Response
     */
    public function removeCountry(Country $country): Response
    {
        $this->repository->delete($country);

        return JsonResponse::create([]);
    }
}
