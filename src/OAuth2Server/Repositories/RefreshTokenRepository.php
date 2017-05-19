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
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use OAuth2Server\Entities\RefreshTokenEntity;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    protected $conn;
    protected $app;


    public function __construct(Container $app)
    {
        $this->app = $app;
       	$this->conn = $app['oauth2server.db'];
    	$this->connEm = $app['orm.ems']['mysql'];
    	$this->tokenRepo = $this->connEm->getRepository('\Locopoly\Entity\OauthRefreshToken');
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        // Some logic to persist the refresh token in a database
        $token = new \Locopoly\Entity\OauthRefreshToken();
    	$token->setRefreshToken($refreshTokenEntity->getIdentifier());
    	
    	$token->setRefreshTokenExpires($refreshTokenEntity->getExpiryDateTime()->getTimestamp());
    	$token->setAccessTokenId($refreshTokenEntity->getAccessToken()->getIdentifier());
    	$this->connEm->persist($token);
    	$this->connEm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        // Some logic to revoke the refresh token in a database
        $token = $this->tokenRepo->findOneBy(
        	array('refresh_token'=>$tokenId)
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
    public function isRefreshTokenRevoked($tokenId)
    {
        $token = $this->tokenRepo->findOneBy(
        	array('refresh_token'=>$tokenId)
        );
        if (!$token) {
           return true;
        }
        return false; // The refresh token has not been revoked
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
    
    	$refreshTokenEntity = new RefreshTokenEntity();
        return $refreshTokenEntity;
    }
}
