<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RechargeurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RechargeurRepository::class)]
#[ApiResource]
class Rechargeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
