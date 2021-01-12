<?php

namespace App\Tests\Controller;

use App\Tests\NeedLogin;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PagesControllerTest extends WebTestCase
{
    use FixturesTrait;
    use NeedLogin;

    public function setUp()
    {
        self::ensureKernelShutdown();
    }

    public function testHomepage()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h1', 'Create your own quizz and play with.');
    }

    public function testNavWithNoAccount()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('#sign a:first-of-type', 'Sign in');
        $this->assertSelectorTextSame('#sign a:last-of-type', 'Sign up');
    }

    /**
     * @dataProvider userProvider
     */
    public function testNavWithConnectedAccount($user)
    {
        $client = static::createClient();

        $this->login($client, $user);

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('#sign a:first-of-type', 'My account');
        $this->assertSelectorTextSame('#sign a:last-of-type', 'Logout');
    }

    public function userProvider(): iterable
    {
        $users = $this->loadFixtureFiles([
            dirname(__DIR__) . '/fixtures/Users.yaml'
        ]);

        yield [$users['user_user']];
        yield [$users['user_admin']];
    }
}
