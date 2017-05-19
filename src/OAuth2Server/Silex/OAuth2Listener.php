<?php

namespace OAuth2Server\Silex;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use OAuth2Server\Silex\OAuth2UserToken;

use League\OAuth2\Server\ResourceServer;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class OAuth2Listener implements ListenerInterface
{
    protected $tokenStorage;
    protected $authenticationManager;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager) 
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
	    global $app;
	    
        $request = $event->getRequest();
        $authregex = '~Bearer (.)*~';
        if (!$request->headers->has('x-authorization') || 1 !== preg_match($authregex, $request->headers->get('x-authorization'), $matches)) {
            return;
        }
        // add bearer since oauth by php league checks this:
        $request->headers->set('authorization',$matches[0]);
        $psr7Factory = new DiactorosFactory();
        // convert a Request
        $psrRequest = $psr7Factory->createRequest($request);
        $server = new ResourceServer(
            $app['oauth2server.access_token_repository'],            // instance of AccessTokenRepositoryInterface
            ($app['oauth2server.key_public']) ? : '/var/www/html/public.key'  // the authorization server's public key
        );
        
        try {
            $psrRequest = $server->validateAuthenticatedRequest($psrRequest);
            
            $token = new OAuth2UserToken( $psrRequest->getAttribute('oauth_scopes') );
            $user = $app['oauth2server.user']->loadUserById($psrRequest->getAttribute('oauth_user_id'));
            $token->setUser($user);
            $authToken = $this->authenticationManager->authenticate($token); // pseudo request to follow protocol of silex auth
            $this->tokenStorage->setToken($authToken);
            return;
        } catch (OAuthServerException $exception) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $response->setContent('Server error');
            $event->setResponse($response);
            // @codeCoverageIgnoreStart
        } catch (\Exception $exception) {
           $response = new Response();
           $response->setStatusCode(Response::HTTP_FORBIDDEN);
           $response->setContent($exception->getMessage());
           $event->setResponse($response);
           return;
            // @codeCoverageIgnoreEnd
        }
        
        // By default deny authorization
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $response->setContent("OAuth authentication failed");
        $event->setResponse($response);
        
    }
}

