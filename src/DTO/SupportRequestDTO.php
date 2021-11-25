<?php

namespace App\DTO;

use App\Entity\SupportRequest;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class SupportRequestDTO
{
    /**
     * @Assert\NotBlank(message="Поле должно быть заполнено")
     * @Assert\Length(max=255)
     */
    private ?string $title;
    /**
     * @Assert\NotBlank(message="Поле должно быть заполнено")
     * @Assert\Length(max=10000)
     */
    private ?string $message;
    private User $createdBy;

    public function __construct(User $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    public static function createFromEntity(SupportRequest $request): self
    {
        $dto = new self($request->getCreatedBy());

        $dto->setTitle($request->getTitle());
        $dto->setMessage($request->getMessage());

        return $dto;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}