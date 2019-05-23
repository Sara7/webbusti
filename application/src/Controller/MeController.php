<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\UserEvent;
use App\Events\UserEvents;
use App\Formatter\UserFormatter;
use App\Repository\UserQualificationRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/users/me", requirements={"version": "\d+"})
 */
class MeController extends BaseController
{
    /**
     * @var UserFormatter
     */
    private $formatter;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * MeController constructor.
     *
     * @param UserFormatter            $formatter
     * @param UserRepository           $repository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(UserFormatter $formatter, UserRepository $repository, EventDispatcherInterface $eventDispatcher)
    {
        $this->formatter = $formatter;
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route(path="", methods={"GET"})
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function fetchMe(): Response
    {
        $user = $this->getUser();

        return JsonResponse::create($this->formatter->formatFull($user));
    }

    /**
     * @Route("/password", methods={"POST"})
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function setPassword(Request $request): Response
    {
        /** @var User $me */
        $me = $this->getUser();

        $oldPassword = $request->request->get('oldPassword');
        if (!password_verify($oldPassword, $me->getPassword())) {
            return JsonResponse::create([
                'msg' => 'Password non corretta',
                'field' => 'oldPassword',
            ], Response::HTTP_BAD_REQUEST);
        }

        $password = $request->request->get('newPassword');
        if (!$password || strlen($password) < 8 || $password === strtolower($password) || !preg_match('/\d/i', $password)) {
            return JsonResponse::create([
                'msg' => 'Password in formato non corretto',
                'field' => 'newPassword',
            ], Response::HTTP_BAD_REQUEST);
        }

        $me->setPassword($password);

        $this->repository->save($me);

        return JsonResponse::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     *
     * @param Request                     $request
     * @param UserQualificationRepository $userQualificationRepository
     *
     * @return Response
     * @throws \Exception
     */
    public function updateMe(Request $request, UserQualificationRepository $userQualificationRepository): Response
    {
        /** @var User $me */
        $me = $this->getUser();

        $userType = $request->request->get('user_type');
        if (!User::isTypeValid($userType)) {
            return JsonResponse::create([
                'msg' => 'Tipo di utente non valido',
                'field' => 'user_type',
            ], Response::HTTP_BAD_REQUEST);
        }
        $me->setType($userType);

        if (User::TYPE_PRIVATE === $me->getType()) {
            $me->setFirstname($request->request->get('user_firstname'));
            $me->setLastname($request->request->get('user_lastname'));
            $me->setBirthDate(new \DateTime($request->request->get('user_birthdate')));

            if (null === $userQualification = $userQualificationRepository->fetch($request->request->getInt('user_qualification_id'))) {
                return JsonResponse::create([
                    'msg' => 'Qualifica non valida',
                    'field' => 'user_qualification_id',
                ], Response::HTTP_BAD_REQUEST);
            }
            $me->setQualification($userQualification);

            $fiscalCode = $request->request->get('user_fiscal_code');
            if (!$fiscalCode) {
                return JsonResponse::create([
                    'msg' => 'Codice fiscale non valido',
                    'field' => 'user_fiscal_code',
                ], Response::HTTP_BAD_REQUEST);
            }
            $me->setFiscalCode($fiscalCode);
        }

        if (User::TYPE_BUSINESS === $me->getType()) {
            $me->setCompanyName($request->request->get('user_company_name'));

            $pec = $request->request->get('user_company_pec');
            if (!$pec || !filter_var($pec, FILTER_VALIDATE_EMAIL)) {
                return JsonResponse::create([
                    'msg' => 'Email Pec in formato non corretto',
                    'field' => 'user_company_pec',
                ], Response::HTTP_BAD_REQUEST);
            }
            $me->setPecAddress($pec);
            $me->setSdiCode($request->request->get('user_company_sdi_code'));
            $me->setVat($request->request->get('user_company_vat_number'));
        }

        $me->setPromoEnabled($request->request->getBoolean('user_promo', false));
        $me->setNewsletterEnabled($request->request->getBoolean('user_newsletter', false));

        $this->repository->save($me);

        return JsonResponse::create($this->formatter->formatFull($me));
    }

    /**
     * @Route(path="", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function removeMe(): Response
    {
        $me = $this->getUser();

        $this->repository->delete($me);
        $this->eventDispatcher->dispatch(UserEvents::REMOVED, new UserEvent($me));

        return JsonResponse::create([], Response::HTTP_NO_CONTENT);
    }
}
