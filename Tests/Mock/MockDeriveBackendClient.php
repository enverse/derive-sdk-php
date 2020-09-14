<?php

namespace Heretique\DeriveSDK\Tests\Mock;

use Heretique\DeriveSDK\Tests\DeriveClientTestUtils;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class MockDeriveBackendClient extends MockHttpClient
{
    private $registeredUsername = null;

    public function __construct()
    {
        parent::__construct(function ($method, $url, $options) {
            if (!DeriveClientTestUtils::testContentTypeHeaderIsSet($options['headers'])) {
                return new MockResponse('', ['http_code' => 400]);
            }

            if (!DeriveClientTestUtils::testXAppVersionHeaderIsSet($options['headers'])) {
                return new MockResponse('', ['http_code' => 460]);
            }

            if ($url == 'https://derive-api.altertopia.co/device/signup' && $method == 'POST') {
                $body = json_decode($options['body']);
                if (!isset($body->uuid)) {
                    return new MockResponse('', ['http_code' => 400]);
                }
                $this->registeredUsername = 'uuid::' . $body->uuid;
                return new MockResponse('{"username":"' . $this->registeredUsername . '"}', [
                    'response_headers' => [
                        'Content-Type' => 'application/json'
                    ]
                ]);
            } elseif ($url == 'https://derive-api.altertopia.co/auth' && $method == 'POST') {
                $body = json_decode($options['body']);
                $username = $body->username;
                $password = $body->password;

                if ($password !== DeriveClientTestUtils::API_KEY || !$this->registeredUsername || $this->registeredUsername !== $username) {
                    return new MockResponse('', ['http_code' => 400]);
                }

                return new MockResponse('{"access_token":"secretaccesstoken"}', [
                    'response_headers' => [
                        'Content-Type' => 'application/json'
                    ]
                ]);
            } elseif (preg_match('/^https:\/\/derive-api\.altertopia\.co\/derive/', $url)) {
                if (!DeriveClientTestUtils::testAuthorizationHeaderIsSet($options['headers'], 'secretaccesstoken')) {
                    return new MockResponse('', ['http_code' => 401]);
                }

                return new MockResponse(
                    '{"lat": 48.7506, "lng": 2.28854, "message": "Quelque part au milieu de nul part", "code": "MMMMMM", "address": "90 a Avenue Fran\u00e7ois Mol\u00e9, 92160 Antony, France"}',
                    [
                        'response_headers' => [
                            'Content-Type' => 'application/json'
                        ]
                    ]
                );
            }

            return new MockResponse('', ['http_code' => 404]);
        });
    }
}
