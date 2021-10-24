<?php namespace Celestriode\Mattock\Parsers\Java\Nbt;

use Celestriode\Mattock\Exceptions\MattockException;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\ByteTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\ShortTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\IntTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\LongTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\FloatTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\DoubleTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\ByteArrayTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\StringTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\ListTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\CompoundTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\IntArrayTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\LongArrayTag;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\TagInterface;

final class TagUtils
{
    public static function createTag(int $type): TagInterface
    {
        switch($type) {
            case TagInterface::TAG_END:
                throw new MattockException('Unhandled TAG_END');
            case TagInterface::TAG_BYTE:
                return new ByteTag();
            case TagInterface::TAG_SHORT:
                return new ShortTag();
            case TagInterface::TAG_INT:
                return new IntTag();
            case TagInterface::TAG_LONG:
                return new LongTag();
            case TagInterface::TAG_FLOAT:
                return new FloatTag();
            case TagInterface::TAG_DOUBLE:
                return new DoubleTag();
            case TagInterface::TAG_BYTE_ARRAY:
                return new ByteArrayTag();
            case TagInterface::TAG_STRING:
                return new StringTag();
            case TagInterface::TAG_LIST:
                return new ListTag();
            case TagInterface::TAG_COMPOUND:
                return new CompoundTag();
            case TagInterface::TAG_INT_ARRAY:
                return new IntArrayTag();
            case TagInterface::TAG_LONG_ARRAY:
                return new LongArrayTag();
            default:
                throw new MattockException('Unknown NBT type: ' . $type);
        }
    }

    public static function convertToString(int $type): string
    {
        switch($type) {
            case TagInterface::TAG_END:
                return "TAG_End";
            case TagInterface::TAG_BYTE:
                return "TAG_Byte";
            case TagInterface::TAG_SHORT:
                return "TAG_Short";
            case TagInterface::TAG_INT:
                return "TAG_Int";
            case TagInterface::TAG_LONG:
                return "TAG_Long";
            case TagInterface::TAG_FLOAT:
                return "TAG_Float";
            case TagInterface::TAG_DOUBLE:
                return "TAG_Double";
            case TagInterface::TAG_BYTE_ARRAY:
                return "TAG_Byte_Array";
            case TagInterface::TAG_STRING:
                return "TAG_String";
            case TagInterface::TAG_LIST:
                return "TAG_List";
            case TagInterface::TAG_COMPOUND:
                return "TAG_Compound";
            case TagInterface::TAG_INT_ARRAY:
                return "TAG_Int_Array";
            case TagInterface::TAG_LONG_ARRAY:
                return "TAG_Long_Array";
            case TagInterface::TAG_NUMERIC:
                return "Any Numeric Tag";
            default:
                return "UNKNOWN";
        }
    }
}