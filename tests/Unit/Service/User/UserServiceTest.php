<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\User;

use App\DataFixtures\Test\User\UserFixtures;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserServiceTest extends KernelTestCase
{
    use FixturesTrait;

    private UserRepository $userRepositoryMock;

    /**
     * @covers \App\Service\User\UserService::isEmailExist
     *
     * @dataProvider dataProviderForTestIsEmailExist
     */
    public function testIsEmailExist(string $email, bool $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->getUserService()->isEmailExist($email));
    }

    public function dataProviderForTestIsEmailExist(): array
    {
        return [
            [
                'email' => 'user@example.com',
                'email exists' => true,
            ],
            [
                'email' => 'user@example-do-not-exist.com',
                'email exists' => false,
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = $this->createMock(UserRepository::class);

        $this->loadFixtures([
            UserFixtures::class,
        ]);
    }

    protected function getUserService()
    {
        return $this::$container->get(UserService::class);
    }
}
