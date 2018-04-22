<?php

namespace App\Helpers;

class EnumOrderStatusType extends EnumType
{
	// Name to be used as type in doctrine.
	protected $name = 'orderstatus';
	// [ClassName]::class, to output valid enumeration keys.
	protected static $enumType = EnumOrderStatusType::class;

	// Define enumeration keys. Used in database table.
	// Integer values define the desired sorting order.
	public const Ongoing    = 0;
	public const Placed     = 1;
	public const Complete   = 2;
	public const Terminated = 3;
	public const Canceled   = 4;

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