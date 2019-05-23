<?php

namespace App\Controller;

use App\Entity\Municipality;
use App\Formatter\MunicipalityFormatter;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/municipalities", requirements={"version": "\d+"})
 */
class MunicipalityController extends BaseController
{
    /** @var MunicipalityRepository */
    protected $repository;

    /** @var MunicipalityFormatter */
    protected $formatter;

    /**
     * MunicipalityController constructor.
     *
     * @param MunicipalityRepository $repository
     * @param MunicipalityFormatter  $formatter
     */
    public function __construct(MunicipalityRepository $repository, MunicipalityFormatter $formatter)
    {
        $this->repository = $repository;
        $this->formatter  = $formatter;
    }

    /**
     * @Route(path="", methods={"GET"})
     *
     * @param Request            $request
     * @param ProvinceRepository $provinceRepository
     *
     * @return Response
     */
    public function listMunicipalities(Request $request, ProvinceRepository $provinceRepository): Response
    {
        $all = $request->query->getBoolean('all', false);
        $provinceId = $request->query->getInt('province');
        $province = $provinceRepository->fetch($provinceId);

        $municipalitiesPage = null;
        if ($all) {
            $municipalities = $province
                ? $this->repository->getAllMunicipalitiesByProvince($province)
                : $this->repository->getAllMunicipalities()
            ;
        } else {
            $perPage = $request->query->getInt('perPage', 30);
            $page = $request->query->getInt('page', 1);

            $municipalitiesPage = $province
                ? $this->repository->getMunicipalitiesByProvince($province, $perPage, $page)
                : $this->repository->getMunicipalities($perPage, $page)
            ;

            $municipalities = $municipalitiesPage->getResults();
        }

        $municipalitiesToReturn = [];
        foreach ($municipalities as $municipality) {
            $municipalitiesToReturn[] = $this->formatter->format($municipality);
        }

        $response = JsonResponse::create([
            'municipalities' => $municipalitiesToReturn,
        ]);

        if ($all) {
            return $response;
        }

        return $this->setResponseHeadersForPage($response, $municipalitiesPage);
    }

    /**
     * @Route(path="", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request            $request
     * @param ProvinceRepository $provinceRepository
     *
     * @return Response
     */
    public function createMunicipality(Request $request, ProvinceRepository $provinceRepository): Response
    {
        $municipality = new Municipality();

        $municipality->setName($request->request->get('municipality_name'));

        $provinceId = $request->request->getInt('municipality_province_id');
        $province = $provinceRepository->fetch($provinceId);
        $municipality->setProvince($province);

        $this->repository->save($municipality);

        return JsonResponse::create($this->formatter->format($municipality));
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, requirements={"id": "\d+"})
     *
     * @param Municipality $municipality
     *
     * @return Response
     */
    public function fetchMunicipality(Municipality $municipality): Response
    {
        return JsonResponse::create($this->formatter->format($municipality));
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request            $request
     * @param Municipality       $municipality
     * @param ProvinceRepository $provinceRepository
     *
     * @return Response
     */
    public function editMunicipality(Request $request, Municipality $municipality, ProvinceRepository $provinceRepository): Response
    {
        $municipality->setName($request->request->get('municipality_name'));

        $provinceId = $request->request->getInt('municipality_province_id');
        $province = $provinceRepository->fetch($provinceId);
        $municipality->setProvince($province);

        $this->repository->save($municipality);

        return JsonResponse::create($this->formatter->format($municipality));
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Municipality $municipality
     *
     * @return Response
     */
    public function removeMunicipality(Municipality $municipality): Response
    {
        $this->repository->delete($municipality);

        return JsonResponse::create([]);
    }
}
