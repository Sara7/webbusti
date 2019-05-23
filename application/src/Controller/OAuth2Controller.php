<?php

namespace App\Controller;

use App\Entity\User;
use App\OAuth2\Storage;
use App\Security\UserActivationManager;
use OAuth2\Request as OAuth2Request;
use OAuth2\Response as OAuth2Response;
use OAuth2\Server as OAuth2Server;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller about OAuth2.
 *
 * @author Andrea Cristaudo <andrea.cristaudo@gmail.com>
 *
 * @Route(path="/api/v{version}", requirements={"version": "\d+"})
 */
class OAuth2Controller extends BaseController
{
    /** @var OAuth2Server */
    private $server;

    /**
     * OAuth2Controller constructor.
     *
     * @param OAuth2Server $server
     */
    public function __construct(OAuth2Server $server)
    {
        $this->server = $server;
    }

    /**
     * @Route(path="/token", name="token", methods="POST")
     *
     * @param int $version
     */
    public function tokenAction(int $version = 1): void
    {
        $this->minMaxVersion($version);

        $response = new OAuth2Response([], 200, ['Access-Control-Allow-Origin' => '*']);

        /** @var OAuth2Response $response */
        $response = $this->server->handleTokenRequest(OAuth2Request::createFromGlobals(), $response);
        $response->send();
        die();
    }

    /**
     * @Route(path="/revoke", methods={"POST"})
     * @IsGranted("ROLE_USER")
     *
     * @param int $version
     *
     * @return Response
     */
    public function revokeAction(int $version = 1): Response
    {
        $this->minMaxVersion($version);

        $token    = '';
        $clientId = '';

        /** @var Storage $storage */
        $storage = $this->server->getStorage('0');
        if (null !== $storage) {
            $storage->unsetRefreshToken($token);
            $storage->unsetAccessToken($token, $clientId);
        }

        return JsonResponse::create(
            null,
            200,
            ['Access-Control-Allow-Origin' => '*']
        );
    }

    /**
     * @Route(path="/me", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     *
     * @param int $version
     *
     * @return Response
     */
    public function getMeAction(int $version = 1): Response
    {
        $this->minMaxVersion($version);

        /** @var User $user */
        $user = $this->getUser();

        return JsonResponse::create($user->getMeSerialized());
    }
}
