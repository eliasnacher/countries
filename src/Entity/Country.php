<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $commonName = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $officialName = null;

    #[ORM\ManyToOne(inversedBy: 'countries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $region = null;

    #[ORM\Column(length: 255)]
    private ?string $flag = null;

    #[ORM\Column]
    private ?int $population = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommonName(): ?string
    {
        return $this->commonName;
    }

    public function setCommonName(string $commonName): self
    {
        $this->commonName = $commonName;

        return $this;
    }

    public function getOfficialName(): ?string
    {
        return $this->officialName;
    }

    public function setOfficialName(string $officialName): self
    {
        $this->officialName = $officialName;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(string $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(int $population): self
    {
        $this->population = $population;

        return $this;
    }
}
