<?php
namespace Riskio\Authentication\Auth0;

use Exception;
use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Provider\ProviderInterface;
use Riskio\Authentication\Auth0\OAuth2Result;
use Zend\Authentication\Adapter\AdapterInterface;

class Adapter implements AdapterInterface
{
    /**
     * @var ProviderInterface
     */
    private $oauthProvider;

    /**
     * @var string
     */
    private $code;

    /**
     * @param ProviderInterface $oauthProvider
     */
    public function __construct(ProviderInterface $oauthProvider)
    {
        $this->oauthProvider = $oauthProvider;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = (string) $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate()
    {
        if (empty($this->code)) {
            return new OAuth2Result(
                OAuth2Result::FAILURE_CREDENTIAL_INVALID,
                null,
                ['No code specified']
            );
        }

        try {
            $token = $this->getAccessToken();

            /* @var $user \League\OAuth2\Client\Entity\User */
            $user = $this->oauthProvider->getUserDetails($token);
            if (!$user) {
                return new OAuth2Result(
                    OAuth2Result::FAILURE_IDENTITY_NOT_FOUND,
                    $this->code,
                    [
                        sprintf(
                            'Failed to retrieve user related to access token "%s"',
                            $token
                        )
                    ]
                );
            }

            $result = new OAuth2Result(OAuth2Result::SUCCESS, $user);
            $result->setAccessToken($token);

            return $result;
        } catch (Exception $e) {
            return new OAuth2Result(
                OAuth2Result::FAILURE_CREDENTIAL_INVALID,
                $this->code,
                [$e->getMessage()]
            );
        }
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    private function getAccessToken()
    {
        $grant = new AuthorizationCode();

        return $this->oauthProvider->getAccessToken($grant, [
            'code' => $this->code,
        ]);
    }
}