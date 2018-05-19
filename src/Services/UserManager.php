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

    /**
     * @param User $user
     */
    public function createUser(User $user)
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());

        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();
    }

    public static function createAuthenticationTokenFor(User $user): UsernamePasswordToken
    {
    	return $token = new UsernamePasswordToken(
    		$user,
		    $user->getPassword(),
		    'global',
		    $user->getRoles()
	    );
    }

    public function findUser(?string $email): ?User
    {
        if ($email === null) {
            return null;
        }

        $repository = $this->em->getRepository(User::class);
        $user = $repository->findOneBy(['email' => $email]);
        if ($user === null) {
            return null;
        }
        return $user;
    }

    /**
     * Checks whether user has completed registration by filling his personal information fields.
     * @param $user User
     * @return bool
     */
    public function hasUserFilledPersonalInformation(User $user): bool
    {
        return !(empty($user->getFirstName()) ||
            empty($user->getLastName()) ||
            empty($user->getPhone()) ||
            empty($user->getAddress()) ||
            empty($user->getCity()));
    }
}