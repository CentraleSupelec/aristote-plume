<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;

class CiTextType extends TextType
{
    final public const CITEXT = 'citext';

    public function getName(): string
    {
        return static::CITEXT;
    }

    /**
     * @throws Exception
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDoctrineTypeMapping(static::CITEXT);
    }
}
