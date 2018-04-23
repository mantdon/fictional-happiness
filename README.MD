Team
=
Martynas Treinys IFF-6/3\
Mantas Arlauskas IFF-6/3\
Mantvydas Donauskas IFF-6/3\
Dainius Arnastauskas IFF-6/3

Project set up
=
Run:\
 `composer install`\
 `yarn install`\
 `yarn run encore dev`
 

Open .env, find this line\
`DATABASE_URL=mysql://{db_user}:{db_password}@127.0.0.1:3306/fictional-happiness`\
and enter your mysql username and password.

Compiling assets
=

Every time javascript or css is changed it's necessary to compile it:\
`yarn run encore dev`

Requiring authentication
=

In order to require authentication before loading a page, in **security.yaml**, under **access_control:** \
add the following line: \
`- { path: regex_for_url, roles: required_role_for_access }`\
where :
 - *regex_for_url* - regular expression matching the URL
 - *required_role_for_access* - role user must belong to in order to be granted access\
                                (currently only ROLE_USER is handled.)

Creating user with Admin privileges
=


`php bin/console app:admin:create [username] [password]`

Functional tests
=

To set up test database open `phpunit.xml.dist` and add/edit

```xml
<php>
      <!--........-->
      <env name="DATABASE_URL" value="mysql://[username]:[password]@127.0.0.1:3306/fictional-happiness_tests" />
      <!-- define your env variables for the test env here -->
 </php>
 ```
Tests should extend `Liip\FunctionalTestBundle\Test\WebTestCase`.
 
To set up test database and load fixtures use this method: 
```php
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
            'Path/To/Fixture',
        );
        $this->loadFixtures($fixtures);
    }
```