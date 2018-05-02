<?php

namespace App\tests;


use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class CustomWebTestCase extends WebTestCase
{
	/** @var SchemaTool */
	private static $schemaTool;
	private static $em;
	protected static $fixtures;
	protected static $appendFixtures = false;

	public static function setUpBeforeClass()
	{
		printf("Setting up database.\n");

		$kernel = static::createKernel();
		$kernel->boot();
		self::$em = $kernel->getContainer()->get('doctrine')->getManager();
		$metadata = self::$em->getMetadataFactory()->getAllMetadata();
		self::$schemaTool = new SchemaTool(self::$em);
		if(!empty($metadata)){
			self::$schemaTool->updateSchema($metadata);
		}
		if(static::$fixtures !== null){
			printf("Loading fixtures for: %s\n", static::class);
			(new self)->loadFixtures(static::$fixtures, static::$appendFixtures);
		}
	}
}

