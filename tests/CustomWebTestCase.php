<?php

namespace App\tests;


use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class customWebTestCase extends WebTestCase
{
	/**
	 * @var SchemaTool
	 */
	private static $schemaTool;
	private static $em;
	public static function setUpBeforeClass()
	{
		printf("Setting up database.\n");

		$kernel = static::createKernel();
		$kernel->boot();
		self::$em = $kernel->getContainer()->get('doctrine')->getManager();
		$metadatas = self::$em->getMetadataFactory()->getAllMetadata();
		self::$schemaTool = new SchemaTool(self::$em);
		if (!empty($metadatas)) {
			self::$schemaTool->updateSchema($metadatas);
		}
	}

	public static function tearDownAfterClass()
	{
		printf("\nDropping database.\n");
		self::$schemaTool->dropDatabase();
	}
}

