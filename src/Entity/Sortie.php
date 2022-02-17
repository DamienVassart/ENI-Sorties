<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vueillez préciser un nom pour la sortie")
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     * @Assert\GreaterThan("today")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez préciser une durée pour la sortie")
     * @Assert\Positive(message="La durée doit être un nombre entier positif")
     */
    private $duree;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     * @Assert\GreaterThan("today")
     * @Assert\Expression(
     *     "this.getDateLimiteInscription < this.getDateHeureDebut",
     *     message="La date limite d'inscription ne peut être postérieure à la date de début"
     * )
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez préciser un nombre de participants pour la sortie")
     * @Assert\Positive(message="Le nombre de participants doit être un nombre entier positif")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infosSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $idEtat;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idLieu;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idCampus;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="sortiesOrganisateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idOrganisateur;

    /**
     * @ORM\ManyToMany(targetEntity=Participant::class, inversedBy="sortiesParticipants")
     */
    private $participants;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motifAnnulation;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getIdEtat(): ?Etat
    {
        return $this->idEtat;
    }

    public function setIdEtat(?Etat $idEtat): self
    {
        $this->idEtat = $idEtat;

        return $this;
    }

    public function getIdLieu(): ?Lieu
    {
        return $this->idLieu;
    }

    public function setIdLieu(?Lieu $idLieu): self
    {
        $this->idLieu = $idLieu;

        return $this;
    }

    public function getIdCampus(): ?Campus
    {
        return $this->idCampus;
    }

    public function setIdCampus(?Campus $idCampus): self
    {
        $this->idCampus = $idCampus;

        return $this;
    }

    public function getIdOrganisateur(): ?Participant
    {
        return $this->idOrganisateur;
    }

    public function setIdOrganisateur(?Participant $idOrganisateur): self
    {
        $this->idOrganisateur = $idOrganisateur;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): self
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

}
