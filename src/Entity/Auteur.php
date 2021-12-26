<?php

namespace App\Entity;

use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuteurRepository::class)]
class Auteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom_prenom;

    #[ORM\Column(type: 'string', length: 1)]
    private $sexe;

    #[ORM\Column(type: 'date')]
    private $date_de_naissance;

    #[ORM\Column(type: 'string', length: 255)]
    private $nationalite;

    #[ORM\ManyToMany(targetEntity: Livre::class, inversedBy: 'auteurs')]
    private $hh;

    public function __construct()
    {
        $this->hh = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPrenom(): ?string
    {
        return $this->nom_prenom;
    }

    public function setNomPrenom(string $nom_prenom): self
    {
        $this->nom_prenom = $nom_prenom;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->date_de_naissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $date_de_naissance): self
    {
        $this->date_de_naissance = $date_de_naissance;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(string $nationalite): self
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    /**
     * @return Collection|Livre[]
     */
    public function getHh(): Collection
    {
        return $this->hh;
    }

    public function addHh(Livre $hh): self
    {
        if (!$this->hh->contains($hh)) {
            $this->hh[] = $hh;
        }

        return $this;
    }

    public function removeHh(Livre $hh): self
    {
        $this->hh->removeElement($hh);

        return $this;
    }
}
