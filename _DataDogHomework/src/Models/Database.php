<?php
namespace src\Models;


class Database
{
    private $users;

    public function addUser(User $user)
    {
        $this->users[] = $user;
    }

    public function findByUsername($username)
    {
        /**
         * @var $user User
         */
        foreach($this->users as $user)
        {
            if($user->getUsername() === $username)
                return $user;
        }
        return null;
    }
}