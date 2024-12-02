<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(length: 64)]
    private ?string $fullname = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'conveyor', targetEntity: Route::class)]
    private Collection $routes;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $tagUid = null;

    #[ORM\Column(nullable: true)]
    private ?float $balance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RechargeUser::class)]
    private Collection $rechargeUsers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Logins::class)]
    private Collection $logins;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $identityCard = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Vehicle $vehicle = null;

    const SERVER_PATH_TO_IMAGE_FOLDER = 'images/users';
    /**
     * Unmapped property to handle file uploads
     */
    private ?UploadedFile $image = null;
    /**
     * Unmapped property to handle file uploads
     */
    private ?UploadedFile $card = null;
    /**
     * Unmapped property to handle password
     */
    private ?string $plainPassword = null;

    #[ORM\OneToMany(mappedBy: 'driver', targetEntity: KilometerTrack::class)]
    private Collection $kilometerTracks;

    #[ORM\OneToMany(mappedBy: 'driver', targetEntity: Versement::class)]
    private Collection $versements;



    public function __construct()
    {
        $this->routes = new ArrayCollection();
        $this->rechargeUsers = new ArrayCollection();
        $this->logins = new ArrayCollection();
        $this->kilometerTracks = new ArrayCollection();
        $this->versements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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
            $route->setConveyor($this);
        }

        return $this;
    }

    public function removeRoute(Route $route): self
    {
        if ($this->routes->removeElement($route)) {
            // set the owning side to null (unless already changed)
            if ($route->getConveyor() === $this) {
                $route->setConveyor(null);
            }
        }

        return $this;
    }

    public function getTagUid(): ?string
    {
        return $this->tagUid;
    }

    public function setTagUid(?string $tagUid): self
    {
        $this->tagUid = $tagUid;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(?float $balance): self
    {
        $this->balance = $balance;

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
     * @return Collection<int, RechargeUser>
     */
    public function getRechargeUsers(): Collection
    {
        return $this->rechargeUsers;
    }

    public function addRechargeUser(RechargeUser $rechargeUser): self
    {
        if (!$this->rechargeUsers->contains($rechargeUser)) {
            $this->rechargeUsers->add($rechargeUser);
            $rechargeUser->setUser($this);
        }

        return $this;
    }

    public function removeRechargeUser(RechargeUser $rechargeUser): self
    {
        if ($this->rechargeUsers->removeElement($rechargeUser)) {
            // set the owning side to null (unless already changed)
            if ($rechargeUser->getUser() === $this) {
                $rechargeUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Logins>
     */
    public function getLogins(): Collection
    {
        return $this->logins;
    }

    public function addLogin(Logins $login): self
    {
        if (!$this->logins->contains($login)) {
            $this->logins->add($login);
            $login->setUser($this);
        }

        return $this;
    }

    public function removeLogin(Logins $login): self
    {
        if ($this->logins->removeElement($login)) {
            // set the owning side to null (unless already changed)
            if ($login->getUser() === $this) {
                $login->setUser(null);
            }
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getIdentityCard(): ?string
    {
        return $this->identityCard;
    }

    public function setIdentityCard(?string $identityCard): self
    {
        $this->identityCard = $identityCard;

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
    public function setImage(?UploadedFile $image = null): void
    {
        $this->image = $image;
    }

    public function getImage(): ?UploadedFile
    {
        return $this->image;
    }
    public function setCard(?UploadedFile $card = null): void
    {
        $this->card = $card;
    }

    public function getCard(): ?UploadedFile
    {
        return $this->card;
    }
    public function setPlainPassword(?string $plainPassword = null): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }


    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload(): void
    {
        // the file property can be empty if the field is not required
        if (null !== $this->getImage()) {
             // move takes the target directory and target filename as params
       $fname = $this->username.'_photo_'.$this->id.'.jpg';
       //die(var_dump(dirname(__DIR__).self::SERVER_PATH_TO_IMAGE_FOLDER));
       $this->getImage()->move(
        self::SERVER_PATH_TO_IMAGE_FOLDER,
           $fname
       );

       // set the path property to the filename where you've saved the file
       $this->photo = $fname;

       // clean up the file property as you won't need it anymore
       $this->setImage(null);
    }
    if (null !== $this->getCard()) {
        // move takes the target directory and target filename as params
    $fname = $this->username.'_card_'.$this->id.'.jpg';
    //die(var_dump(dirname(__DIR__).self::SERVER_PATH_TO_IMAGE_FOLDER));
    $this->getCard()->move(
    self::SERVER_PATH_TO_IMAGE_FOLDER,
      $fname
    );

    // set the path property to the filename where you've saved the file
    $this->identityCard = $fname;

    // clean up the file property as you won't need it anymore
    $this->setCard(null);
    }

       // we use the original file name here but you should
       // sanitize it at least to avoid any security issues

      
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
           $kilometerTrack->setDriver($this);
       }

       return $this;
   }

   public function removeKilometerTrack(KilometerTrack $kilometerTrack): self
   {
       if ($this->kilometerTracks->removeElement($kilometerTrack)) {
           // set the owning side to null (unless already changed)
           if ($kilometerTrack->getDriver() === $this) {
               $kilometerTrack->setDriver(null);
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
           $versement->setDriver($this);
       }

       return $this;
   }

   public function removeVersement(Versement $versement): self
   {
       if ($this->versements->removeElement($versement)) {
           // set the owning side to null (unless already changed)
           if ($versement->getDriver() === $this) {
               $versement->setDriver(null);
           }
       }

       return $this;
   }


}
