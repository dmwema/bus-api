<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\GetCollection;
use App\State\RouteStateProcessor;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
#[ApiResource(operations:[new Get(), new Post(), new Put(processor: RouteStateProcessor::class),new GetCollection()])]
#[ApiFilter(SearchFilter::class,properties:['isActive'=>'exact', 'vehicle'=>'exact','conveyor'=>'exact', 'status'=>'exact'])]
class Route
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $destination = null;

    #[ORM\Column(nullable: true)]
    private ?float $ticket_price = null;

    #[ORM\Column(nullable: true)]
    private ?float $startLat = null;

    #[ORM\Column(nullable: true)]
    private ?float $startLng = null;

    #[ORM\Column(nullable: true)]
    private ?float $endLat = null;

    #[ORM\Column(nullable: true)]
    private ?float $endLng = null;

    #[ORM\Column(nullable: true)]
    private ?int $passengers = null;

    #[ORM\Column(length: 128)]
    private ?string $origine = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startingTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endingTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $gpx = null;

    #[ORM\ManyToOne(inversedBy: 'routes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'routes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $conveyor = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?int $driverPassengers = null;

    #[ORM\ManyToOne(inversedBy: 'routes')]
    private ?Line $line = null;

    #[ORM\OneToMany(mappedBy: 'route', targetEntity: Transaction::class)]
    private Collection $transactions;

  


    public function __construct()
    {
        //$this->cretedAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));

        $this->startingTime = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        $this->transactions = new ArrayCollection();

        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getTicketPrice(): ?float
    {
        return $this->ticket_price;
    }

    public function setTicketPrice(?float $ticket_price): self
    {
        $this->ticket_price = $ticket_price;

        return $this;
    }

    public function getStartLat(): ?float
    {
        return $this->startLat;
    }

    public function setStartLat(?float $startLat): self
    {
        $this->startLat = $startLat;

        return $this;
    }

    public function getStartLng(): ?float
    {
        return $this->startLng;
    }

    public function setStartLng(?float $startLng): self
    {
        $this->startLng = $startLng;

        return $this;
    }

    public function getEndLat(): ?float
    {
        return $this->endLat;
    }

    public function setEndLat(?float $endLat): self
    {
        $this->endLat = $endLat;

        return $this;
    }

    public function getEndLng(): ?float
    {
        return $this->endLng;
    }

    public function setEndLng(?float $endLng): self
    {
        $this->endLng = $endLng;

        return $this;
    }

    public function getPassengers(): ?int
    {
        return $this->passengers;
    }

    public function setPassengers(?int $passengers): self
    {
        $this->passengers = $passengers;

        return $this;
    }

    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function setOrigine(string $origine): self
    {
        $this->origine = $origine;

        return $this;
    }

    public function getStartingTime(): ?\DateTimeInterface
    {
        return $this->startingTime;
    }

    public function setStartingTime(?\DateTimeInterface $startingTime): self
    {
        $this->startingTime = $startingTime;

        return $this;
    }

    public function getEndingTime(): ?\DateTimeInterface
    {
        return $this->endingTime;
    }

    public function setEndingTime(?\DateTimeInterface $endingTime): self
    {
        $this->endingTime = $endingTime;

        return $this;
    }

    public function getGpx(): ?string
    {
        return $this->gpx;
    }

    public function setGpx(?string $gpx): self
    {
        $this->gpx = $gpx;

        return $this;
    }


    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getConveyor(): ?User
    {
        return $this->conveyor;
    }

    public function setConveyor(?User $conveyor): self
    {
        $this->conveyor = $conveyor;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDriverPassengers(): ?int
    {
        return $this->driverPassengers;
    }

    public function setDriverPassengers(?int $driverPassengers): self
    {
        $this->driverPassengers = $driverPassengers;

        return $this;
    }

    public function getLine(): ?Line
    {
        return $this->line;
    }

    public function setLine(?Line $line): static
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setRoute($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getRoute() === $this) {
                $transaction->setRoute(null);
            }
        }

        return $this;
    }


}
