<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\Task\TaskFixtures;
use App\DataFixtures\User\UserFixtures;
use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ApplicationAvailabilityFunctionalTest
 * @package App\Tests\Functional
 */
class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @var string 
     */
    protected string $jwtToken;

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful(
        string $url,
        string $method,
        bool $needAuth,
        array $params = []
    ) {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        if ($needAuth) {
            $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->jwtToken));
        }

        $client->request(
            $method,
            $url,
            [],
            [],
            [],
            \json_encode($params)
        );

        if ($client->getResponse()->getStatusCode() != 201 && $client->getResponse()->isRedirect())
        {
            $client->followRedirect();
        }

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @return array[]
     */
    public function urlProvider(): array
    {
        return [
            [
                'url' => 'api/login',
                'method' => 'GET',
                'auth' => false,
                'params' => [
                    'username' => 'admin@example.com',
                    'password' => '12345qwerty',
                ]
            ],
            [
                'url' => 'api/tasks',
                'method' => 'GET',
                'auth' => true,
                'params' => [
                    'page' => 1,
                ]
            ],
            [
                'url' => 'api/tasks/1',
                'method' => 'GET',
                'auth' => true,
            ],
            [
                'url' => 'api/tasks/create',
                'method' => 'POST',
                'auth' => true,
                'params' => [
                    'title' => 'smoke test title',
                    'comment' => 'smoke test comment',
                    'time_spent' => 10,
                    'date' => '2011-04-08 00:00:00',
                ]
            ],
            [
                'url' => 'api/tasks/report/generate',
                'method' => 'POST',
                'auth' => true,
                'params' => [
                    'start_date' => '2100-01-01 00:00:00',
                    'end_date' => '2100-01-01 23:59:59',
                    'format' => 'pdf',
                ]
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->loadFixtures([
           UserFixtures::class,
           TaskFixtures::class,
        ]);

        self::bootKernel();

        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'admin@example.com']);
        $jwtManager = self::$container->get('lexik_jwt_authentication.jwt_manager');
        $this->jwtToken = $jwtManager->create($user);

        self::ensureKernelShutdown();
    }
}