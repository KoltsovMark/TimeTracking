<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\Task\Report;

use App\DataFixtures\Test\Task\Report\TasksReportFixtures;
use App\Tests\Functional\Contract\ValidationAssertsInterface;
use App\Tests\Functional\Controller\AuthenticableControllerTest;
use App\Tests\Functional\Traits\ValidationAssertsTrait;

class ShowReportControllerTest extends AuthenticableControllerTest implements ValidationAssertsInterface
{
    use ValidationAssertsTrait;

    /**
     * @covers \App\Controller\Api\Task\CreateReportController::generateReport
     *
     * @dataProvider dataProviderForReportShowSuccess
     */
    public function testReportShowSuccess(string $expectedExtension)
    {
        $id = $this->createReport(
            [
                'start_date' => '2100-01-01 00:00:00',
                'end_date' => '2100-01-01 23:59:59',
                'format' => $expectedExtension,
            ]
        );

        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'GET',
            "api/tasks/report/{$id}",
        );

        // Check response code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Compare retrieved data
        $this->assertCount(2, $responseContent);
        $this->assertArrayHasKey('content', $responseContent);
        $this->assertEquals($expectedExtension, $responseContent['extension']);
    }

    public function dataProviderForReportShowSuccess(): array
    {
        return [
            'success report in csv' => [
                'expected_extension' => 'csv',
            ],
        ];
    }

    /**
     * @covers \App\Controller\Api\Task\ShowController::show
     *
     * @dataProvider dataProviderForReportShowErrors
     */
    public function testTaskShowErrors($id, int $expectedCode, string $expectedTitle, string $expectedMessage)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'GET',
            "api/tasks/report/{$id}",
        );

        // Check response code
        $this->assertEquals($expectedCode, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Compare retrieved data
        $this->assertEquals($expectedTitle, $responseContent['title']);
        $this->assertEquals($expectedMessage, $responseContent['detail']);
    }

    public function dataProviderForReportShowErrors(): array
    {
        return [
            'report do not belong user, task id 16' => [
                'id' => 5,
                'code' => 403,
                'title' => 'An error occurred',
                'message' => 'Access Denied.',
            ],
            'report do not exist' => [
                'id' => 9999,
                'code' => 404,
                'title' => 'An error occurred',
                'message' => 'App\Entity\Task\TasksReport object not found by the @ParamConverter annotation.',
            ],
            'report file do not exist' => [
                'id' => 1,
                'code' => 404,
                'title' => 'An error occurred',
                'message' => 'Not Found',
            ],
            'inject SQL in id' => [
                'id' => 'UNION SELECT email, password FROM users',
                'code' => 404,
                'title' => 'An error occurred',
                'message' => 'No route found for "GET /api/tasks/report/UNION SELECT email, password FROM users"',
            ],
        ];
    }

    /**
     * Create a new report on a storage, needed for show success response.
     */
    protected function createReport(array $params): int
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

        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        return $responseContent['id'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            TasksReportFixtures::class,
        ], true);

        $this->makeAuth();
    }
}
