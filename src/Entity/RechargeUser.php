<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RechargeUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RechargeUserRepository::class)]
#[ApiResource]
class RechargeUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rechargeUsers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $createdBy = null;

    #[ORM\Column(nullable: true)]
    private ?float $oldBalance = null;

    #[ORM\Column(nullable: true)]
    private ?float $newBalance = null;

    #[ORM\Column(length: 10)]
    private ?string $reference = null;
    public function __construct()
    {
       
        $this->createdAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

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

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getOldBalance(): ?float
    {
        return $this->oldBalance;
    }

    public function setOldBalance(?float $oldBalance): static
    {
        $this->oldBalance = $oldBalance;

        return $this;
    }

    public function getNewBalance(): ?float
    {
        return $this->newBalance;
    }

    public function setNewBalance(?float $newBalance): static
    {
        $this->newBalance = $newBalance;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }
}
