<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\Task;

use App\DataFixtures\Test\Task\TaskFixtures;
use App\Tests\Functional\Controller\AuthenticableControllerTest;

class ListControllerTest extends AuthenticableControllerTest
{
    /**
     * @covers \App\Controller\Api\Task\ShowController::tasksList
     *
     * @dataProvider dataProviderForTasksListSuccess
     */
    public function testTasksListSuccess(array $params, int $expectedItemsCount, int $expectedTotalItems)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'GET',
            'api/tasks',
            $params
        );

        // Check response status and code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Compare retrieved data
        $this->assertEquals($params['page'] ?? 1, $responseContent['page']);
        $this->assertEquals($params['limit'] ?? 10, $responseContent['per_page']);
        $this->assertArrayHasKey('items', $responseContent);
        $this->assertCount($expectedItemsCount, $responseContent['items']);
        $this->assertEquals($expectedTotalItems, $responseContent['total_items']);
    }

    public function dataProviderForTasksListSuccess(): array
    {
        return [
            'list without request parameters' => [
                'params' => [],
                'items on page' => 10,
                'total items' => 15,
            ],
            'list 1 page with 15 items per page' => [
                'params' => [
                    'limit' => 15,
                ],
                'items on page' => 15,
                'total items' => 15,
            ],
            'list 2-nd page with 5 items per page' => [
                'params' => [
                    'page' => 2,
                    'limit' => 5,
                ],
                'items on page' => 5,
                'total items' => 15,
            ],
        ];
    }

    /**
     * @covers \App\Controller\Api\Task\ShowController::tasksList
     *
     * @dataProvider dataProviderForTasksListWithSqlInjection
     */
    public function testTasksListWithSqlInjection(array $params, string $expectedMessage)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'GET',
            'api/tasks',
            $params
        );

        // Check response status and code
        $this->assertEquals(500, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Compare retrieved data
        $this->assertEquals('An error occurred', $responseContent['title']);
        $this->assertEquals($expectedMessage, $responseContent['detail']);
        $this->assertEquals('LogicException', $responseContent['class']);
    }

    public function dataProviderForTasksListWithSqlInjection(): array
    {
        return [
            'inject in page' => [
                [
                    'page' => 'UNION SELECT email, password FROM users',
                    'limit' => 10,
                ],
                'Invalid item per page number. Limit: 10 and Page: 0, must be positive non-zero integers',
            ],
            'inject in limit' => [
                [
                    'limit' => 'UNION SELECT email, password FROM users',
                    'page' => 1,
                ],
                'Invalid item per page number. Limit: 0 and Page: 1, must be positive non-zero integers',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            TaskFixtures::class,
        ], true);

        $this->makeAuth();
    }
}
