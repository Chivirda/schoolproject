<?php

namespace App\DTO;

use App\Entity\SupportRequest;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class SupportRequestAnswerDTO
{
    private ?string $status;
    /**
     * @Assert\NotBlank(message="Поле должно быть заполнено")
     * @Assert\Length(max=10000)
     */
    private ?string $answer;
    private SupportRequest $supportRequest;
    private User $createdBy;

    public function __construct(SupportRequest $supportRequest, User $createdBy)
    {
        $this->supportRequest = $supportRequest;
        $this->createdBy = $createdBy;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): void
    {
        $this->answer = $answer;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getSupportRequest(): SupportRequest
    {
        return $this->supportRequest;
    }

    public function setSupportRequest(SupportRequest $supportRequest): void
    {
        $this->supportRequest = $supportRequest;
    }
}