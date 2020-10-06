<?php

namespace Heretique\DeriveSDK\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Heretique\DeriveSDK\Client\DeriveClient;
use Heretique\DeriveSDK\Document\Derive;
use Heretique\DeriveSDK\Exception\LoginException;
use Heretique\DeriveSDK\Exception\SignupException;
use Heretique\DeriveSDK\Factory\DeriveFactory;
use Heretique\DeriveSDK\Tests\Mock\MockDeriveBackendClient;

use function PHPUnit\Framework\throwException;

class DeriveClientTest extends TestCase
{
    private $secretToken = 'motdepasse';

    private $apiUrl = 'https://derive-api.altertopia.co';

    private function createMockDeriveBackendClient()
    {
        return new MockDeriveBackendClient();
    }

    public function testAuthenticate()
    {
        $httpClient = $this->createMockDeriveBackendClient();
    ;

        try {
            $deriveClient = new DeriveClient($httpClient, $this->secretToken, DeriveClientTestUtils::API_KEY, $this->apiUrl);
        } catch (Exception $exception) {
            $this->fail('Could not instantiate DeriveClient class with exception message : ' . $exception->getMessage());
        }

        try {
            $deriveClient->authenticate();
        } catch (LoginException $loginException) {
            $this->fail('Login failed during authentication with message : ' . $loginException->getMessage());
        } catch (SignupException $signupException) {
            $this->fail('Signup failed during authentication with message : ' . $signupException->getMessage());
        }

        $this->assertEquals('uuid::motdepasse', $deriveClient->getUsername());
        $this->assertEquals('secretaccesstoken', $deriveClient->getAccessToken());
    }

    public function testGetDerive()
    {
        $httpClient = $this->createMockDeriveBackendClient();

        $deriveClient = new DeriveClient($httpClient, $this->secretToken, DeriveClientTestUtils::API_KEY, $this->apiUrl);

        $derive = $deriveClient->getDerive('MMMMMM');

        $this->assertEquals(Derive::class, get_class($derive));
        $this->assertEquals('MMMMMM', $derive->getCode());
        $this->assertNotEmpty($derive->getMessage());
        $this->assertNotEmpty($derive->getAddressPositionLat());
        $this->assertNotEmpty($derive->getAddressPositionLng());
        $this->assertNotEmpty($derive->getAddressText());
    }

    public function testCreateDerive()
    {
        $httpClient = $this->createMockDeriveBackendClient();

        $deriveClient = new DeriveClient($httpClient, $this->secretToken, DeriveClientTestUtils::API_KEY, $this->apiUrl);

        $derive = DeriveFactory::createDeriveFromArray([
            'address' => [
                'position' => [
                    'lat' => '47.9000987',
                    'lng' => '2.9000987'
                ],
                'text' => [
                    'street' => '89 rue de la rifoufi',
                    'city' => 'Lambda',
                    'country' => 'France'
                ]
            ],
            'message' => 'Ok ceci est un message',
            'reveal_address' => True
        ]);

        $derive = $deriveClient->createDerive($derive);

        $this->assertEquals('CCCCCC', $derive->getCode());
    }
}
