<?php

namespace Heretique\DeriveSDK\Client;

interface DeriveClientInterface
{
    public function authenticate();

    public function isAuthenticated();

    public function getAccessToken();

    public function getUsername();

    public function getDerive($code);
}