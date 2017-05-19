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
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use OAuth2Server\Entities\AuthCodeEntity;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    
    
    protected $conn;
    protected $app;


    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->conn = $app['oauth2server.db'];
        $this->connEm = $app['orm.ems']['mysql'];
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        // Some logic to persist the auth code to a database
        
        /*$sql = 'INSERT INTO oauth_session_authcodes (client_id, owner_type, owner_id) VALUES (:clientId, :ownerType, :ownerId)';
        $params = array('clientId' => $clientId, 'ownerType' => $ownerType, 'ownerId' => $ownerId);
        $this->conn->executeUpdate($sql, $params);*/
        print_r($authCodeEntity);
        
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId)
    {
        // Some logic to revoke the auth code in a database
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
    {
        return false; // The auth code has not been revoked
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }
}
