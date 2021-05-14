<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\Task;

use App\Tests\Functional\Contract\ValidationAssertsInterface;
use App\Tests\Functional\Controller\AuthenticableControllerTest;
use App\Tests\Functional\Traits\ValidationAssertsTrait;
use DateTime;
use DateTimeZone;

class CreateControllerTest extends AuthenticableControllerTest implements ValidationAssertsInterface
{
    use ValidationAssertsTrait;

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
    public function testCreateTaskFailedValidation(array $params, array $expectedValidationErrors)
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

        // Check response code
        $this->assertEquals(422, $client->getResponse()->getStatusCode());

        $this->applyValidationAsserts(
            $expectedValidationErrors,
            $this->decodeResponse($client->getResponse()->getContent())
        );
    }

    public function dataProviderForCreateTaskFailedValidation(): array
    {
        return [
            'empty params' => [
                'params' => [],
                'expected validation errors' => [
                    'title' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_BLANK_VALUE
                        ),
                    ],
                    'time_spent' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_BLANK_VALUE
                        ),
                    ],
                    'date' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_BLANK_VALUE
                        ),
                    ],
                ],
            ],
            'max fields values' => [
                'params' => [
                    'title' => \str_pad($this->getFaker()->text(256), 256, 'w'),
                    'comment' => \str_pad($this->getFaker()->text(100001), 100001, 'w'),
                    'time_spent' => 4294967296,
                    'date' => '2011-04-08 00:00:00',
                ],
                'expected validation errors' => [
                    'title' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_MAX_LENGTH_VALUE,
                            [
                                ValidationAssertsInterface::MAX_LENGTH_PARAMETER => 255,
                            ],
                        ),
                    ],
                    'comment' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_MAX_LENGTH_VALUE,
                            [
                                ValidationAssertsInterface::MAX_LENGTH_PARAMETER => 10000,
                            ],
                        ),
                    ],
                    'time_spent' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_MAX_VALUE,
                            [
                                ValidationAssertsInterface::MAX_VALUE_PARAMETER => 4294967295,
                            ],
                        ),
                    ],
                ],
            ],
            'negative time_spent' => [
                'params' => [
                    'title' => 'functional test title',
                    'comment' => 'functional test comment',
                    'time_spent' => -100,
                    'date' => '2011-04-08 00:00:00',
                ],
                'expected validation errors' => [
                    'time_spent' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_POSITIVE_VALUE
                        ),
                    ],
                ],
            ],
            'wrong date format' => [
                'params' => [
                    'title' => 'functional test title',
                    'comment' => 'functional test comment',
                    'time_spent' => 10000,
                    'date' => '08-04-2012 00:00:00',
                ],
                'expected validation errors' => [
                    'date' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_INVALID_VALUE
                        ),
                    ],
                ],
            ],
            'date without time' => [
                'params' => [
                    'title' => 'functional test title',
                     'comment' => 'functional test comment',
                     'time_spent' => 10000,
                     'date' => '08-04-2012',
                ],
                'expected validation errors' => [
                    'date' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_INVALID_VALUE
                        ),
                    ],
                ],
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->makeAuth();
    }
}
