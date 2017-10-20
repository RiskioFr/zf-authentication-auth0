<?php
namespace Riskio\Authentication\Auth0;

use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class AdapterTest extends TestCase
{
    /**
     * @test
     */
    public function authenticate_GivenCodeAndValidIdentity_ShouldReturnSuccessResult()
    {
        $accessToken = 'abcdef';
        $resourceOwnerId = 123;
        $code = 123;

        $token = new AccessToken(['access_token' => $accessToken]);
        $resourceOwner = $this->createResourceOwner($resourceOwnerId);
        $providerMock = $this->getOauthProvider($token, $resourceOwner);
        $adapter = new Adapter($providerMock->reveal());
        $adapter->setCode($code);

        $result = $adapter->authenticate();

        self::assertInstanceOf(OAuth2Result::class, $result);
        self::assertSame(OAuth2Result::SUCCESS, $result->getCode());
        self::assertSame($token, $result->getAccessToken());
        self::assertSame($resourceOwner, $result->getIdentity());
    }

    private function getOauthProvider($token, $resourceOwner)
    {
        $providerMock = $this->prophesize(AbstractProvider::class);
        $providerMock
            ->getAccessToken(Argument::type(AuthorizationCode::class), Argument::type('array'))
            ->willReturn($token);
        $providerMock
            ->getResourceOwner($token)
            ->willReturn($resourceOwner);

        return $providerMock;
    }

    private function createResourceOwner(int $resourceOwnerId) : GenericResourceOwner
    {
        return new GenericResourceOwner([], $resourceOwnerId);
    }
}
