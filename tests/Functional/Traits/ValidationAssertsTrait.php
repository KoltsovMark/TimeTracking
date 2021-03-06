<?php

declare(strict_types=1);

namespace App\Tests\Functional\Traits;

use App\Tests\Functional\Contract\ValidationAssertsInterface;

trait ValidationAssertsTrait
{
    /**
     * Compare validation response code, message and fields with expected result.
     */
    public function applyValidationAsserts(array $expectedResult, array $requestContent): void
    {
        // Check response code and message
        $this->assertEquals(422, $requestContent['code']);
        $this->assertEquals(
            $this->getExpectedConstraintMessage(ValidationAssertsInterface::VALIDATION_FAILED),
            $requestContent['message']
        );

        // Check validation
        $this->validateField('request', $requestContent['errors'], ['request' => $expectedResult]);
    }

    protected function validateField(string $fieldName, array $fieldData, array $expectedResult)
    {
        if (\array_key_exists('children', $fieldData)) {
            $resultErrors = \array_filter($fieldData['children']);

            // Check that count of errors and expected errors are equal, no missed fields
            $this->assertCount(count($expectedResult[$fieldName]), $resultErrors);

            foreach ($resultErrors as $subFieldName => $fieldDetails) {
                $this->validateField($subFieldName, $fieldDetails, [$subFieldName => $expectedResult[$fieldName][$subFieldName]]);
            }
        } else {
            $errors = $fieldData['errors'];
            $expectedErrors = $expectedResult[$fieldName] ?? [];

            // Check error messages count
            $this->assertCount(count($expectedErrors), $errors);

            sort($errors);
            sort($expectedErrors);

            foreach ($errors as $error) {
                $expectedError = \array_shift($expectedErrors);
                $this->assertEquals($expectedError, $error);
            }
        }
    }

    public function getExpectedConstraintMessage(string $messageType, array $namedParams = []): string
    {
        $message = ValidationAssertsInterface::CONSTRAINT_MESSAGES[$messageType] ?? ValidationAssertsInterface::CONSTRAINT_MESSAGES[ValidationAssertsInterface::CONSTRAINT_TYPE_UNDEFINED];

        return (string) \str_replace(array_keys($namedParams), $namedParams, $message);
    }
}
