<?php namespace Celestriode\Mattock\Exceptions\Utils;

use Celestriode\Captain\Exceptions\DynamicCommandExceptionType;
use Celestriode\Captain\Exceptions\DynamicFunctionInterface;
use Celestriode\Captain\Exceptions\SimpleCommandExceptionType;
use Celestriode\Captain\LiteralMessage;
use Celestriode\Captain\MessageInterface;

final class BuiltInUtilsExceptions
{
    public function invalidUuid(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Invalid UUID \'' . $data[0] . '\'');
            }
        });
    }

    public function missingRange(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('Expected numeric value or range');
    }

    public function negativeBounds(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Cannot have a negative boundary: \'' . $data[0] . '\'');
            }
        });
    }

    public function minMaxSwapped(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Min and max values \'' . $data[0] . '\' must be swapped');
            }
        });
    }

    public function missingMinAndMax(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('There must be either a minimum or maximum value specified');
    }

    public function invalidRange(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Invalid range: \'' . $data[0] . '\'');
            }
        });
    }

    public function integerOnly(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('Range values can only be whole numbers');
    }

    public function invalidResourceNamespace(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Invalid resource location namespace: \'' . $data[0] . '\'');
            }
        });
    }

    public function invalidResourcePath(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Invalid resource location path: \'' . $data[0] . '\'');
            }
        });
    }
}