<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Services\AvailableTimesFetcher;
use App\Tests\Fixtures\LoadIncompletePersonalDetailsFilledUser;
use App\Util\Clock;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
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
        ClockMock::register(Clock::class);
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

}
