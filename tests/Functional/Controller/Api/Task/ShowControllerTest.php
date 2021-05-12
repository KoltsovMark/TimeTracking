<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\Task;

use App\DataFixtures\Test\Task\TaskFixtures;
use App\Tests\Functional\Controller\AuthenticableControllerTest;

class ShowControllerTest extends AuthenticableControllerTest
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

    /**
     * @covers \App\Controller\Api\Task\ShowController::taskShow
     *
     * @dataProvider dataProviderForTaskShowSuccess
     */
    public function testTaskShowSuccess(int $taskId)
    {
        $expectedUser = [
            'id' => $this->getAuthUser()->getId(),
            'email' => $this->getAuthUser()->getEmail(),
        ];

        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'GET',
            "api/tasks/{$taskId}",
        );

        // Check response status and code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Compare retrieved data
        $this->assertCount(6, $responseContent);

        $this->assertEquals($taskId, $responseContent['id']);
        $this->assertEquals($expectedUser, $responseContent['user']);

        $this->assertArrayHasKey('title', $responseContent);
        $this->assertArrayHasKey('comment', $responseContent);
        $this->assertArrayHasKey('time_spent', $responseContent);
        $this->assertArrayHasKey('date', $responseContent);
    }

    public function dataProviderForTaskShowSuccess(): array
    {
        return [
            'test with auth user, task id 1' => [
                'id' => 1,
            ],
            'test with auth user, task id 10' => [
                'id' => 10,
            ],
        ];
    }

    /**
     * @covers \App\Controller\Api\Task\ShowController::taskShow
     *
     * @dataProvider dataProviderForTaskShowErrors
     */
    public function testTaskShowErrors(int $taskId, int $expectedCode, string $expectedTitle, string $expectedMessage)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'GET',
            "api/tasks/{$taskId}",
        );

        // Check response status and code
        $this->assertEquals($expectedCode, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Compare retrieved data
        $this->assertEquals($expectedTitle, $responseContent['title']);
        $this->assertEquals($expectedMessage, $responseContent['detail']);
    }

    public function dataProviderForTaskShowErrors(): array
    {
        return [
            'task do not belong user, task id 16' => [
                'id' => 16,
                'code' => 403,
                'title' => 'An error occurred',
                'message' => 'Access Denied.',
            ],
            'task do not exist' => [
                'id' => 9999,
                'code' => 404,
                'title' => 'An error occurred',
                'message' => 'App\\Entity\\Task object not found by the @ParamConverter annotation.',
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
