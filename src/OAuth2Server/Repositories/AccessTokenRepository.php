<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2Server\Repositories;

use Pimple\Container;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use OAuth2Server\Entities\AccessTokenEntity;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    
    protected $conn;
    protected $app;


    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->conn = $app['oauth2server.db'];
        $this->connEm = $app['orm.ems']['mysql'];
        $this->tokenRepo = $this->connEm->getRepository('\Locopoly\Entity\OauthAccessToken');
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        // Some logic here to save the access token to a database
        //print_r($accessTokenEntity);
        $token = new \Locopoly\Entity\OauthAccessToken();
        //print_r(get_class_methods($accessTokenEntity));
        $token->setAccessToken($accessTokenEntity->getIdentifier());
        $token->setAccessTokenExpires($accessTokenEntity->getExpiryDateTime()->getTimestamp());
        $token->setClientId($accessTokenEntity->getClient()->getIdentifier());
        $token->setUserId($accessTokenEntity->getUserIdentifier());
        $this->connEm->persist($token);
        $this->connEm->flush();
        
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        $token = $this->tokenRepo->findOneBy(
        	array('access_token'=>$tokenId)
        );
        if (!$token) {
           return;
        }
        $this->connEm->remove($token);
        $this->connEm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $token = $this->tokenRepo->findOneBy(
        	array('access_token'=>$tokenId)
        );
        if (!$token) {
           return true;
        }
        return false; // Access token hasn't been revoked
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }
}
