<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LineRepository::class)]
#[ApiResource()]
class Line
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 15)]
    private ?string $paymentType = null;

    #[ORM\ManyToOne(inversedBy: 'liness')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $region = null;

    #[ORM\Column(nullable: true)]
    private ?float $ticketPrice = null;

    #[ORM\ManyToMany(targetEntity: NfcCard::class, mappedBy: 'liness')]
    private Collection $nfccards;

    #[ORM\OneToMany(mappedBy: 'line', targetEntity: Route::class)]
    private Collection $routes;

    #[ORM\OneToMany(mappedBy: 'line', targetEntity: Place::class)]
    #[Groups(['line:read', 'child:read'])]
    private Collection $places;

    #[ORM\OneToMany(mappedBy: 'line', targetEntity: Vehicle::class)]
    #[Groups(['line:read', 'child:read'])]
    private Collection $vehicles;

    #[ORM\OneToMany(mappedBy: 'line', targetEntity: Stop::class)]
    private Collection $stops;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'liness')]
    private ?Enterprise $enterprise = null;

    #[ORM\ManyToOne]
    private ?Currency $currency = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        
        $this->nfccards = new ArrayCollection();
        $this->routes = new ArrayCollection();
        $this->places = new ArrayCollection();
        $this->vehicles = new ArrayCollection();
        $this->stops = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getTicketPrice(): ?float
    {
        return $this->ticketPrice;
    }

    public function setTicketPrice(?float $ticketPrice): static
    {
        $this->ticketPrice = $ticketPrice;

        return $this;
    }

    /**
     * @return Collection<int, NfcCard>
     */
    public function getNfcCards(): Collection
    {
        return $this->nfccards;
    }

    public function addNfcCards(NfcCard $nfccard): static
    {
        if (!$this->nfccards->contains($nfccard)) {
            $this->nfccards->add($nfccard);
            $nfccard->addLiness($this);
        }

        return $this;
    }

    public function removeNfcCard(NfcCard $nfccard): static
    {
        if ($this->nfccards->removeElement($nfccard)) {
            $nfccard->removeLiness($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Route>
     */
    public function getRoutes(): Collection
    {
        return $this->routes;
    }

    public function addRoute(Route $route): static
    {
        if (!$this->routes->contains($route)) {
            $this->routes->add($route);
            $route->setLine($this);
        }

        return $this;
    }

    public function removeRoute(Route $route): static
    {
        if ($this->routes->removeElement($route)) {
            // set the owning side to null (unless already changed)
            if ($route->getLine() === $this) {
                $route->setLine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): static
    {
        if (!$this->places->contains($place)) {
            $this->places->add($place);
            $place->setLine($this);
        }

        return $this;
    }

    public function removePlace(Place $place): static
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getLine() === $this) {
                $place->setLine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->setLine($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            // set the owning side to null (unless already changed)
            if ($vehicle->getLine() === $this) {
                $vehicle->setLine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stop>
     */
    public function getStops(): Collection
    {
        return $this->stops;
    }

    public function addStop(Stop $stop): static
    {
        if (!$this->stops->contains($stop)) {
            $this->stops->add($stop);
            $stop->setLine($this);
        }

        return $this;
    }

    public function removeStop(Stop $stop): static
    {
        if ($this->stops->removeElement($stop)) {
            // set the owning side to null (unless already changed)
            if ($stop->getLine() === $this) {
                $stop->setLine(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getEnterprise(): ?Enterprise
    {
        return $this->enterprise;
    }

    public function setEnterprise(?Enterprise $enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }
    public function toArray(): array{
        return [
            "id"=>$this->getId(),
        "region"=>$this->getRegion()?->getId(),
        "enterprise"=>$this->getEnterprise()?->getId(),
        "name"=>$this->getName(),
        "paymentType"=>$this->getPaymentType(),
        "ticketPrice"=>$this->getTicketPrice(),
        //"region"=>"/api/regions/".$line->getRegion()->getId(),
        "description"=>$this->getDescription(),
        ];
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
