<?php

namespace App\Helpers;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class EnumType extends Type {
	protected $name;
	protected static $enumType;
	protected static $map;

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return "INT";
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if(!self::isValid($value))
		{
			throw new \InvalidArgumentException(sprintf(
				'The value "%s" is not valid for the enum "%s". Expected one of ["%s"]',
				$value,
				static::$enumType,
				implode('", "', self::keys())
            ));
		}
		return static::$map[$value];
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if(!self::isValid($value))
		{
			throw new \InvalidArgumentException(sprintf(
                'The value "%s" is not valid for the enum "%s". Expected one of ["%s"]',
                $value,
                static::$enumType,
                implode('", "', self::keys())
            ));
		}
		return (int) $value;
	}

	public function getName()
	{
		return $this->name;
	}

	public function requiresSQLCommentHint(AbstractPlatform $platform)
	{
		return true;
	}

	public static function keys()
	{
		return array_keys(static::$map);
	}

	public static function values()
	{
		return array_values(static::$map);
	}

	private static function isValid(int $key)
	{
		return isset(static::$map[$key]);
	}

	public static function getValue(int $key)
	{
		if(self::isValid($key))
		{
			return static::$map[$key];
		}
		throw new \InvalidArgumentException("Ungood.");
	}
}