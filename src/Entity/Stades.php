<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StadesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Stades
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, minMessage="Le nom du stade doit faire au moins 5 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     */
    private $city;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type(
     *     type="float",
     *     message="La capacité entrée est invalide."
     * )
     */
    private $capacity;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=20, minMessage="La description doit faire au moins 20 caractères")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Url()
     * @Assert\Length(max=255, maxMessage="L'url de l'image doit faire moins de 255 caractères")
     */
    private $cover;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Teams", inversedBy="stade", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $resident;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Matchs", mappedBy="stade")
     */
    private $matchs;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groups", inversedBy="stades")
     * @ORM\JoinColumn(nullable=true)
     */
    private $groups;

    /**
     * Permet d'intialiser la date de création
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeCreatedAt(){
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime('Europe/Brussels');
        }
    }

    public function __construct()
    {
        $this->matchs = new ArrayCollection();
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCapacity(): ?float
    {
        return $this->capacity;
    }

    public function setCapacity(float $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function getResident(): ?Teams
    {
        return $this->resident;
    }

    public function setResident(Teams $resident): self
    {
        $this->resident = $resident;

        return $this;
    }

    /**
     * @return Collection|Matchs[]
     */
    public function getMatchs(): Collection
    {
        return $this->matchs;
    }

    public function addMatch(Matchs $match): self
    {
        if (!$this->matchs->contains($match)) {
            $this->matchs[] = $match;
            $match->setStade($this);
        }

        return $this;
    }

    public function removeMatch(Matchs $match): self
    {
        if ($this->matchs->contains($match)) {
            $this->matchs->removeElement($match);
            // set the owning side to null (unless already changed)
            if ($match->getStade() === $this) {
                $match->setStade(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getGroups(): ?Groups
    {
        return $this->groups;
    }

    public function setGroups(?Groups $groups): self
    {
        $this->groups = $groups;

        return $this;
    }
}
