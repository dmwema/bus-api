<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RechargeCarteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RechargeCarteRepository::class)]
#[ApiResource]
class RechargeCarte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 64)]
    private ?string $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'rechargeCartes')]
    private ?NfcCard $card = null;

    #[ORM\Column(nullable: true)]
    private ?float $oldBalance = null;

    #[ORM\Column(nullable: true)]
    private ?float $newBalance = null;

    #[ORM\Column(length: 10)]
    private ?string $rechargeType = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fromDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $toDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $oldFromDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $oldToDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $subscriptionId = null;

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

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCard(): ?NfcCard
    {
        return $this->card;
    }

    public function setCard(?NfcCard $card): self
    {
        $this->card = $card;

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

    public function getRechargeType(): ?string
    {
        return $this->rechargeType;
    }

    public function setRechargeType(string $rechargeType): static
    {
        $this->rechargeType = $rechargeType;

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(?\DateTimeInterface $fromDate): static
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(?\DateTimeInterface $toDate): static
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function getOldFromDate(): ?\DateTimeInterface
    {
        return $this->oldFromDate;
    }

    public function setOldFromDate(?\DateTimeInterface $oldFromDate): static
    {
        $this->oldFromDate = $oldFromDate;

        return $this;
    }

    public function getOldToDate(): ?\DateTimeInterface
    {
        return $this->oldToDate;
    }

    public function setOldToDate(?\DateTimeInterface $oldToDate): static
    {
        $this->oldToDate = $oldToDate;

        return $this;
    }

    public function getSubscriptionId(): ?int
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(?int $subscriptionId): static
    {
        $this->subscriptionId = $subscriptionId;

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
