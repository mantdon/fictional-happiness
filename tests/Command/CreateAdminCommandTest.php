<?php


namespace App\Tests\Command;

use App\Entity\User;
use App\Tests\CustomWebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAdminCommandTest extends CustomWebTestCase
{
    private $em;
    private $command;
    private $commandTester;
    private $passwordEncoder;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $application = new Application($kernel);

        $this->command = $application->find('app:admin:create');
        $this->commandTester = new CommandTester($this->command);

        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->passwordEncoder = $kernel->getContainer()->get('security.password_encoder');
    }

    /**
     * @dataProvider getData
     */
    public function testExecute($data)
    {

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => isset($data['username'])?$data['username']:'admin']);

        $this->assertNull($user);

        $this->commandTester->execute($this->getCommand($data));

        $output = $this->commandTester->getDisplay();

        $username = isset($data['username'])? $data['username']: 'admin';
        $password = isset($data['password'])? $data['password']: 'password';

        $this->assertContains('username: ' . $username, $output);
        $this->assertContains('password: ' . $password, $output);

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $username]);

        $this->assertEquals($username, $user->getEmail());
        $this->assertTrue($this->passwordEncoder->isPasswordValid($user, $password));
    }

    private function getCommand($data)
    {
        $command = array(
            'command'  => $this->command->getName()
        );

        $command = $this->addFieldIfExists($command, $data, 'username');
        $command = $this->addFieldIfExists($command, $data, 'password');

        return $command;
    }

    private function addFieldIfExists($command, $data, $field)
    {
        if(isset($data[$field]))
            $command = array_merge($command, [$field => $data[$field]]);

        return $command;
    }

    public static function getData()
    {
        yield [[]];
        yield [['username' => 'asddf']];
        yield [['username' => 'tyrrb', 'password' => '1235']];
    }
}