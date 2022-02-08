<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $nom;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $prenom;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $telephone;

    #[ORM\Column(type: 'boolean')]
    private $administrateur;

    #[ORM\Column(type: 'boolean')]
    private $actif;

    #[ORM\OneToOne(mappedBy: 'idParticipant', targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    private $idUtilisateur;

    #[ORM\ManyToOne(targetEntity: Site::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: true)]
    private $site;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class)]
    private $sorties_orga;

    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'inscrits')]
    private $sorties_participant;

    public function __construct()
    {
        $this->sorties_orga = new ArrayCollection();
        $this->sorties_participant = new ArrayCollection();
        $this->setAdministrateur(false);
        $this->setActif(true);

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getIdUtilisateur(): ?Utilisateur
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(Utilisateur $idUtilisateur): self
    {
        // set the owning side of the relation if necessary
        if ($idUtilisateur->getIdParticipant() !== $this) {
            $idUtilisateur->setIdParticipant($this);
        }

        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesOrga(): Collection
    {
        return $this->sorties_orga;
    }

    public function addSortiesOrga(Sortie $sortiesOrga): self
    {
        if (!$this->sorties_orga->contains($sortiesOrga)) {
            $this->sorties_orga[] = $sortiesOrga;
            $sortiesOrga->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrga(Sortie $sortiesOrga): self
    {
        if ($this->sorties_orga->removeElement($sortiesOrga)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrga->getOrganisateur() === $this) {
                $sortiesOrga->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesParticipant(): Collection
    {
        return $this->sorties_participant;
    }

    public function addSortiesParticipant(Sortie $sortiesParticipant): self
    {
        if (!$this->sorties_participant->contains($sortiesParticipant)) {
            $this->sorties_participant[] = $sortiesParticipant;
        }

        return $this;
    }

    public function removeSortiesParticipant(Sortie $sortiesParticipant): self
    {
        $this->sorties_participant->removeElement($sortiesParticipant);

        return $this;
    }
}
