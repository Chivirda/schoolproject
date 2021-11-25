<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email;
    /**
     * @Assert\NotBlank
     */
    private ?string $fullName;
    /**
     * @Assert\NotBlank
     */
    private ?string $plainPassword;
    private ?string $repeatedPlainPassword;
    private ?string $hashedPassword;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getRepeatedPlainPassword(): ?string
    {
        return $this->repeatedPlainPassword;
    }

    public function setRepeatedPlainPassword(?string $repeatedPlainPassword): void
    {
        $this->repeatedPlainPassword = $repeatedPlainPassword;
    }

    public function getHashedPassword(): ?string
    {
        return $this->hashedPassword;
    }

    public function setHashedPassword(?string $hashedPassword): void
    {
        $this->hashedPassword = $hashedPassword;
    }
}