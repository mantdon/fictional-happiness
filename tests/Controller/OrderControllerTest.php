<?php

namespace App\Tests\Controller;

use App\Tests\Fixtures\LoadIncompletePersonalDetailsFilledUser;
use App\Tests\Fixtures\LoadUserWithoutVehiclesAndOrders;
use App\Tests\CustomWebTestCase;

class OrderControllerTest extends CustomWebTestCase
{
    private $client;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();
		printf("Loading fixtures for: %s\n", self::class);
		$fixtures = array(
			LoadIncompletePersonalDetailsFilledUser::class,
			LoadUserWithoutVehiclesAndOrders::class
		);
		(new self)->loadFixtures($fixtures, false);
	}

    public function testUserRedirectionIfNotAllPersonalInformationFieldsAreFilled()
    {
        $this->client = static::createClient(array('environment' => 'test'), array(
            'PHP_AUTH_USER' => 'info@incomplete.com',
            'PHP_AUTH_PW'   => 'pass',
        ));

        $this->client->request('GET', '/order');

        $crawler = $this->client->followRedirect();
        
        $this->assertTrue($crawler->filter('html:contains("Prieš atliekant užsakymą privalote užpildyti savo informaciją")')->count() > 0);
    }

    public function testUserAccessOrderPage()
    {
        $this->client = static::createClient(array('environment' => 'test'), array(
            'PHP_AUTH_USER' => 'info@complete.com',
            'PHP_AUTH_PW'   => 'pass',
        ));

        $crawler = $this->client->request('GET', '/order');

        $this->assertTrue($crawler->filterXPath('//div[contains(@id, "VehicleSelection")]')->count() === 1);
    }
}
