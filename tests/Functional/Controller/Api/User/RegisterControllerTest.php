<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\User;

use App\DataFixtures\Test\User\UserFixtures;
use App\Tests\Functional\Contract\ValidationAssertsInterface;
use App\Tests\Functional\Controller\AuthenticableControllerTest;
use App\Tests\Functional\Traits\ValidationAssertsTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class RegisterControllerTest extends AuthenticableControllerTest implements ValidationAssertsInterface
{
    use FixturesTrait;
    use ValidationAssertsTrait;

    /**
     * @covers \App\Controller\Api\User\RegisterController::register
     *
     * @dataProvider dataProviderForRegisterSuccess
     */
    public function testRegisterSuccess(string $email, string $password)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->request(
            'POST',
            'api/users/register',
            [],
            [],
            [],
            \json_encode([
                'email' => $email,
                'password' => [
                    'first' => $password,
                    'second' => $password,
                ],
            ])
        );

        // Check response code
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        //Check headers
        $responseHeaders = $client->getResponse()->headers;
        $this->assertTrue($responseHeaders->has('content-location'));

        // Check response status
        $responseContent = $this->decodeResponse($client->getResponse()->getContent());

        // Compare retrieved data
        $this->assertCount(2, $responseContent);
        $this->assertArrayHasKey('id', $responseContent);
        $this->assertIsInt($responseContent['id']);
        $this->assertEquals($email, $responseContent['email']);
    }

    public function dataProviderForRegisterSuccess(): array
    {
        return [
            [
                'email' => 'adminnewemail@example.com',
                'password' => 'correct$password1',
            ],
            [
                'email' => 'adminemail@example.com',
                'password' => 'correct$password1',
            ],
        ];
    }

    /**
     * @covers \App\Controller\Api\User\RegisterController::register
     *
     * @dataProvider dataProviderForRegisterFailedValidation
     */
    public function testRegisterFailedValidation(array $params, array $expectedValidationErrors)
    {
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        $client = self::createClient([], $headers);

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $this->getJwtToken()));

        $client->request(
            'POST',
            'api/users/register',
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

    public function dataProviderForRegisterFailedValidation(): array
    {
        $passwordMaxLength = 'a1A$'.\str_pad($this->getFaker()->text(252), 256, 'w');
        $email = $this->getFaker()->email;

        return [
            'all fields blank' => [
                'params' => [],
                'expected response' => [
                    'email' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_BLANK_VALUE,
                        ),
                    ],
                    'password' => [
                        'first' => [
                            $this->getExpectedConstraintMessage(
                                ValidationAssertsInterface::CONSTRAINT_TYPE_BLANK_VALUE,
                            ),
                        ],
                    ],
                ],
            ],
            'email exists' => [
                'params' => [
                    'email' => 'admin@example.com',
                    'password' => [
                        'first' => 'correct$password1',
                        'second' => 'correct$password1',
                    ],
                ],
                'expected response' => [
                    'email' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_EMAIL_EXISTS,
                        ),
                    ],
                    'password' => [],
                ],
            ],
            'passwords and password confirmation not equal' => [
                'params' => [
                    'email' => 'adminnewemail@example.com',
                    'password' => [
                        'first' => 'correct$password1',
                        'second' => 'correct$password2',
                    ],
                ],
                'expected response' => [
                    'password' => [
                        'first' => [
                            $this->getExpectedConstraintMessage(
                                ValidationAssertsInterface::CONSTRAINT_TYPE_INVALID_VALUE,
                            ),
                        ],
                    ],
                ],
            ],
            'passwords mismatch regex' => [
                'params' => [
                    'email' => 'adminnewemail@example.com',
                    'password' => [
                        'first' => 'wrong',
                        'second' => 'wrong',
                    ],
                ],
                'expected response' => [
                    'password' => [
                        'first' => [
                            $this->getExpectedConstraintMessage(
                                ValidationAssertsInterface::CONSTRAINT_TYPE_WRONG_PASSWORD_REGEX,
                            ),
                        ],
                    ],
                ],
            ],
            'max length constraints' => [
                'params' => [
                    'email' => \str_pad('', 181 - strlen($email), 'w').$email,
                    'password' => [
                        'first' => $passwordMaxLength,
                        'second' => $passwordMaxLength,
                    ],
                ],
                'expected response' => [
                    'email' => [
                        $this->getExpectedConstraintMessage(
                            ValidationAssertsInterface::CONSTRAINT_TYPE_MAX_LENGTH_VALUE,
                            [ValidationAssertsInterface::MAX_LENGTH_PARAMETER => 180]
                        ),
                    ],
                    'password' => [
                        'first' => [
                            $this->getExpectedConstraintMessage(
                                ValidationAssertsInterface::CONSTRAINT_TYPE_MAX_LENGTH_VALUE,
                                [ValidationAssertsInterface::MAX_LENGTH_PARAMETER => 255]
                            ),
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            UserFixtures::class,
        ]);
        self::ensureKernelShutdown();
    }
}
