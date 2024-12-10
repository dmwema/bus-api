<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ApiResource]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?NfcCard $card = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?float $oldBalance = null;

    #[ORM\Column(nullable: true)]
    private ?float $newBalance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $oldFromDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $oldToDate = null;

    #[ORM\Column(length: 10)]
    private ?string $reference = null;

    #[ORM\Column(length: 10)]
    private ?string $paymentType = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Route $route = null;

    #[ORM\ManyToOne]
    private ?Currency $currency = null;
    public function __construct()
    {
       
        $this->createdAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): static
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    public function setRoute(?Route $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }
}
