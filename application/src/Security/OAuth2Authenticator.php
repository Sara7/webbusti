<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OAuth2\Request as OAuth2Request;
use OAuth2\Server;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class OAuth2Authenticator extends AbstractGuardAuthenticator
{
    /** @var Server */
    private $oauth2Server;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * OAuth2Authenticator constructor.
     *
     * @param Server                 $server
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Server $server, EntityManagerInterface $entityManager)
    {
        $this->oauth2Server  = $server;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        if ($request->attributes->get('_route') === 'token' && $request->isMethod('POST')) {
            return false;
        }

        /** @var string $token */
        $token = $request->headers->get('Authorization', '');
        $token = trim($token);

        return
            false !== strpos($token, ' ')
            &&
            0 === strpos($token, 'Bearer');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request): array
    {
        /** @var string $token */
        $token = $request->headers->get('Authorization', '');
        $token = trim($token);

        $parts = explode(' ', $token);

        return [
            'token' => $parts[1],
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        // $token = $credentials['token']; // TODO: we should use this

        if (!$this->oauth2Server->verifyResourceRequest(OAuth2Request::createFromGlobals())) {
            return null;
        }

        $atd = $this->oauth2Server->getAccessTokenData(OAuth2Request::createFromGlobals());
        $id  = $atd['user_id'];

        try {
            $userRepository = $this->entityManager->getRepository(User::class);
            /** @var User|null $user */
            $user = $userRepository->findOneBy([
                'id' => $id,
                'status' => User::STATUS_ACTIVE,
            ]);
        } catch (\Exception $e) {
            return null;
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return JsonResponse::create($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            'message' => 'Authentication Required',
        ];

        return JsonResponse::create($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
