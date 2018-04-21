<?php

namespace App\Helpers;

class EnumOrderStatusType extends EnumType
{
	// Name to be used as type in doctrine.
	protected $name = 'orderstatus';
	// [ClassName]::class, to output valid enumeration keys.
	protected static $enumType = EnumOrderStatusType::class;

	// Define enumeration keys. Used in database table
	public const Complete =     'CMP';
	public const Canceled =     'CNC';
	public const Terminated =   'TRM';
	public const Placed =       'PLC';
	public const Ongoing =      'ONG';

	// Map keys to user readable values.
	// Mapped values are displayed instead of keys when
	// retrieved from the database.
	protected static $map = array(
		self::Complete => 'Complete',
		self::Canceled => 'Canceled',
		self::Terminated => 'Terminated',
		self::Placed => 'Placed',
		self::Ongoing => 'Ongoing'
	);
}