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
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use OAuth2Server\Entities\ClientEntity;

class ClientRepository implements ClientRepositoryInterface
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
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        /*$clients = [
            'myawesomeapp' => [
                'secret'          => password_hash('abc123', PASSWORD_BCRYPT),
                'name'            => 'My Awesome App',
                'redirect_uri'    => 'http://foo/bar',
                'is_confidential' => true,
            ],
        ];*/
        
        
        $sql = 'SELECT oauth_client.id AS client_id, secret AS client_secret, name ';
        $sql .= 'FROM oauth_client ';
        $sql .= 'WHERE oauth_client.id = :clientId ';
        $params = array(':clientId' => $clientIdentifier);

        if ($mustValidateSecret && $clientSecret !== null) {
            $sql .= 'AND secret = :clientSecret ';
            $params[':clientSecret'] = $clientSecret;
        }

        $clientDb = $this->conn->fetchAssoc($sql, $params);
        

        // Check if client is registered
        if (!$clientDb) {
            return;
        }


        $client = new ClientEntity();
        $client->setIdentifier($clientIdentifier);
        $client->setName($clientDb['name']);

        return $client;
    }
}
