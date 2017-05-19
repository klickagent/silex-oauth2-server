<?php

namespace OAuth2Server\Silex;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OAuth2Server\Silex\OAuth2UserToken;

class OAuth2Provider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $cacheDir;
    private $timeWindow;

    public function __construct(UserProviderInterface $userProvider, $cacheDir, $timeWindow)
    {
        $this->userProvider = $userProvider;
        $this->cacheDir     = $cacheDir;
        $this->timeWindow = $timeWindow;
    }

    public function authenticate(TokenInterface $token)
    {
        return $token;
    }

    /**
     * 
     *
     * For more information specific to the logic here, see
     * https://github.com/symfony/symfony-docs/pull/3134#issuecomment-27699129
     */
    protected function validateDigest($digest, $nonce, $created, $secret)
    {
        return true;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof OAuth2UserToken;
    }
}

