<?php
namespace Riskio\Authentication\Auth0;

use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Grant\AuthorizationCode;
use League\OAuth2\Client\Provider\ProviderInterface;
use League\OAuth2\Client\Token\AccessToken;
use Prophecy\Argument;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function authenticate_GivenCodeAndValidIdentity_ShouldReturnSuccessResult()
    {
        $code  = 123;
        $token = $this->prophesize(AccessToken::class);
        $user  = new User();
        $providerMock = $this->getOauthProvider($token, $user);
        $adapter = new Adapter($providerMock->reveal());
        $adapter->setCode($code);

        $result = $adapter->authenticate();

        $this->assertInstanceOf(OAuth2Result::class, $result);
        $this->assertEquals(OAuth2Result::SUCCESS, $result->getCode());
        $this->assertInstanceOf(AccessToken::class, $result->getAccessToken());
        $this->assertEquals($user, $result->getIdentity());
    }

    private function getOauthProvider($token, $user)
    {
        $providerMock = $this->prophesize(ProviderInterface::class);
        $providerMock
            ->getAccessToken(Argument::type(AuthorizationCode::class), Argument::type('array'))
            ->willReturn($token->reveal());
        $providerMock->getUserDetails(Argument::type(AccessToken::class))->willReturn($user);

        return $providerMock;
    }
}
