<?php

namespace src\Models;


class User implements \Serializable
{
    private $username;
    private $password;

    public function setPlainPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function verifyPassword($password)
    {
        return password_verify($password, $this->password) || $this->password === $password;
    }

    public function equals(User $other)
    {
        return $other->username === $this->username && $other->password === $this->password;
    }

    public function serialize()
    {
        return serialize(array(
            $this->username,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }


}