<?php

namespace App\Entity;

use App\DTO\SupportRequestDTO;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SupportRequestRepository")
 */
class SupportRequest
{
    public const DRAFT_STATUS_INDEX    = 0;
    public const SOLVED_STATUS_INDEX   = 1;
    public const REJECTED_STATUS_INDEX = 2;

    public const STATUS_TITLE_LIST = [
        self::DRAFT_STATUS_INDEX    => 'Ожидание решения',
        self::SOLVED_STATUS_INDEX   => 'Решено',
        self::REJECTED_STATUS_INDEX => 'Отклонено',
    ];

    public const ANSWER_STATUS_TITLE_LIST = [
        self::SOLVED_STATUS_INDEX   => 'Решено',
        self::REJECTED_STATUS_INDEX => 'Отклонено',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private ?int $id;
    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private string $title;
    /**
     * @ORM\Column(name="message", type="text", length=10000)
     */
    private string $message;
    /**
     * @ORM\Column(name="status", type="smallint", options={"default" : 0})
     */
    private int $status = 0;
    /**
     * @ORM\Column(name="create_at", type="datetime", nullable=false)
     */
    private DateTime $createAt;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=false)
     */
    private User $createdBy;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SupportRequestAnswer", mappedBy="supportRequest")
     */
    private ?SupportRequestAnswer $answer;

    public function __construct(User $user, string $title, string $message)
    {
        $this->createdBy = $user;
        $this->title = $title;
        $this->message = $message;
        $this->createAt = new DateTime();
    }

    public static function createFromDTO(SupportRequestDTO $dto): self
    {
        return new self($dto->getCreatedBy(), $dto->getTitle(), $dto->getMessage());
    }

    public function updateFromDTO(SupportRequestDTO $dto): self
    {
        $this->setTitle($dto->getTitle());
        $this->setMessage($dto->getMessage());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getStatusTitle(): string
    {
        return self::STATUS_TITLE_LIST[$this->getStatus()];
    }

    public function getCreateAt(): DateTime
    {
        return $this->createAt;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function isAnswered(): bool
    {
        return in_array($this->getStatus(), [self::SOLVED_STATUS_INDEX, self::REJECTED_STATUS_INDEX]);
    }

    public function getAnswer(): ?SupportRequestAnswer
    {
        return $this->answer;
    }

    public function setAnswer(?SupportRequestAnswer $answer): void
    {
        $this->answer = $answer;
    }

    public function getData(): array
    {
        return [
            'id'         => $this->getId(),
            'title'      => $this->getTitle(),
            'message'    => $this->getMessage(),
            'status'     => [
                'id'    => $this->getStatus(),
                'title' => $this->getStatusTitle(),
            ],
            'created_at' => $this->getCreateAt()->format(DATE_ATOM),
            'created_by' => $this->getCreatedBy()->getData(),
        ];
    }
}