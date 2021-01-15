<?php

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    public function testCountUsers()
    {
        $users = $this->loadFixtureFiles([
            dirname(__DIR__) . '/fixtures/Users.yaml'
        ]);

        $users = self::$container->get(UserRepository::class)->count([]);
        $this->assertEquals(12, $users);
    }
}
