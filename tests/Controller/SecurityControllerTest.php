<?php

namespace App\Tests\Controller;

use App\Domain\Auth\User;
use App\Tests\NeedLogin;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;
    use NeedLogin;

    public function setUp()
    {
        self::ensureKernelShutdown();
    }

    public function testLoginPage()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testRegisterPage()
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="registration_form"]');
    }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Sign In')->form([
            'username' => 'user',
            'password' => 'password'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/login');

        $client->followRedirect();
        $this->assertSelectorExists('.alert');
    }

    /**
     * @dataProvider userProvider
     * @param User $user
     */
    public function testLoginWithCredentials($user)
    {
        $client = static::createClient();

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            'username' => $user->getUsername(),
            'password' => 'UserPassword1234',
            '_csrf_token' => $csrfToken
        ]);

        $this->assertResponseRedirects('/');
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
