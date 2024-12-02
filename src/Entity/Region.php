<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ApiResource]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shape = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: Line::class)]
    private Collection $liness;

    public function __construct()
    {
        
        $this->createdAt = new \DateTime('now',new \DateTimeZone('Africa/Kinshasa'));
        
        
        $this->liness = new ArrayCollection();
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

    public function getShape(): ?string
    {
        return $this->shape;
    }

    public function setShape(?string $shape): self
    {
        $this->shape = $shape;

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
            $liness->setRegion($this);
        }

        return $this;
    }

    public function removeLiness(Line $liness): static
    {
        if ($this->liness->removeElement($liness)) {
            // set the owning side to null (unless already changed)
            if ($liness->getRegion() === $this) {
                $liness->setRegion(null);
            }
        }

        return $this;
    }
}
