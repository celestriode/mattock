<?php namespace Celestriode\Mattock\Exceptions\Selector;

use Celestriode\Captain\Exceptions\DynamicCommandExceptionType;
use Celestriode\Captain\Exceptions\DynamicFunctionInterface;
use Celestriode\Captain\Exceptions\SimpleCommandExceptionType;
use Celestriode\Captain\LiteralMessage;
use Celestriode\Captain\MessageInterface;
use Celestriode\DynamicRegistry\AbstractRegistry;
use Celestriode\Mattock\Exceptions\NotInRegistryException;

final class BuiltInSelectorExceptions
{
    public function invalidNameOrUuid(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Input \'' . $data[0] . '\' was not a valid name or UUID');
            }
        });
    }

    public function selectorsNotAllowed(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('Selectors are not allowed in this context');
    }

    public function missingSelectorType(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('Missing selector type');
    }

    public function unknownSelectorType(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Unknown selector type \'' . $data[0] . '\'');
            }
        });
    }

    public function missingOptionValue(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Value for option \'' . $data[0] . '\' is missing');
            }
        });
    }

    public function missingEndOfOptions(): SimpleCommandExceptionType
    {
        return SimpleCommandExceptionType::createWithLiteral('Missing ending square bracket');
    }

    public function inapplicableOption(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Inapplicable option \'' . $data[0] . '\'');
            }
        });
    }

    public function duplicateOption(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Cannot have duplicate option \'' . $data[0] . '\'');
            }
        });
    }

    public function duplicateInvertibleOption(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Cannot have duplicate option \'' . $data[0] . '\' unless it is inverted');
            }
        });
    }

    public function unknownOption(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Unknown option \'' . $data[0] . '\'');
            }
        });
    }

    public function limitOutOfRange(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Limit \'' . $data[0] . '\' cannot be less than 1');
            }
        });
    }

    public function unknownSort(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Unknown sort \'' . $data[0] . '\'');
            }
        });
    }

    public function unknownGameMode(AbstractRegistry $registry): DynamicCommandExceptionType
    {
        return new NotInRegistryException($registry, new class implements DynamicFunctionInterface {

            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Unknown game mode \'' . $data[0] . '\'');
            }
        });
    }

    public function unknownEntityType(AbstractRegistry $registry): DynamicCommandExceptionType
    {
        return new NotInRegistryException($registry, new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Unknown entity type \'' . $data[0] . '\'');
            }
        });
    }

    public function unknownEntityTag(AbstractRegistry $registry): DynamicCommandExceptionType
    {
        return new NotInRegistryException($registry, new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Unknown entity tag \'' . $data[0] . '\'');
            }
        });
    }

    public function duplicateObjective(): DynamicCommandExceptionType
    {
        return new DynamicCommandExceptionType(new class implements DynamicFunctionInterface {
            public function apply(...$data): MessageInterface
            {
                return new LiteralMessage('Duplicate objective \'' . $data[0] . '\' specified');
            }
        });
    }
}