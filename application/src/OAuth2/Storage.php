<?php

namespace App\OAuth2;

use App\Entity\OAuth2AccessToken;
use App\Entity\OAuth2Client;
use App\Entity\OAuth2RefreshToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OAuth2\Storage\AccessTokenInterface;
use OAuth2\Storage\ClientCredentialsInterface;
use OAuth2\Storage\UserCredentialsInterface;
use OAuth2\Storage\RefreshTokenInterface;

/**
 * Class Storage
 *
 * @package App\OAuth2
 */
class Storage implements AccessTokenInterface, ClientCredentialsInterface, UserCredentialsInterface, RefreshTokenInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * Storage constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function checkUserCredentials($username, $password): bool
    {
        $repository = $this->entityManager->getRepository(User::class);
        /** @var User|null $user */
        $user = $repository->findOneBy([
            'email'  => $username,
        ]);

        if (null === $user) {
            return false;
        }

        if (!password_verify($password, $user->getPassword())) {
            return false;
        }

        return true;
    }

    /**
     * @param string $username
     *
     * @return array|false
     */
    public function getUserDetails($username)
    {
        $repository = $this->entityManager->getRepository(User::class);
        /** @var User|null $user */
        $user = $repository->findOneBy(['email' => $username]);

        if (null === $user) {
            return false;
        }

        $userDetails = [];
        $userDetails['user_id'] = $user->getId();

        if (User::STATUS_PENDING === $user->getStatus()) {
            $userDetails['need_activation'] = true;
        }

        return $userDetails;
    }

    /**
     * @param string      $token
     * @param mixed       $clientId
     * @param mixed       $userId
     * @param int         $expires
     * @param string|null $scope
     *
     * @throws \Exception
     */
    public function setAccessToken($token, $clientId, $userId, $expires, $scope = null): void
    {
        $accessTokenRepository = $this->entityManager->getRepository(OAuth2AccessToken::class);
        $accessToken           = $accessTokenRepository->findOneBy(
            [
                'token'    => $token,
                'idClient' => (string) $clientId,
            ]
        );

        if (null === $accessToken) {
            $accessToken = new OAuth2AccessToken();
        }

        $accessToken->setToken($token);
        $accessToken->setScope($scope);
        $accessToken->setIdClient($clientId);
        $accessToken->setIdUser($userId);

        $expiresDateTime = new \DateTime('@' . $expires);
        $expiresDateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $accessToken->setExpires($expiresDateTime);

        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();
    }

    /**
     * @param string $token
     * @param string $clientId
     */
    public function unsetAccessToken($token, $clientId): void
    {
        $accessTokenRepository = $this->entityManager->getRepository(OAuth2AccessToken::class);
        /** @var OAuth2AccessToken|null $accessToken */
        $accessToken           = $accessTokenRepository->findOneBy(
            [
                'token'    => $token,
                'idClient' => $clientId,
            ]
        );

        if (null === $accessToken) {
            return;
        }

        $this->entityManager->remove($accessToken);
        $this->entityManager->flush();
    }

    /**
     * @param string $token
     *
     * @return array|null
     */
    public function getAccessToken($token): ?array
    {
        $accessTokenRepository = $this->entityManager->getRepository(OAuth2AccessToken::class);
        /** @var OAuth2AccessToken|null $accessToken */
        $accessToken = $accessTokenRepository->findOneBy(
            [
                'token' => $token,
            ]
        );

        if (null === $accessToken) {
            return null;
        }

        return [
            'access_token' => $accessToken->getToken(),
            'client_id'    => $accessToken->getIdClient(),
            'user_id'      => $accessToken->getIdUser(),
            'expires'      => $accessToken->getExpires()->format('U'),
            'scope'        => $accessToken->getScope(),
        ];
    }

    /**
     * @param string      $token
     * @param string      $clientId
     * @param string      $userId
     * @param int         $expires
     * @param string|null $scope
     *
     * @throws \Exception
     */
    public function setRefreshToken($token, $clientId, $userId, $expires, $scope = null): void
    {
        $refreshTokenRepository = $this->entityManager->getRepository(OAuth2RefreshToken::class);
        /** @var OAuth2RefreshToken|null $refreshToken */
        $refreshToken           = $refreshTokenRepository->findOneBy(
            [
                'token'    => $token,
                'idClient' => $clientId,
            ]
        );

        if (null === $refreshToken) {
            $refreshToken = new OAuth2RefreshToken();
        }

        $refreshToken->setToken($token);
        $refreshToken->setScope($scope);
        $refreshToken->setIdClient($clientId);
        $refreshToken->setIdUser($userId);
        $expiresDateTime = new \DateTime('@' . $expires);
        $expiresDateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $refreshToken->setExpires($expiresDateTime);

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();
    }

    /**
     * @param string $token
     */
    public function unsetRefreshToken($token): void
    {
        $refreshTokenRepository = $this->entityManager->getRepository(OAuth2RefreshToken::class);
        /** @var OAuth2RefreshToken|null $refreshToken */
        $refreshToken           = $refreshTokenRepository->findOneBy(
            [
                'token' => $token,
            ]
        );

        if (null === $refreshToken) {
            return;
        }

        $this->entityManager->remove($refreshToken);
        $this->entityManager->flush();
    }

    /**
     * @param string $token
     *
     * @return array|null
     */
    public function getRefreshToken($token): ?array
    {
        $refreshTokenRepository = $this->entityManager->getRepository(OAuth2RefreshToken::class);
        /** @var OAuth2RefreshToken|null $refreshToken */
        $refreshToken = $refreshTokenRepository->findOneBy(
            [
                'token' => $token,
            ]
        );

        if (null === $refreshToken) {
            return null;
        }

        return [
            'refresh_token' => $refreshToken->getToken(),
            'client_id'     => $refreshToken->getIdClient(),
            'user_id'       => $refreshToken->getIdUser(),
            'expires'       => $refreshToken->getExpires()->format('U'),
            'scope'         => $refreshToken->getScope(),
        ];
    }

    /**
     * @param string $clientId
     *
     * @return boolean|array
     */
    public function getClientDetails($clientId)
    {
        $clientRepository = $this->entityManager->getRepository(OAuth2Client::class);
        /** @var OAuth2Client|null $client */
        $client = $clientRepository->findOneBy(
            [
                'idClient' => $clientId,
            ]
        );

        if (null === $client) {
            return false;
        }

        return [
            'client_id'     => $client->getIdClient(),
            'client_secret' => $client->getSecret(),
            'redirect_uri'  => $client->getRedirectUri(),
        ];
    }

    /**
     * @param string $clientId
     *
     * @return bool|mixed
     */
    public function getClientScope($clientId)
    {
        $clientDetails = $this->getClientDetails($clientId);

        if (!is_array($clientDetails) || !isset($clientDetails['scope'])) {
            return false;
        }

        return $clientDetails['scope'];
    }

    /**
     * @param string $clientId
     *
     * @return bool
     */
    public function isPublicClient($clientId): bool
    {
        $client = $this->getClientDetails($clientId);

        if (!is_array($client) || !isset($client['client_secret'])) {
            return false;
        }

        $clientSecret = $client['client_secret'];

        return empty($clientSecret);
    }

    /**
     * @param string $client_id
     * @param string|null $client_secret
     *
     * @return bool
     */
    public function checkClientCredentials($client_id, $client_secret = null): bool
    {
        $client = $this->getClientDetails($client_id);
        if (!is_array($client)) {
            return false;
        }

        return isset($client['client_secret']) && $client['client_secret'] === $client_secret;
    }

    /**
     * @param string $client_id
     * @param string $grant_type
     *
     * @return bool
     */
    public function checkRestrictedGrantType($client_id, $grant_type): bool
    {
        $details = $this->getClientDetails($client_id);
        if (!is_array($details)) {
            return false;
        }

        if (isset($details['grant_types'])) {
            $grant_types = explode(' ', $details['grant_types']);

            return \in_array($grant_type, $grant_types, true);
        }

        return true;
    }
}
