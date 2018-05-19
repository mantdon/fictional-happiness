<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class NewPassword
{
    /**
     * @Assert\Length(
     *     min = 5,
     *     minMessage = "Slaptažodį privalo sudaryti bent 5 simboliai"
     * )
     */
    private $newPassword;

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $password): self
    {
        $this->newPassword = $password;

        return $this;
    }
}
