<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\Task\Report;

use App\DataFixtures\Test\Task\TaskFixtures;
use App\Tests\Functional\Contract\ValidationAssertsInterface;
use App\Tests\Functional\Controller\AuthenticableControllerTest;
use App\Tests\Functional\Traits\ValidationAssertsTrait;

class CreateReportControllerTest extends AuthenticableControllerTest implements ValidationAssertsInterface
{
    use ValidationAssertsTrait;

    /**
     * @covers \App\Controller\Api\Task\CreateReportController::generateReport
     *
     * @dataProvider dataProviderForGenerateReportSuccess
     */
    public function testGenerateReportSuccess(array $params)
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

        // Check response code
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Compare retrieved data
        $this->assertCount(1, $responseContent);
        $this->assertArrayHasKey('id', $responseContent);
        $this->assertIsInt($responseContent['id']);
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
            ],
            'success report in xlsx' => [
                [
                    'start_date' => '2100-01-01 00:00:00',
                    'end_date' => '2100-01-01 23:59:59',
                    'format' => 'excel',
                ],
            ],
            'success report in pdf' => [
                [
                    'start_date' => '2100-01-01 00:00:00',
                    'end_date' => '2100-01-01 23:59:59',
                    'format' => 'pdf',
                ],
            ],
        ];
    }

    /**
     * @covers \App\Controller\Api\Task\CreateReportController::generateReport
     *
     * @dataProvider dataProviderForGenerateReportFailedValidation
     */
    public function testGenerateReportFailedValidation(array $params, array $expectedValidationErrors)
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

        // Check response code
        $this->assertEquals(422, $client->getResponse()->getStatusCode());

        $this->applyValidationAsserts(
            $expectedValidationErrors,
            $this->decodeResponse($client->getResponse()->getContent())
        );
    }

    public function dataProviderForGenerateReportFailedValidation(): array
    {
        return [
            'all fields blank' => [
                'params' => [],
                'expected response' => [
                    'format' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_BLANK_VALUE,
                        ),
                    ],
                ],
            ],
            'wrong dates format' => [
                'params' => [
                    'start_date' => 'some text',
                    'end_date' => 'some text',
                    'format' => 'csv',
                ],
                'expected response' => [
                    'start_date' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_INVALID_VALUE,
                        ),
                    ],
                    'end_date' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_INVALID_VALUE,
                        ),
                    ],
                ],
            ],
            'wrong data format' => [
                'params' => [
                    'start_date' => '2100-01-01 00:00:00',
                    'end_date' => '2100-01-01 23:59:59',
                    'format' => 'wrong format',
                ],
                'expected response' => [
                    'format' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_INVALID_VALUE,
                        ),
                    ],
                ],
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
