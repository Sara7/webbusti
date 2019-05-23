<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\UserEvent;
use App\Events\UserEvents;
use App\Formatter\UserFormatter;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/users", requirements={"version": "\d+"})
 */
class UserController extends BaseController
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var UserFormatter
     */
    private $formatter;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * UserController constructor.
     *
     * @param UserRepository           $repository
     * @param UserFormatter            $formatter
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(UserRepository $repository, UserFormatter $formatter, EventDispatcherInterface $eventDispatcher)
    {
        $this->repository = $repository;
        $this->formatter = $formatter;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route(path="", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function createUser(Request $request): Response
    {
        $user = new User();

        $email = $request->request->get('email');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return JsonResponse::create([
                'msg' => 'Email in formato non corretto',
                'field' => 'email',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (null !== $this->repository->getUserByEmail($email)) {
            return JsonResponse::create([
                'msg' => 'Email già associata ad un account',
                'field' => 'email',
            ], Response::HTTP_BAD_REQUEST);
        }

        $password = $request->request->get('password');
        if (!$password || strlen($password) < 8 || $password === strtolower($password) || !preg_match('/\d/i', $password)) {
            return JsonResponse::create([
                'msg' => 'Password in formato non corretto',
                'field' => 'password',
            ], Response::HTTP_BAD_REQUEST);
        }

        $promo = $request->request->getBoolean('promo', false);
        $newsletter = $request->request->getBoolean('newsletter', false);

        $user->setEmail($email);
        $user->setPassword($password);
        $user->setPromoEnabled($promo);
        $user->setNewsletterEnabled($newsletter);

        $user->generateValidationCode();

        $this->eventDispatcher->dispatch(UserEvents::BEFORE_CREATE, new UserEvent($user));
        $this->repository->save($user);
        $this->eventDispatcher->dispatch(UserEvents::CREATED, new UserEvent($user));

        return JsonResponse::create($this->formatter->formatFull($user));
    }

    /**
     * @Route("/activation/{email}", methods={"POST"})
     *
     * @param Request $request
     * @param string  $email
     *
     * @return Response
     * @throws \Exception
     */
    public function activateUser(Request $request, string $email): Response
    {
        $code = $request->request->get('code');
        if (!$code) {
            return JsonResponse::create([
                'msg' => 'Il codice di attivazione non è valido',
                'field' => 'code',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (null === $user = $this->repository->getByActivationCode($email, $code)) {
            return JsonResponse::create([
                'msg' => 'Combinazione email e codice di attivazione non valida',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->resetValidationCode();
        $user->setStatus(User::STATUS_ACTIVE);
        $user->setActivatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $this->repository->save($user);

        $this->eventDispatcher->dispatch(UserEvents::ACTIVATED, new UserEvent($user));

        return JsonResponse::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/password/{email}", methods={"DELETE"})
     *
     * @param string  $email
     *
     * @return Response
     */
    public function recoverPassword(string $email): Response
    {
        if (null === $user = $this->repository->getUserByEmail($email)) {
            return JsonResponse::create([], Response::HTTP_NOT_FOUND);
        }

        if (User::STATUS_ACTIVE !== $user->getStatus()) {
            return JsonResponse::create([], Response::HTTP_NOT_FOUND);
        }

        $user->generatePasswordRecoveryCode();
        $this->repository->save($user);
        $this->eventDispatcher->dispatch(UserEvents::RECOVER_PASSWORD, new UserEvent($user));
    }

    /**
     * @Route("/password/{email}", methods={"POST"})
     *
     * @param Request $request
     * @param string  $email
     *
     * @return Response
     * @throws \Exception
     */
    public function setPasswordWithRecoverCode(Request $request, string $email): Response
    {
        if (null === $user = $this->repository->getUserByEmail($email)) {
            return JsonResponse::create([], Response::HTTP_NOT_FOUND);
        }

        if (User::STATUS_ACTIVE !== $user->getStatus()) {
            return JsonResponse::create([], Response::HTTP_NOT_FOUND);
        }

        if (!$user->getPasswordRecoveryCode()) {
            return JsonResponse::create([], Response::HTTP_NOT_FOUND);
        }

        $now = new \DateTime();
        $now->sub(new \DateInterval('P2D'));
        if ($now > $user->getRecoverPasswordStartedAt()) {
            return JsonResponse::create([], Response::HTTP_NOT_FOUND);
        }

        $code = $request->request->get('code');
        if ($code !== $user->getPasswordRecoveryCode()) {
            return JsonResponse::create([], Response::HTTP_NOT_FOUND);
        }

        $password = $request->request->get('password');
        if (!$password || strlen($password) < 8 || $password === strtolower($password) || !preg_match('/\d/i', $password)) {
            return JsonResponse::create([
                'msg' => 'Password in formato non corretto',
                'field' => 'password',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->resetPasswordRecoveryCode();
        $user->setPassword($password);

        $this->repository->save($user);

        return JsonResponse::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param User $user
     *
     * @return Response
     */
    public function fetchUser(User $user): Response
    {
        $me = $this->getUser();

        return JsonResponse::create($this->formatter->formatFull($user, $me));
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param User $user
     *
     * @return Response
     */
    public function removeUser(User $user): Response
    {
        $this->repository->delete($user);
        $this->eventDispatcher->dispatch(UserEvents::REMOVED, new UserEvent($user));

        return JsonResponse::create([], Response::HTTP_NO_CONTENT);
    }
}
