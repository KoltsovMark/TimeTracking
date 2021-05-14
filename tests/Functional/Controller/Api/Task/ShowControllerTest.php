<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\Task;

use App\DataFixtures\Test\Task\TaskFixtures;
use App\Tests\Functional\Controller\AuthenticableControllerTest;

class ShowControllerTest extends AuthenticableControllerTest
{
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

        // Check response code
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
    public function testTaskShowErrors($taskId, int $expectedCode, string $expectedTitle, string $expectedMessage)
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

        // Check response code
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
                'message' => 'App\Entity\Task\Task object not found by the @ParamConverter annotation.',
            ],
            'inject SQL in id' => [
                'id' => 'UNION SELECT email, password FROM users',
                'code' => 404,
                'title' => 'An error occurred',
                'message' => 'No route found for "GET /api/tasks/UNION SELECT email, password FROM users"',
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
