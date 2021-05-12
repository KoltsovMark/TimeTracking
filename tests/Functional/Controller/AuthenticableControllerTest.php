<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\DataFixtures\User\UserFixtures;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticableControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected ?string $jwtToken = null;
    protected ?User $authUser = null;

    protected function getJwtToken(): ?string
    {
        return $this->jwtToken;
    }

    protected function getAuthUser(): ?User
    {
        return $this->authUser;
    }

    protected function setUp(): void
    {
        $this->loadFixtures([
            UserFixtures::class,
        ]);
    }

    protected function makeAuth()
    {
        self::bootKernel();

        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'admin@example.com']);
        $jwtManager = self::$container->get('lexik_jwt_authentication.jwt_manager');
        $this->jwtToken = $jwtManager->create($user);
        $this->authUser = $user;

        self::ensureKernelShutdown();
    }

    protected function decodeResponse(string $raw): array
    {
        $responseContent = \json_decode($raw, true);

        static::assertEquals(\JSON_ERROR_NONE, json_last_error());

        return $responseContent;
    }

    protected function getFaker(): Generator
    {
        return Factory::create();
    }
}
