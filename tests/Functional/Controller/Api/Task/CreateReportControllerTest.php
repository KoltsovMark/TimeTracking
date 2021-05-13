<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\Task;

use App\DataFixtures\Test\Task\TaskFixtures;
use App\Tests\Functional\Controller\AuthenticableControllerTest;

class CreateReportControllerTest extends AuthenticableControllerTest
{
    /**
     * @covers \App\Controller\Api\Task\CreateReportController::generateReport
     *
     * @dataProvider dataProviderForGenerateReportSuccess
     */
    public function testGenerateReportSuccess(array $params, string $expectedExtension)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'POST',
            'api/tasks/report',
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
        $this->assertCount(2, $responseContent);
        $this->assertArrayHasKey('content', $responseContent);
        $this->assertEquals($expectedExtension, $responseContent['extension']);
    }

    public function dataProviderForGenerateReportSuccess(): array
    {
        return [
            'success report in csv' => [
                [
                    'start_date' => '2100-01-01 00:00:00',
                    'end_date' => '2100-01-01 23:59:59',
                    'format' => 'csv',
                ],
                'expected_extension' => 'csv',
            ],
            'success report in xlsx' => [
                [
                    'start_date' => '2100-01-01 00:00:00',
                    'end_date' => '2100-01-01 23:59:59',
                    'format' => 'excel',
                ],
                'expected_extension' => 'xlsx',
            ],
            'success report in pdf' => [
                [
                    'start_date' => '2100-01-01 00:00:00',
                    'end_date' => '2100-01-01 23:59:59',
                    'format' => 'pdf',
                ],
                'expected_extension' => 'pdf',
            ],
        ];
    }

    /**
     * @covers \App\Controller\Api\Task\CreateReportController::generateReport
     *
     * @dataProvider dataProviderForGenerateReportFailedValidation
     */
    public function testGenerateReportFailedValidation(array $params, string $expectedJsonResponse)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'POST',
            'api/tasks/report',
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

    public function dataProviderForGenerateReportFailedValidation(): array
    {
        return [
            'all fields blank' => [
                'params' => [],
                'expected response' => '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "start_date": {}, "end_date": {}, "format": { "errors": [ "This value should not be blank." ] } } } }',
            ],
            'wrong dates format' => [
                'params' => [
                    'start_date' => 'some text',
                    'end_date' => 'some text',
                    'format' => 'csv',
                ],
                'expected response' => '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "start_date": { "errors": [ "This value is not valid." ] }, "end_date": { "errors": [ "This value is not valid." ] }, "format": {} } } }',
            ],
            'wrong data format' => [
                'params' => [
                    'start_date' => '2100-01-01 00:00:00',
                    'end_date' => '2100-01-01 23:59:59',
                    'format' => 'wrong format',
                ],
                'expected response' => '{ "code": 400, "message": "Validation Failed", "errors": { "children": { "start_date": {}, "end_date": {}, "format": { "errors": [ "This value is not valid." ] } } } }',
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
