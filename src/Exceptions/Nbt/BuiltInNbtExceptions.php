<?php namespace Celestriode\Mattock\Exceptions\Nbt;

use Celestriode\Captain\MessageInterface;
use Celestriode\Captain\Exceptions\Dynamic2CommandExceptionType;
use Celestriode\Captain\Exceptions\DynamicFunctionInterface;
use Celestriode\Captain\LiteralMessage;
use Celestriode\Captain\Exceptions\SimpleCommandExceptionType;
use Celestriode\Captain\Exceptions\DynamicCommandExceptionType;

final class BuiltInNbtExceptions
{
    public function trailing(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('Unexpected trailing data');
    }

    public function expectedKey(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('Expected key');
    }

    public function expectedValue(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('Expected value');
    }
    
    public function listMixed(): Dynamic2CommandExceptionType
	{
		return new Dynamic2CommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Can\'t insert ' . $data[0] . ' into list of ' . $data[1]);
            }
        });
	}
    
    public function arrayMixed(): Dynamic2CommandExceptionType
	{
		return new Dynamic2CommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Can\'t insert ' . $data[0] . ' into ' . $data[1]);
            }
        });
	}

    public function arrayInvalid(): DynamicCommandExceptionType
	{
		return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Invalid array type \'' . $data[0] . '\'');
            }
        });
	}

	public function invalidNode(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('Invalid node.');
    }
}