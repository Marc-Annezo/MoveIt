<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $nom;

    #[ORM\Column(type: 'string', length: 5)]
    /**
     * @Assert\Regex(
     *     pattern="^\d{5}$",
     *     match=true,
     *     message="Doit uniquement comporter 5 chiffres"
     * )
     */
    private ?string $CodePostal;

    #[ORM\OneToMany(mappedBy: 'idVille', targetEntity: Lieu::class)]
    private $Lieux;

    public function __construct()
    {
        $this->Lieux = new ArrayCollection();
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

    public function getCodePostal(): ?string
    {
        return $this->CodePostal;
    }

    public function setCodePostal(string $CodePostal): self
    {
        $this->CodePostal = $CodePostal;

        return $this;
    }

    /**
     * @return Collection|Lieu[]
     */
    public function getLieux(): Collection
    {
        return $this->Lieux;
    }

    public function addLieux(Lieu $lieux): self
    {
        if (!$this->Lieux->contains($lieux)) {
            $this->Lieux[] = $lieux;
            $lieux->setIdVille($this);
        }

        return $this;
    }

    public function removeLieux(Lieu $lieux): self
    {
        if ($this->Lieux->removeElement($lieux)) {
            // set the owning side to null (unless already changed)
            if ($lieux->getIdVille() === $this) {
                $lieux->setIdVille(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNom();
    }


}
