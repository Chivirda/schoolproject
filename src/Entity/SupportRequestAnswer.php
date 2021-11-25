<?php

namespace App\Entity;

use App\DTO\SupportRequestAnswerDTO;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SupportRequestAnswerRepository")
 */
class SupportRequestAnswer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private ?int $id;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SupportRequest", inversedBy="answer")
     * @ORM\JoinColumn(name="support_request_id", referencedColumnName="id")
     */
    private SupportRequest $supportRequest;
    /**
     * @ORM\Column(name="answer", type="text")
     */
    private string $answer;
    /**
     * @ORM\Column(name="answered_at", type="datetime", nullable=false)
     */
    private DateTime $answeredAt;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="answered_by", referencedColumnName="id", nullable=false)
     */
    private User $answeredBy;

    public function __construct(SupportRequest $supportRequest, User $user)
    {
        $this->supportRequest = $supportRequest;
        $this->answeredBy = $user;
        $this->answeredAt = new DateTime();
    }

    public static function createFromDto(SupportRequestAnswerDTO $dto): self
    {
        $answer = new SupportRequestAnswer($dto->getSupportRequest(), $dto->getCreatedBy());
        $answer->setAnswer($dto->getAnswer());
        return $answer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSupportRequest(): SupportRequest
    {
        return $this->supportRequest;
    }

    public function setSupportRequest(SupportRequest $supportRequest): void
    {
        $this->supportRequest = $supportRequest;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

    public function getAnsweredAt(): DateTime
    {
        return $this->answeredAt;
    }

    public function setAnsweredAt(DateTime $answeredAt): void
    {
        $this->answeredAt = $answeredAt;
    }

    public function getAnsweredBy(): User
    {
        return $this->answeredBy;
    }

    public function setAnsweredBy(User $answeredBy): void
    {
        $this->answeredBy = $answeredBy;
    }

    public function getData(): array
    {
        return [
            'answer'      => $this->getAnswer(),
            'answered_by' => $this->getAnsweredBy()->getData(),
        ];
    }
}