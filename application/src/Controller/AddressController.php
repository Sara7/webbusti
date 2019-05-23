<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Municipality;
use App\Events\AddressEvent;
use App\Events\AddressEvents;
use App\Formatter\AddressFormatter;
use App\Repository\AddressRepository;
use App\Repository\MunicipalityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/addresses", requirements={"version": "\d+"})
 */
class AddressController extends BaseController
{
    /** @var AddressRepository */
    protected $repository;

    /** @var AddressFormatter */
    protected $formatter;

    /**
     * AddressController constructor.
     *
     * @param AddressRepository $repository
     * @param AddressFormatter  $formatter
     */
    public function __construct(AddressRepository $repository, AddressFormatter $formatter)
    {
        $this->repository = $repository;
        $this->formatter  = $formatter;
    }

    /**
     * @Route(path="", methods={"GET"})
     * @Security("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAddresses(Request $request): Response
    {
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        $addressesPage = null;
        if ($isAdmin) {
            $perPage = $request->query->getInt('perPage', 30);
            $page = $request->query->getInt('page', 1);

            $addressesPage = $this->repository->getAddresses($perPage, $page);

            $addresses = $addressesPage->getResults();
        } else {
            $user = $this->getUser();
            $addresses = $this->repository->getAddressesByUser($user);
        }

        $addressesToReturn = [];
        foreach ($addresses as $address) {
            $addressesToReturn[] = $this->formatter->format($address);
        }

        $response = JsonResponse::create([
            'addresses' => $addressesToReturn,
        ]);

        if (!$isAdmin) {
            return $response;
        }

        return $this->setResponseHeadersForPage($response, $addressesPage);
    }

    /**
     * @Route(path="", methods={"POST"})
     * @IsGranted("ROLE_USER")
     *
     * @param Request                  $request
     * @param MunicipalityRepository   $municipalityRepository
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function createAddress(Request $request, MunicipalityRepository $municipalityRepository, EventDispatcherInterface $eventDispatcher): Response
    {
        $address = new Address();
        $address->setUser($this->getUser());

        $address->setType($request->request->get('address_type'));
        $address->setStreetName($request->request->get('address_street_name'));
        $address->setStreetNumber($request->request->get('address_street_number'));
        $address->setZip($request->request->get('address_zip'));
        $address->setFreeText($request->request->get('address_free_text'));
        $address->setFavourite($request->request->getBoolean('address_favourite'));

        $municipality = $municipalityRepository->fetch($request->request->getInt('address_municipality_id'));
        $address->setMunicipality($municipality);

        $eventDispatcher->dispatch(AddressEvents::BEFORE_CREATE, new AddressEvent($address));
        $this->repository->save($address);
        $eventDispatcher->dispatch(AddressEvents::CREATED, new AddressEvent($address));

        return JsonResponse::create($this->formatter->format($address));
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, requirements={"id": "\d+"})
     * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_USER') and user == address.user)")
     *
     * @param Address $address
     *
     * @return Response
     */
    public function fetchAddress(Address $address): Response
    {
        return JsonResponse::create($this->formatter->format($address));
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER') and user == address.user")
     *
     * @param Request                $request
     * @param Address                $address
     * @param MunicipalityRepository $municipalityRepository
     *
     * @return Response
     */
    public function editAddress(Request $request, Address $address, MunicipalityRepository $municipalityRepository): Response
    {
        $address->setStreetName($request->request->get('address_street_name'));
        $address->setStreetNumber($request->request->get('address_street_number'));
        $address->setZip($request->request->get('address_zip'));
        $address->setFreeText($request->request->get('address_free_text'));
        $address->setFavourite($request->request->getBoolean('address_favourite'));

        $municipality = $municipalityRepository->fetch($request->request->getInt('address_municipality_id'));
        $address->setMunicipality($municipality);

        $this->repository->save($address);

        return JsonResponse::create($this->formatter->format($address));
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER') and user == address.user")
     *
     * @param Address                  $address
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function removeAddress(Address $address, EventDispatcherInterface $eventDispatcher): Response
    {
        $this->repository->delete($address);
        $eventDispatcher->dispatch(AddressEvents::REMOVED, new AddressEvent($address));

        return JsonResponse::create([]);
    }
}
