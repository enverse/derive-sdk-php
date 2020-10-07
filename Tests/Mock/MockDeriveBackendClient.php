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
            return $this->signupController($method, $url, $options);
            } elseif ($url == 'https://derive-api.altertopia.co/auth' && $method == 'POST') {
                return $this->authController($method, $url, $options);
            } elseif ('https://derive-api.altertopia.co/derive' == $url) {
                return $this->createDeriveController($method, $url, $options);
            } elseif (preg_match('/https:\/\/derive-api\.altertopia\.co\/geocode\/forward/', $url)) {
                return $this->forwardGeocodeController($method, $url, $options);
            } elseif (preg_match('/^https:\/\/derive-api\.altertopia\.co\/derive\/[aA0-zZ9]{6}/', $url)) {
                return $this->getDeriveController($method, $url, $options);
            }

            return new MockResponse('', ['http_code' => 404]);
        });
    }

    private function signupController($method, $url, $options)
    {
        $body = json_decode($options['body']);
        if (!isset($body->uuid)) {
            return new MockResponse('', ['http_code' => 400]);
        }
        $this->registeredUsername = 'uuid::' . $body->uuid;
        return new MockResponse('{"username":"' . $this->registeredUsername . '"}', [
            'response_headers' => [
                'Content-Type' => 'application/json'
            ],
            'http_code' => 261
        ]);
    }

    private function authController($method, $url, $options)
    {
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
    }

    private function getDeriveController($method, $url, $options)
    {
        if (!DeriveClientTestUtils::testAuthorizationHeaderIsSet($options['headers'], 'secretaccesstoken')) {
            return new MockResponse('', ['http_code' => 401]);
        }

        return new MockResponse(
            json_encode([
                'address' => [
                    'text' => [
                        'street' => '78 rue de la roche',
                        'city' => 'Poitiers',
                        'country' => 'France'
                    ],
                    'position' => [
                        'lat' => '48.8989',
                        'lng' => '2.978'
                    ]
                ],
                'message' => 'Quelque part au milieu de nul part',
                'code' => 'MMMMMM'
            ]),
            [
                'response_headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]
        );
    }

    private function createDeriveController($method, $url, $options)
    {
        if (!DeriveClientTestUtils::testAuthorizationHeaderIsSet($options['headers'], 'secretaccesstoken')) {
            return new MockResponse('', ['http_code' => 401]);
        }

        $body = json_decode($options['body']);

        if ($body->address && $body->message && $body->reveal_address) {
            return new MockResponse(json_encode(['code' => 'CCCCCC']), [
                'response_headers' => [
                    'Content-Type' => 'application/json'
                ],
                'http_code' => 201
            ]);
        }

        return new MockResponse('', ['http_code' => 400]);
    }

    private function forwardGeocodeController($method, $url, $options)
    {
        if (!DeriveClientTestUtils::testAuthorizationHeaderIsSet($options['headers'], 'secretaccesstoken')) {
            return new MockResponse('', ['http_code' => 401]);
        }

        $address = $options['query']['address'];

        if (!$address) {
            return new MockResponse('', ['http_code', 400]);
        }

        return new MockResponse(
            '{"Avenue Fran\u00e7ois Mol\u00e9, Verri\u00e8res-le-Buisson, 91370, France": {"text": {"prefix": null, "street": "Avenue Fran\u00e7ois Mol\u00e9", "city": "Verri\u00e8res-le-Buisson", "postal_code": "91370", "country": "France"}, "position": {"lat": 48.7453765, "lng": 2.2832358}}}',
            [
                'response_headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]
                );
    }
}
