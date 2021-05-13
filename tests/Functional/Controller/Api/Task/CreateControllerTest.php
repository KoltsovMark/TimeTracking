<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\Task;

use App\Tests\Functional\Controller\AuthenticableControllerTest;
use DateTime;
use DateTimeZone;

class CreateControllerTest extends AuthenticableControllerTest
{
    /**
     * @covers \App\Controller\Api\Task\CreateController::createTask
     *
     * @dataProvider dataProviderForCreateTaskSuccess
     */
    public function testCreateTaskSuccess(array $params)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'POST',
            'api/tasks',
            [],
            [],
            [],
            \json_encode($params)
        );

        // Check response status and code
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Compare retrieved data
        $this->assertArrayHasKey('id', $responseContent);

        $formattedDate = (new DateTime($params['date']))->setTimezone(new DateTimeZone('UTC'))->format(DateTime::RFC3339);

        $expectedResponse = [
            'id' => $responseContent['id'],
            'title' => $params['title'],
            'comment' => $params['comment'],
            'time_spent' => $params['time_spent'],
            'date' => $formattedDate,
            'user' => [
                'id' => $this->getAuthUser()->getId(),
                'email' => $this->getAuthUser()->getEmail(),
            ],
        ];

        $this->assertEquals($expectedResponse, $responseContent);
    }

    public function dataProviderForCreateTaskSuccess(): array
    {
        return [
            'date without timezone' => [
                [
                    'title' => 'functional test title',
                    'comment' => 'functional test comment',
                    'time_spent' => 10000,
                    'date' => '2011-04-08 00:00:00',
                ],
            ],
            'date with timezone' => [
                [
                    'title' => 'functional test title',
                    'comment' => 'functional test comment',
                    'time_spent' => 10000,
                    'date' => '2021-04-30 04:22:11 -05:00',
                ],
            ],
        ];
    }

    /**
     * @covers \App\Controller\Api\Task\CreateController::createTask
     *
     * @dataProvider dataProviderForCreateTaskFailedValidation
     */
    public function testCreateTaskFailedValidation(array $params, string $expectedJsonResponse)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'POST',
            'api/tasks',
            [],
            [],
            [],
            \json_encode($params)
        );

        // Check response status and code
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Expected response from API
        $expectedResponse = \json_decode($expectedJsonResponse, true);

        $this->assertEquals($expectedResponse, $responseContent);
    }

    public function dataProviderForCreateTaskFailedValidation(): array
    {
        return [
            'empty params' => [
                [],
                '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "title": { "errors": [ "This value should not be blank." ] }, "comment": {}, "time_spent": { "errors": [ "This value should not be blank." ] }, "date": { "errors": [ "This value should not be blank." ] } } } }',
            ],
            'max fields values' => [
                [
                    'title' => \str_pad($this->getFaker()->text(256), 256, 'w'),
                    'comment' => \str_pad($this->getFaker()->text(100001), 100001, 'w'),
                    'time_spent' => 4294967296,
                    'date' => '2011-04-08 00:00:00',
                ],
                '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "title": { "errors": [ "This value is too long. It should have 255 characters or less." ] }, "comment": { "errors": [ "This value is too long. It should have 10000 characters or less." ] }, "time_spent": { "errors": [ "This value should be 4294967295 or less." ] }, "date": {} } } }',
            ],
            'negative time_spent' => [
                [
                    'title' => 'functional test title',
                    'comment' => 'functional test comment',
                    'time_spent' => -100,
                    'date' => '2011-04-08 00:00:00',
                ],
                '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "title": {}, "comment": {}, "time_spent": { "errors": [ "This value should be positive." ] }, "date": {} } } }',
            ],
            'wrong date format' => [
                [
                    'title' => 'functional test title',
                    'comment' => 'functional test comment',
                    'time_spent' => 10000,
                    'date' => '08-04-2012 00:00:00',
                ],
                '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "title": {}, "comment": {}, "time_spent": {}, "date": { "errors": [ "This value is not valid." ] } } } }',
            ],
            'date without time' => [
                ['title' => 'functional test title',
                 'comment' => 'functional test comment',
                 'time_spent' => 10000,
                 'date' => '08-04-2012',
                ],
                '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "title": {}, "comment": {}, "time_spent": {}, "date": { "errors": [ "This value is not valid." ] } } } }',
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->makeAuth();
    }
}
