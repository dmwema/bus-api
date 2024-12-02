<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\NfcCardRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NfcCardRepository::class)]
#[ApiResource]
class NfcCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $uid = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $cardHolder = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    private ?float $balance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'card', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\OneToMany(mappedBy: 'card', targetEntity: RechargeCarte::class)]
    private Collection $rechargeCartes;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $createdBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subscriptionFromDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subscriptionEndDate = null;

    #[ORM\Column(length: 6)]
    private ?string $code = null;

    #[ORM\ManyToMany(targetEntity: Line::class, inversedBy: 'nfccards')]
    private Collection $liness;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->createdAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        $this->rechargeCartes = new ArrayCollection();
        $this->liness = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function getCardHolder(): ?string
    {
        return $this->cardHolder;
    }

    public function setCardHolder(?string $cardHolder): self
    {
        $this->cardHolder = $cardHolder;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setCard($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCard() === $this) {
                $transaction->setCard(null);
            }
        }

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, RechargeCarte>
     */
    public function getRechargeCartes(): Collection
    {
        return $this->rechargeCartes;
    }

    public function addRechargeCarte(RechargeCarte $rechargeCarte): self
    {
        if (!$this->rechargeCartes->contains($rechargeCarte)) {
            $this->rechargeCartes->add($rechargeCarte);
            $rechargeCarte->setCard($this);
        }

        return $this;
    }

    public function removeRechargeCarte(RechargeCarte $rechargeCarte): self
    {
        if ($this->rechargeCartes->removeElement($rechargeCarte)) {
            // set the owning side to null (unless already changed)
            if ($rechargeCarte->getCard() === $this) {
                $rechargeCarte->setCard(null);
            }
        }

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

    public function getSubscriptionFromDate(): ?\DateTimeInterface
    {
        return $this->subscriptionFromDate;
    }

    public function setSubscriptionFromDate(?\DateTimeInterface $subscriptionFromDate): static
    {
        $this->subscriptionFromDate = $subscriptionFromDate;

        return $this;
    }

    public function getSubscriptionEndDate(): ?\DateTimeInterface
    {
        return $this->subscriptionEndDate;
    }

    public function setSubscriptionEndDate(?\DateTimeInterface $subscriptionEndDate): static
    {
        $this->subscriptionEndDate = $subscriptionEndDate;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, Line>
     */
    public function getLiness(): Collection
    {
        return $this->liness;
    }

    public function addLiness(Line $liness): static
    {
        if (!$this->liness->contains($liness)) {
            $this->liness->add($liness);
        }

        return $this;
    }

    public function removeLiness(Line $liness): static
    {
        $this->liness->removeElement($liness);

        return $this;
    }
    public function isSubscribed():bool{
        $now =  new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        return $this->subscriptionFromDate <= $now && $this->subscriptionEndDate >  $now;
    }
}
