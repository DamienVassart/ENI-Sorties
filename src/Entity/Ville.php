<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 * @UniqueEntity(fields={"nom"}, message="Cette ville existe déjà!")
 */
class Ville
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $codePostal;

    /**
     * @ORM\OneToMany(targetEntity=Lieu::class, mappedBy="idVille", cascade={"remove"})
     */
    private $lieux;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getIdlieu(): ?lieu
    {
        return $this->idLieu;
    }

    public function setIdLieu(?Lieu $idLieu): self
    {
        $this->idLieu = $idLieu;

        return $this;
    }

    /**
     * @return Collection|Lieu[]
     */
    public function getLieux(): Collection
    {
        return $this->lieux;
    }
}
