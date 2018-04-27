<?php

namespace App\Tests\Controller;

use App\Services\AvailableTimesFetcher;
use App\Services\UnavailableDaysFinder;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

class OrderControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient(array('environment' => 'test'));
        $em = $this->getContainer()->get('doctrine')->getManager();
        if (!isset($metadatas)) {
            $metadatas = $em->getMetadataFactory()->getAllMetadata();
        }
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        if (!empty($metadatas)) {
            $schemaTool->createSchema($metadatas);
        }
        $this->postFixtureSetup();

        $fixtures = array(
            'App\DataFixtures\AppFixtures',
            'App\Tests\Fixtures\LoadIncompletePersonalDetailsFilledUser',
            'App\Tests\Fixtures\LoadUserWithVehicles'
        );
        $this->loadFixtures($fixtures);
    }

    public static function setUpBeforeClass()
    {
        ClockMock::register(AvailableTimesFetcher::class);
        ClockMock::register(UnavailableDaysFinder::class);
    }

    public function testUserRedirectionIfNotAllPersonalInformationFieldsAreFilled()
    {
        $this->client->request('GET', '/order', array(), array(), array(
            'PHP_AUTH_USER' => 'info@incomplete.com',
            'PHP_AUTH_PW'   => 'pass',
        ));

        $crawler = $this->client->followRedirect();
        
        $this->assertTrue($crawler->filter('html:contains("Prieš atliekant užsakymą privalote užpildyti savo informaciją")')->count() > 0);
    }

    public function testUserAccessOrderPage()
    {
        $crawler = $this->client->request('GET', '/order', array(), array(), array(
            'PHP_AUTH_USER' => 'info@complete.com',
            'PHP_AUTH_PW'   => 'pass',
        ));

        $this->assertTrue($crawler->filterXPath('//div[contains(@id, "VehicleSelection")]')->count() === 1);
    }

    /**
     * @group time-sensitive
     */
    public function testIfUnavailableDaysAreFetched()
    {
        $now = new \DateTime('2018-05-02');
        ClockMock::withClockMock($now->format('U'));

        $this->client->request('GET', '/order/fetch_unavailable_days', array(), array(), array(
            'PHP_AUTH_USER' => 'info@complete.com',
            'PHP_AUTH_PW'   => 'pass',
        ));

        $response = $this->client->getResponse();

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(count($responseData), 1);

        $this->assertEquals("2018-05-18", $responseData[0]);
    }

    /**
     * @group time-sensitive
     */
    public function testIfAvailableTimesAreFetched()
    {
        $expected = ['11:00', '13:00'];
        $data = ['date' => '2018-05-02'];

        $now = new \DateTime('2018-05-02 10:00');
        ClockMock::withClockMock($now->format('U'));

        $this->client->request('GET', '/order/fetch_times', array(), array(), array(
            'PHP_AUTH_USER' => 'info@complete.com',
            'PHP_AUTH_PW'   => 'pass',
        ), json_encode($data));

        $response = $this->client->getResponse();

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($responseData, $expected);
    }
}
