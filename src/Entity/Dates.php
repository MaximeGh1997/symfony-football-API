<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups as Groupes;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DatesRepository")
 * @ApiResource
 */
class Dates
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today", message="La date doit être utlérieure à la date d'aujourd'hui")
     * @Groupes({"matchs_subresource"})
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Matchs", inversedBy="date", cascade={"persist", "remove"})
     */
    private $matchNbr;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMatchNbr(): ?Matchs
    {
        return $this->matchNbr;
    }

    public function setMatchNbr(?Matchs $matchNbr): self
    {
        $this->matchNbr = $matchNbr;

        return $this;
    }
}
