<?php

namespace OAuth2Server\Silex;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant;
use OAuth2Server\Repositories\AccessTokenRepository;
use OAuth2Server\Repositories\ClientRepository;
use OAuth2Server\Repositories\RefreshTokenRepository;
use OAuth2Server\Repositories\ScopeRepository;
use OAuth2Server\Repositories\UserRepository;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use OAuth2Server\Repositories\AuthCodeRepository;
use OAuth2Server\Silex\OAuth2Provider;


use Silex\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use \RuntimeException;

class OAuth2ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     * @throws RuntimeException if options are invalid.
     */
    public function register(Container $app)
    {
        /*$app['oauth2server.session_manager'] = function() use ($app) {
            return new SessionStore($app['db']);
        };
		*/
        $app['oauth2server.client_repository'] = function() use ($app) {
            return new ClientRepository($app); // instance of ClientRepositoryInterface
        };

        $app['oauth2server.access_token_repository'] = function() use ($app) {
            return new AccessTokenRepository($app); // instance of AccessTokenRepositoryInterface
        };

        $app['oauth2server.scope_repository'] = function() use ($app) {
            return new ScopeRepository($app); // instance of ScopeRepositoryInterface
        };
        
        $app['oauth2server.auth_code_repository'] = function() use ($app) {
            return new AuthCodeRepository($app); // instance of ScopeRepositoryInterface
        };
        
        $app['oauth2server.refresh_token_repository'] = function() use ($app) {
            return new RefreshTokenRepository($app); // instance of ScopeRepositoryInterface
        };

		$app['oauth2server.user_repository'] = function() use ($app) {
		  
		   return new UserRepository($app); // instance of ScopeRepositoryInterface
		  
		};
		

    $app['oauth2server.auth_server'] = function() use ($app) {
        
    	$privateKey = ($app['oauth2server.key_private']) ? : '/var/www/html/private.key';    // path to private key
    	$publicKey = ($app['oauth2server.key_public']) ? : '/var/www/html/public.key';      // path to public key
        	
			$AuthorizationServer = new AuthorizationServer(
			    $app['oauth2server.client_repository'],
			    $app['oauth2server.access_token_repository'],
			    $app['oauth2server.scope_repository'],
			    $privateKey,
			    $publicKey
			);
            $options = isset($app['oauth2server.options']) ? $app['oauth2server.options'] : array();

            if (array_key_exists('access_token_ttl', $options)) {
                $AuthorizationServer->setExpiresIn($options['access_token_ttl']);
            }

            // Configure grant types.
            if (array_key_exists('grant_types', $options) && is_array($options['grant_types'])) {
                foreach ($app['oauth2server.options']['grant_types'] as $type) {
                    switch ($type) {
                        case 'authorization_code':
                            $AuthorizationServer->enableGrantType(
                                new AuthCodeGrant(
                                    $app['oauth2server.auth_code_repository'],
                                    $app['oauth2server.refresh_token_repository'],
                                    new \DateInterval('PT10M')
                                ),
                                new \DateInterval('PT1H')
                            );
                            break;
                        case 'client_credentials':
                            $AuthorizationServer->enableGrantType(
                            	new \League\OAuth2\Server\Grant\ClientCredentialsGrant(),
                            	new \DateInterval('PT1H') // access tokens will expire after 1 hour
                            );
                            break;
                        case 'password':
                            $grant = new PasswordGrant(
                                $app['oauth2server.user_repository'],           // instance of UserRepositoryInterface
                                $app['oauth2server.refresh_token_repository']    // instance of RefreshTokenRepositoryInterface
                            );
                            $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month
                    
                            // Enable the password grant on the server with a token TTL of 1 hour
                            $AuthorizationServer->enableGrantType(
                                $grant,
                                new \DateInterval('PT1H') // access tokens will expire after 1 hour
                            );
                            break;
                        case 'refresh_token':
                        
                            $grant = new \League\OAuth2\Server\Grant\RefreshTokenGrant($app['oauth2server.refresh_token_repository']);
                            $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // new refresh tokens will expire after 1 month
                            
                            // Enable the refresh token grant on the server
                            $AuthorizationServer->enableGrantType(
                                $grant,
                                new \DateInterval('PT1H') // new access tokens will expire after an hour
                            );
                            
                            //$AuthorizationServer->addGrantType(new RefreshTokenGrantType());
                            break;
                        default:
                            throw new RuntimeException('Invalid grant type "' . $type . '" specified in oauth2server.options.');
                    }
                }
            }
            return $AuthorizationServer;
        };
        
        
        $app['security.authentication_listener.factory.oauth2server'] = $app->protect(function ($name, $options) use ($app) {
            // define the authentication provider object
            $app['security.authentication_provider.' . $name . '.oauth2server'] = function () use ($app) {
                $securityDir = $app['oauth2server.security_dir'] ? $app['oauth2server.security_dir'] : __DIR__ . self::$DEFAULT_SECURITY_DIR;
                $timeWindow = $app['oauth2server.valid_time_window'] ? $app['oauth2server.valid_time_window'] : self::$DEFAULT_VALID_TIME_WINDOW;
                return new OAuth2Provider($app['oauth2server.user'], $securityDir, $timeWindow);
            };

            // define the authentication listener object
            $app['security.authentication_listener.' . $name . '.oauth2server'] = function () use ($app) {
                return new OAuth2Listener($app['security.token_storage'], $app['security.authentication_manager']);
            };

            return array(
                // the authentication provider id
                'security.authentication_provider.' . $name . '.oauth2server',
                // the authentication listener id
                'security.authentication_listener.' . $name . '.oauth2server',
                // the entry point id
                null,
                // the position of the listener in the stack
                'pre_auth'
            );
        });

    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }

}