<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends BaseController
{
    /** @var UserRepository */
    protected $userRepository;

    /**
     * SessionController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(path="/api/v{version}/sessions/create", methods={"POST"}, requirements={"version": "\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Session $session
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function createSession(Session $session): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $session->set('id_user', $user->getId());
        $session->migrate(true);
        $session->save();

        return JsonResponse::create([
            'session_id' => $session->getId(),
        ]);
    }

    /**
     * @Route(path="/api/v{version}/upload", methods={"POST"})
     *
     * @param Request $request
     * @param Session $session
     *
     * @return Response
     */
    public function uploadFile(Request $request, Session $session): Response
    {
        $sessionId = $request->request->get('session_id');
        if (!$sessionId) {
            throw new AccessDeniedHttpException();
        }

        $session->setId($sessionId);

        $idUser = $session->get('id_user');
        if (!$idUser) {
            throw new AccessDeniedHttpException();
        }

        if (null === $user = $this->userRepository->get($idUser)) {
            throw new AccessDeniedHttpException();
        }

        if (!$user->isAdmin()) {
            throw new AccessDeniedHttpException();
        }

        // Todo: qui gestire l'upload

        return JsonResponse::create([]);
    }
}
