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
            'list 2-nd page with 50 items per page' => [
                'params' => [
                    'page' => 2,
                    'limit' => 50,
                ],
                'items on page' => 0,
                'total items' => 15,
            ],
        ];
    }

    /**
     * @covers \App\Controller\Api\Task\ShowController::tasksList
     *
     * @dataProvider dataProviderForTasksListWithFailedValidation
     */
    public function testTasksListWithFailedValidation(array $params, string $expectedJsonResponse)
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
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Expected response from API
        $expectedResponse = \json_decode($expectedJsonResponse, true);

        // Compare retrieved data
        $this->assertEquals($expectedResponse, $responseContent);
    }

    public function dataProviderForTasksListWithFailedValidation(): array
    {
        return [
            'limit not in allowed limits' => [
                [
                    'page' => 1,
                    'limit' => 5,
                ],
                '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "page": {}, "limit": { "errors": [ "This value is not valid." ] } } } }',
            ],
            'inject in page' => [
                [
                    'page' => 'UNION SELECT email, password FROM users',
                    'limit' => 10,
                ],
                '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "page": { "errors": [ "This value is not valid." ] }, "limit": {} } } }',
            ],
            'inject in limit' => [
                [
                    'limit' => 'UNION SELECT email, password FROM users',
                    'page' => 1,
                ],
                '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "page": {}, "limit": { "errors": [ "This value is not valid." ] } } } }',
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
