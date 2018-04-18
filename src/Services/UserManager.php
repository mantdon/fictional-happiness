<?php

namespace App\Services;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{

    private $passwordEncoder;
    private $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder,
                                EntityManagerInterface $em)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }

    public function createUser(User $user)
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());

        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();
    }

    public static function createAuthenticationTokenFor(User $user){
    	return $token = new UsernamePasswordToken(
    		$user,
		    $user->getPassword(),
		    'global',
		    $user->getRoles()
	    );
    }

    /**
     * Checks whether user has completed registration by filling his personal information fields.
     * @param $user User
     * @return bool
     */
    public function hasUserFilledPersonalInformation($user)
    {
        return !(empty($user->getFirstName()) ||
            empty($user->getLastName()) ||
            empty($user->getPhone()) ||
            empty($user->getAddress()) ||
            empty($user->getCity()));
    }
}