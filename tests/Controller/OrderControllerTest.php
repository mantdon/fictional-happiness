<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Fixtures\LoadIncompletePersonalDetailsFilledUser;
use Doctrine\ORM\Tools\SchemaTool;
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
            'App\Tests\Fixtures\LoadIncompletePersonalDetailsFilledUser',
        );
        $this->loadFixtures($fixtures);
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


}
