<?php

namespace App\Controller;

use App\Entity\Province;
use App\Formatter\ProvinceFormatter;
use App\Repository\CountryRepository;
use App\Repository\ProvinceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/provinces", requirements={"version": "\d+"})
 */
class ProvinceController extends BaseController
{
    /** @var ProvinceRepository */
    protected $repository;

    /** @var ProvinceFormatter */
    protected $formatter;

    /**
     * ProvinceController constructor.
     *
     * @param ProvinceRepository $repository
     * @param ProvinceFormatter  $formatter
     */
    public function __construct(ProvinceRepository $repository, ProvinceFormatter $formatter)
    {
        $this->repository = $repository;
        $this->formatter  = $formatter;
    }

    /**
     * @Route(path="", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listProvinces(Request $request): Response
    {
        $all = $request->query->getBoolean('all', false);

        $provincesPage = null;
        if ($all) {
            $provinces = $this->repository->getAllProvinces();
        } else {
            $perPage = $request->query->getInt('perPage', 30);
            $page = $request->query->getInt('page', 1);

            $provincesPage = $this->repository->getProvinces($perPage, $page);

            $provinces = $provincesPage->getResults();
        }

        $provincesToReturn = [];
        foreach ($provinces as $province) {
            $provincesToReturn[] = $this->formatter->format($province);
        }

        $response = JsonResponse::create([
            'provinces' => $provincesToReturn,
        ]);

        if ($all) {
            return $response;
        }

        return $this->setResponseHeadersForPage($response, $provincesPage);
    }

    /**
     * @Route(path="", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request           $request
     * @param CountryRepository $countryRepository
     *
     * @return Response
     */
    public function createProvince(Request $request, CountryRepository $countryRepository): Response
    {
        $province = new Province();

        $province->setName($request->request->get('province_name'));
        $province->setCode($request->request->get('province_code'));

        $countryId = $request->request->getInt('province_country_id');
        $country = $countryRepository->fetch($countryId);
        $province->setCountry($country);

        $this->repository->save($province);

        return JsonResponse::create($this->formatter->format($province));
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, requirements={"id": "\d+"})
     *
     * @param Province $province
     *
     * @return Response
     */
    public function fetchProvince(Province $province): Response
    {
        return JsonResponse::create($this->formatter->format($province));
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request           $request
     * @param Province          $province
     * @param CountryRepository $countryRepository
     *
     * @return Response
     */
    public function editProvince(Request $request, Province $province, CountryRepository $countryRepository): Response
    {
        $province->setName($request->request->get('province_name'));
        $province->setCode($request->request->get('province_code'));

        $countryId = $request->request->getInt('province_country_id');
        $country = $countryRepository->fetch($countryId);
        $province->setCountry($country);

        $this->repository->save($province);

        return JsonResponse::create($this->formatter->format($province));
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Province $province
     *
     * @return Response
     */
    public function removeProvince(Province $province): Response
    {
        $this->repository->delete($province);

        return JsonResponse::create([]);
    }
}
