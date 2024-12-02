<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
#[ApiResource(operations:[new Get(), new Post(), new Put(),new GetCollection()])]
#[ApiFilter(SearchFilter::class,properties:['deviceID'=>'exact'])]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]

    private ?string $name = null;

    #[ORM\Column(length: 24)]
    private ?string $matricule = null;

    #[ORM\Column(nullable: true)]
    private ?float $currentLat = null;

    #[ORM\Column(nullable: true)]
    private ?float $currentLng = null;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: Route::class)]
    private Collection $routes;

    #[ORM\Column(length: 64, nullable: true)]    
    private ?string $deviceID = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $voletJaune = null;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: User::class)]
    private Collection $users;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    const SERVER_PATH_TO_IMAGE_FOLDER = 'images/vehicles';
    /**
     * Unmapped property to handle file uploads
     */
    private ?UploadedFile $file = null;

    #[ORM\Column(nullable: true)]
    private ?float $kilometer = null;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: KilometerTrack::class)]
    private Collection $kilometerTracks;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: Carburant::class)]
    private Collection $carburants;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: Versement::class)]
    private Collection $versements;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: Alert::class)]
    private Collection $alerts;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $gpx = null;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: VehicleTracker::class)]
    private Collection $vehicleTrackers;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read'])]
    private ?Line $line = null;

    public function __construct()
    {
        $this->routes = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->kilometerTracks = new ArrayCollection();
        $this->carburants = new ArrayCollection();
        $this->versements = new ArrayCollection();
        $this->alerts = new ArrayCollection();
        $this->vehicleTrackers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getCurrentLat(): ?float
    {
        return $this->currentLat;
    }

    public function setCurrentLat(?float $currentLat): self
    {
        $this->currentLat = $currentLat;

        return $this;
    }

    public function getCurrentLng(): ?float
    {
        return $this->currentLng;
    }

    public function setCurrentLng(?float $currentLng): self
    {
        $this->currentLng = $currentLng;

        return $this;
    }

    /**
     * @return Collection<int, Route>
     */
    public function getRoutes(): Collection
    {
        return $this->routes;
    }

    public function addRoute(Route $route): self
    {
        if (!$this->routes->contains($route)) {
            $this->routes->add($route);
            $route->setVehicle($this);
        }

        return $this;
    }

    public function removeRoute(Route $route): self
    {
        if ($this->routes->removeElement($route)) {
            // set the owning side to null (unless already changed)
            if ($route->getVehicle() === $this) {
                $route->setVehicle(null);
            }
        }

        return $this;
    }

    public function getDeviceID(): ?string
    {
        return $this->deviceID;
    }

    public function setDeviceID(?string $deviceID): self
    {
        $this->deviceID = $deviceID;

        return $this;
    }

    public function getVoletJaune(): ?string
    {
        return $this->voletJaune;
    }

    public function setVoletJaune(?string $voletJaune): self
    {
        $this->voletJaune = $voletJaune;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setVehicle($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getVehicle() === $this) {
                $user->setVehicle(null);
            }
        }

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
    public function setFile(?UploadedFile $file = null): void
    {
        $this->file = $file;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload(): void
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

       // we use the original file name here but you should
       // sanitize it at least to avoid any security issues

       // move takes the target directory and target filename as params
       $fname = $this->name.''.$this->matricule.'.jpg';
       //die(var_dump(dirname(__DIR__).self::SERVER_PATH_TO_IMAGE_FOLDER));
       $this->getFile()->move(
        self::SERVER_PATH_TO_IMAGE_FOLDER,
           $fname
       );

       // set the path property to the filename where you've saved the file
       $this->voletJaune = $fname;

       // clean up the file property as you won't need it anymore
       $this->setFile(null);
   }

   /**
    * Lifecycle callback to upload the file to the server.
    */
   public function lifecycleFileUpload(): void
   {
       $this->upload();
   }

   /**
    * Updates the hash value to force the preUpdate and postUpdate events to fire.
    */
   public function refreshUpdated(): void
   {
      $this->setUpdatedAt(new \DateTime());
      $this->lifecycleFileUpload();
   }

   public function getKilometer(): ?float
   {
       return $this->kilometer;
   }

   public function setKilometer(?float $kilometer): self
   {
       $this->kilometer = $kilometer;

       return $this;
   }

   /**
    * @return Collection<int, KilometerTrack>
    */
   public function getKilometerTracks(): Collection
   {
       return $this->kilometerTracks;
   }

   public function addKilometerTrack(KilometerTrack $kilometerTrack): self
   {
       if (!$this->kilometerTracks->contains($kilometerTrack)) {
           $this->kilometerTracks->add($kilometerTrack);
           $kilometerTrack->setVehicle($this);
       }

       return $this;
   }

   public function removeKilometerTrack(KilometerTrack $kilometerTrack): self
   {
       if ($this->kilometerTracks->removeElement($kilometerTrack)) {
           // set the owning side to null (unless already changed)
           if ($kilometerTrack->getVehicle() === $this) {
               $kilometerTrack->setVehicle(null);
           }
       }

       return $this;
   }

   /**
    * @return Collection<int, Carburant>
    */
   public function getCarburants(): Collection
   {
       return $this->carburants;
   }

   public function addCarburant(Carburant $carburant): self
   {
       if (!$this->carburants->contains($carburant)) {
           $this->carburants->add($carburant);
           $carburant->setVehicle($this);
       }

       return $this;
   }

   public function removeCarburant(Carburant $carburant): self
   {
       if ($this->carburants->removeElement($carburant)) {
           // set the owning side to null (unless already changed)
           if ($carburant->getVehicle() === $this) {
               $carburant->setVehicle(null);
           }
       }

       return $this;
   }

   /**
    * @return Collection<int, Versement>
    */
   public function getVersements(): Collection
   {
       return $this->versements;
   }

   public function addVersement(Versement $versement): self
   {
       if (!$this->versements->contains($versement)) {
           $this->versements->add($versement);
           $versement->setVehicle($this);
       }

       return $this;
   }

   public function removeVersement(Versement $versement): self
   {
       if ($this->versements->removeElement($versement)) {
           // set the owning side to null (unless already changed)
           if ($versement->getVehicle() === $this) {
               $versement->setVehicle(null);
           }
       }

       return $this;
   }

   /**
    * @return Collection<int, Alert>
    */
   public function getAlerts(): Collection
   {
       return $this->alerts;
   }

   public function addAlert(Alert $alert): self
   {
       if (!$this->alerts->contains($alert)) {
           $this->alerts->add($alert);
           $alert->setVehicle($this);
       }

       return $this;
   }

   public function removeAlert(Alert $alert): self
   {
       if ($this->alerts->removeElement($alert)) {
           // set the owning side to null (unless already changed)
           if ($alert->getVehicle() === $this) {
               $alert->setVehicle(null);
           }
       }

       return $this;
   }

   public function getGpx(): ?array
   {
       return $this->gpx;
   }

   public function setGpx(?array $gpx): static
   {
       $this->gpx = $gpx;

       return $this;
   }

   /**
    * @return Collection<int, VehicleTracker>
    */
   public function getVehicleTrackers(): Collection
   {
       return $this->vehicleTrackers;
   }

   public function addVehicleTracker(VehicleTracker $vehicleTracker): static
   {
       if (!$this->vehicleTrackers->contains($vehicleTracker)) {
           $this->vehicleTrackers->add($vehicleTracker);
           $vehicleTracker->setVehicle($this);
       }

       return $this;
   }

   public function removeVehicleTracker(VehicleTracker $vehicleTracker): static
   {
       if ($this->vehicleTrackers->removeElement($vehicleTracker)) {
           // set the owning side to null (unless already changed)
           if ($vehicleTracker->getVehicle() === $this) {
               $vehicleTracker->setVehicle(null);
           }
       }

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

   public function toArray(): array{
    return [
        "id"=>$this->getId(),
"name"=>$this->getName(),
"matricule"=>$this->getMatricule(),
"currentLat"=>$this->getCurrentLat(),
"currentLng"=>$this->getCurrentLng(),
"deviceID"=>$this->getDeviceID(),
"voletJaune"=>$this->getVoletJaune(),
"updatedAt"=>$this->getUpdatedAt(),
//"t"=>$this->getLine(),
"line"=>$this->getLine()?->getId(),];
   }

}
