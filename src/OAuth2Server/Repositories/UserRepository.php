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
use Symfony\Component\Security\Core\User\User;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use OAuth2Server\Entities\UserEntity;

class UserRepository implements UserRepositoryInterface
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
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
    
  		
        $userEntity = $this->connEm->getRepository('\Locopoly\Entity\User');
        $userDb = $userEntity->findOneBy(
        	array(
        		'username'=>$username
        	)
        );
        
        
        if (!$userDb) {
        	 throw new \Exception(sprintf('Username "%s" does not exist.', $username));
        	//throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
       	// Get the encoder for the users password
		  $user = new User($username,$userDb->getPassword());
		  
		  $encoder_service = $this->app['security.encoder_factory'];
		  $encoder = $encoder_service->getEncoder($user);

      	// Empty password => oauth user, or validate password here
      	
      	//other auth service (username of login must be same as login trying to authenticate
         if( !empty( $userDb->getService()) && $this->app['user']->getUsername() !== $username ){
         	return;
         
         //if own user account, service is empty (pw must match!)	
         } else if (empty($userDb->getService()) && !$encoder->isPasswordValid($userDb->getPassword(), $password, $user->getSalt())) {
           // Password bad
           return;
         } 
          // Get profile list
          $user = new UserEntity();
        $user->setIdentifier($userDb->getId());
        
        return $user;
        
    }
}
