<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\NotificationRepository;
use App\State\NotificationStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource]
#[Post(processor:NotificationStateProcessor::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $body = null;

    #[ORM\Column]
    private ?bool $isSent = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sentTime = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $users = [];

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $type = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        $this->isSent = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function isIsSent(): ?bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): self
    {
        $this->isSent = $isSent;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSentTime(): ?\DateTimeInterface
    {
        return $this->sentTime;
    }

    public function setSentTime(?\DateTimeInterface $sentTime): self
    {
        $this->sentTime = $sentTime;

        return $this;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function setUsers(?array $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
