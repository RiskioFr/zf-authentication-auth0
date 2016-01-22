<?php
namespace Riskio\Authentication\Auth0;

use League\OAuth2\Client\Token\AccessToken;
use Zend\Authentication\Result;

class OAuth2Result extends Result
{
    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * @param AccessToken $accessToken
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
