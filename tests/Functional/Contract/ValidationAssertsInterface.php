<?php

declare(strict_types=1);

namespace App\Tests\Functional\Contract;

interface ValidationAssertsInterface
{
    // Available constraint message types
    public const VALIDATION_FAILED = 'failed';
    public const CONSTRAINT_TYPE_INVALID_VALUE = 'invalid';
    public const CONSTRAINT_TYPE_BLANK_VALUE = 'blank';
    public const CONSTRAINT_TYPE_POSITIVE_VALUE = 'positive';
    public const CONSTRAINT_TYPE_MAX_LENGTH_VALUE = 'max_length';
    public const CONSTRAINT_TYPE_MAX_VALUE = 'max_value';
    public const CONSTRAINT_TYPE_UNDEFINED = 'undefined';

    // Available constraint messages
    public const CONSTRAINT_MESSAGES = [
        self::VALIDATION_FAILED => 'Validation Failed',
        self::CONSTRAINT_TYPE_INVALID_VALUE => 'This value is not valid.',
        self::CONSTRAINT_TYPE_BLANK_VALUE => 'This value should not be blank.',
        self::CONSTRAINT_TYPE_POSITIVE_VALUE => 'This value should be positive.',
        self::CONSTRAINT_TYPE_MAX_LENGTH_VALUE => 'This value is too long. It should have max_length characters or less.',
        self::CONSTRAINT_TYPE_MAX_VALUE => 'This value should be max_value or less.',
        self::CONSTRAINT_TYPE_UNDEFINED => 'Constraint is undefined',
    ];

    // Available message parameters for replacement
    public const MAX_LENGTH_PARAMETER = 'max_length';
    public const MAX_VALUE_PARAMETER = 'max_value';

    /**
     * Apply validation constraints.
     */
    public function applyValidationAsserts(array $expectedResult, array $requestContent): void;

    /**
     * Return expected validation message.
     */
    public function getExpectedConstraintMessage(string $messageType, array $namedParams = []): string;
}
