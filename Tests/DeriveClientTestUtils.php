<?php

namespace Heretique\DeriveSDK\Tests;

use Symfony\Component\HttpClient\Response\MockResponse;

class DeriveClientTestUtils
{
    const API_KEY = 'apisecretkey';

    /**
     * Tests wether the content type header has been set on the MockHttpClient
     *
     * @param array $headers
     * @return bool
     * @throws \Exception
     */
    public static function testContentTypeHeaderIsSet($headers)
    {
        foreach ($headers as $header) {
            if ($header == 'Content-Type: application/json') return true;
        }
        return false;
    }

    /**
     * Tests wether the content type header has been set on the MockHttpClient
     *
     * @param array $headers
     * @return bool
     * @throws \Exception
     */
    public static function testXAppVersionHeaderIsSet($headers)
    {
        foreach ($headers as $header) {
            if ($header == 'X-App-Version: derive-random') return true;
        }
        return false;
    }

    public static function testSignUpBody($body)
    {
        if ($body == '"{"uuid":"motdepasse"}"') {
            return true;
        }
        return false;
    }

    public static function testAuthorizationHeaderIsSet($headers, $accessToken)
    {
        $matches = [];
        foreach ($headers as $header) {
            if (preg_match('/^Authorization: JWT (.+)/', $header, $matches)) {
                $sentAccessToken = $matches[1];

                return $accessToken == $sentAccessToken;
            };
        }
        return false;
    }
}