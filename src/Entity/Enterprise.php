<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EnterpriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnterpriseRepository::class)]
#[ApiResource]
class Enterprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'enterprise', targetEntity: Line::class)]
    private Collection $liness;

    public function __construct()
    {
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

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

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
            $liness->setEnterprise($this);
        }

        return $this;
    }

    public function removeLiness(Line $liness): static
    {
        if ($this->liness->removeElement($liness)) {
            // set the owning side to null (unless already changed)
            if ($liness->getEnterprise() === $this) {
                $liness->setEnterprise(null);
            }
        }

        return $this;
    }
}
