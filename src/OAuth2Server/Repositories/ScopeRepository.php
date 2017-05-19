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
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use OAuth2Server\Entities\ScopeEntity;

class ScopeRepository implements ScopeRepositoryInterface
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
    public function getScopeEntityByIdentifier($scopeIdentifier)
    {
        $scopeDb = $this->conn->fetchAssoc('SELECT * FROM oauth_scope WHERE scope = ? LIMIT 1', array($scopeIdentifier));
		
		if(!$scopeDb){
			return;
		}

        $scope = new ScopeEntity();
        $scope->setIdentifier($scopeIdentifier);
        //todo: add parameters

        return $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        // Example of programatically modifying the final scope of the access token
        /*if ((int) $userIdentifier === 1) {
            $scope = new ScopeEntity();
            $scope->setIdentifier('email');
            $scopes[] = $scope;
        }*/
        
        return $scopes;
    }
}
