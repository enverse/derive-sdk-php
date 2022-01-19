<?php

namespace Heretique\DeriveSDK\Client;

use Heretique\DeriveSDK\Document\Address;
use Heretique\DeriveSDK\Document\AddressText;
use Heretique\DeriveSDK\Exception\LoginException;
use Heretique\DeriveSDK\Exception\SignupException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Heretique\DeriveSDK\Document\Derive;
use Heretique\DeriveSDK\Exception\CreateDeriveException;
use Heretique\DeriveSDK\Exception\ForwardGeocodeException;
use Heretique\DeriveSDK\Exception\GetDeriveException;
use Heretique\DeriveSDK\Factory\DeriveFactory;

use function PHPUnit\Framework\throwException;

/**
 * Makes requests to the Derive backend API so that you can retrieve registered Derives
 */
class DeriveClient implements DeriveClientInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $authorizedToken;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var bool
     */
    private $authenticated;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param HttpClientInterface $client
     * @param string $authorizedToken -> Requires a special authorized token provided by the people responsible of the Derive backend. This key is personnal and should not be communicated
     * @param string $apiKey -> secret API key that needs to be provided by the people responsible of the Derive backend
     * @param string $apiUrl -> the API url
     */
    public function __construct(HttpClientInterface $client, string $authorizedToken, string $apiKey, string $apiUrl)
    {
        $this->httpClient = $client;
        $this->authorizedToken = $authorizedToken;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * Performs authentication with the server.
     * 
     * Throws an exception if fails during the process.
     * 
     * @return void
     * @throws SignupException
     * @throws LoginException
     */
    public function authenticate()
    {
        if (!$this->authorizedToken) {
            throw new SignupException('No authorized token was specified');
        }

        $this->username = $this->signup();

        if (!$this->username) {
            throw new SignupException('Client was unable to retrieve the username');
        }

        $this->accessToken = $this->login();

        if (!$this->accessToken) {
            throw new LoginException('Client was unable to retrieve the access token');
        }

        $this->authenticated = true;
    }

    /**
     * Register the client with the server
     * 
     * @return string|null
     * 
     * @throws SignupException
     */
    private function signup()
    {
        $signupResponse = $this->httpClient->request('POST', $this->apiUrl . '/device/signup', [
            'body' => json_encode(['uuid' => $this->authorizedToken]),
            'headers' => $this->getHeaders(),
        ]);

        $this->checkResponseIsOk($signupResponse, SignupException::class);

        $signupResponseContent = $signupResponse->toArray(true);

        if (!isset($signupResponseContent['username'])) {
            throw new SignupException('Unexpected response content');
        }

        return $signupResponseContent['username'];
    }

    /**
     * Performs login with the server to retrieve the access token
     *
     * @return string|null
     * @throws LoginException
     */
    private function login()
    {
        $loginResponse = $this->httpClient->request('POST', $this->apiUrl . '/auth', [
            'body' => json_encode([
                'username' => $this->username,
                'password' => $this->apiKey
            ]),
            'headers' => $this->getHeaders(),
        ]);

        $this->checkResponseIsOk($loginResponse, LoginException::class);

        $loginResponseContent = $loginResponse->toArray(true);

        if (!isset($loginResponseContent['access_token'])) {
            throw new LoginException('Unexpected response content');
        }

        return $loginResponseContent['access_token'];
    }

    /**
     * Generic check of the server's reponse.
     * 
     * @param ResponseInterface $response
     * @param Exception $exceptionClass -> the exception to be thrown if the response is not ok.
     * @return void
     */
    private function checkResponseIsOk(ResponseInterface $response, $exceptionClass)
    {
        if (!in_array($response->getStatusCode(), [200, 261, 201])) {
            throw new $exceptionClass('Derive Backend resquest failed with status code : ' . $response->getStatusCode());
        }

        if ($response->getHeaders(true)['content-type'][0] !== 'application/json') {
            throw new $exceptionClass('Content Type of response is not json.');
        }
    }

    /**
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string|bool
     */
    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    /**
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return array
     */
    private function getHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'X-App-Version' => 'drd-b'
        ];
    }

    private function getAuthenticatedHeaders()
    {
        $headers = $this->getHeaders();

        $headers['Authorization'] = 'JWT ' . $this->accessToken;

        return $headers;
    }

    /**
     * @param [type] $code
     * @return Derive
     */
    public function getDerive($code)
    {
        if (!$this->isAuthenticated()) {
            $this->authenticate();
        }

        $deriveResponse = $this->httpClient->request('GET', $this->apiUrl . '/derive' . '/' . $code, [
            'headers' => $this->getAuthenticatedHeaders(),
        ]);

        $this->checkResponseIsOk($deriveResponse, GetDeriveException::class);

        $body = $deriveResponse->toArray();
        
        $derive = DeriveFactory::createDeriveFromArray($body);

        return $derive;
    }

    public function createDerive(Derive $derive)
    {
        if (!$this->isAuthenticated()) {
            $this->authenticate();
        }

        $deriveResponse = $this->httpClient->request('POST', $this->apiUrl . '/derive', [
            'body' => json_encode($derive->toArray()),
            'headers' => $this->getAuthenticatedHeaders()
        ]);
        
        $this->checkResponseIsOk($deriveResponse, CreateDeriveException::class);
        
        $body = $deriveResponse->toArray();

        $code = $body['code'];

        $derive->setCode($code);

        return $derive;
    }

    public function editDerive(Derive $derive)
    {
        if (!$this->isAuthenticated()) {
            $this->authenticate();
        }

        $deriveResponse = $this->httpClient->request('POST', $this->apiUrl . '/derive/edit/'. $derive->getCode(), [
            'body' => json_encode($derive->toArray()),
            'headers' => $this->getAuthenticatedHeaders()
        ]);
        
        $this->checkResponseIsOk($deriveResponse, CreateDeriveException::class);
        
        return $derive;
    }

    public function deleteDerive($code)
    {
        if (!$this->isAuthenticated()) {
            $this->authenticate();
        }

        $deriveResponse = $this->httpClient->request('POST', $this->apiUrl . '/derive/delete/'.$code, [
            'headers' => $this->getAuthenticatedHeaders()
        ]);
        
        $this->checkResponseIsOk($deriveResponse, CreateDeriveException::class);

        return true;
    }

    public function forwardGeocode(string $query)
    {
        if (!$this->isAuthenticated()) {
            $this->authenticate();
        }

        $forwardGeocodeResponse = $this->httpClient->request('GET', $this->apiUrl . '/geocode/forward', [
            'query' => [
                'address' => $query,
            ],
            'headers' => $this->getAuthenticatedHeaders()
        ]);

        $this->checkResponseIsOk($forwardGeocodeResponse, ForwardGeocodeException::class);

        $body = $forwardGeocodeResponse->toArray();

        $addresses = [];

        foreach ($body as $address) {
            array_push($addresses, DeriveFactory::createAddressFromArray([
                'address' => $address
            ]));
        }

        return $addresses;
    }
}