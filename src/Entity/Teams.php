<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Teams
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $logo;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cover;

    /**
     * @ORM\Column(type="float")
     */
    private $points;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Stades", mappedBy="resident", cascade={"persist", "remove"})
     */
    private $stade;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Matchs", mappedBy="team1")
     */
    private $homeMatchs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Matchs", mappedBy="team2")
     */
    private $awayMatchs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Matchs", mappedBy="winner")
     */
    private $wins;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Matchs", mappedBy="looser")
     */
    private $defeats;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groups", inversedBy="teams")
     */
    private $groupName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    private $matchs;

    private $matchsPlayed;

    private $groupWins;

    private $draws;

    private $groupDraws;

    private $groupDefeats;

    private $goals;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

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

    /**
     * Permet d'intialiser le nombre de points
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializePoints(){
        if(empty($this->points)){
            $this->points = 0;
        }
    }

    // Permet de récuperer le nbr total de matchs
    public function getAllMatchs()
    {
        $matchs1 = $this->getHomeMatchs()->toArray();
        $matchs2 = $this->getAwayMatchs()->toArray();

        $this->matchs = array_merge($matchs1, $matchs2);

        return $this->matchs;
    }

    // Permet de récuperer le nbr total de matchs joués
    public function getMatchsPlayed()
    {
        $matchs = $this->getAllMatchs();

        foreach($matchs as $match){
            if($match->getIsPlayed() != false && $match->getStage() == null){
                $this->matchsPlayed[] += $match->getIsPlayed();
            }
        }

        return $this->matchsPlayed;
    }

    // Permet de récupérer le nbr de victoire (UNIQUEMENT SUR LES MATCHS DE GROUPES)
    public function getGroupWins()
    {
        $matchs = $this->getWins();

        foreach ($matchs as $match) {
            if($match->getStage() == null){
                $this->groupWins[] = $match;
            }
        }

        return $this->groupWins;
    }

    // Permet de récuperer le nombre total de matchs nul (UNIQUEMENT SUR LES MATCHS DE GROUPES)
    public function getGroupDraws()
    {
        $matchs = $this->getAllMatchs();

        foreach($matchs as $match){
            if($match->getDraw() != null && $match->getStage() == null){
                $this->groupDraws[] += $match->getDraw();
            }
        }
        return $this->groupDraws;
    }

    // Permet de récuperer le nombre total de matchs nul (SUR TOUTS LES MATCHS)
    public function getDraws()
    {
        $matchs = $this->getAllMatchs();

        foreach($matchs as $match){
            if($match->getDraw() != null){
                $this->draws[] += $match->getDraw();
            }
        }
        return $this->draws;
    }

    // Permet de récupérer le nbr de défaites (UNIQUEMENT SUR LES MATCHS DE GROUPES)
    public function getGroupDefeats()
    {
        $matchs = $this->getDefeats();

        foreach ($matchs as $match) {
            if($match->getStage() == null){
                $this->groupDefeats[] = $match;
            }
        }

        return $this->groupDefeats;
    }

    // Permet de récupérer le nbr de buts marqués
    public function getGoals(){
        $this->goals = 0;

        foreach ($this->homeMatchs as $match) {
            $this->goals = $this->goals + $match->getScoreT1();
        }

        foreach ($this->awayMatchs as $match) {
            $this->goals = $this->goals + $match->getScoreT2();
        }

        return $this->goals;
    }

    public function __construct()
    {
        $this->homeMatchs = new ArrayCollection();
        $this->awayMatchs = new ArrayCollection();
        $this->wins = new ArrayCollection();
        $this->defeats = new ArrayCollection();
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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

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

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function setPoints(float $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getStade(): ?Stades
    {
        return $this->stade;
    }

    public function setStade(Stades $stade): self
    {
        $this->stade = $stade;

        // set the owning side of the relation if necessary
        if ($stade->getResident() !== $this) {
            $stade->setResident($this);
        }

        return $this;
    }

    /**
     * @return Collection|Matchs[]
     */
    public function getHomeMatchs(): Collection
    {
        return $this->homeMatchs;
    }

    public function addHomeMatch(Matchs $homeMatch): self
    {
        if (!$this->homeMatchs->contains($homeMatch)) {
            $this->homeMatchs[] = $homeMatch;
            $homeMatch->setTeam1($this);
        }

        return $this;
    }

    public function removeHomeMatch(Matchs $homeMatch): self
    {
        if ($this->homeMatchs->contains($homeMatch)) {
            $this->homeMatchs->removeElement($homeMatch);
            // set the owning side to null (unless already changed)
            if ($homeMatch->getTeam1() === $this) {
                $homeMatch->setTeam1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Matchs[]
     */
    public function getAwayMatchs(): Collection
    {
        return $this->awayMatchs;
    }

    public function addAwayMatch(Matchs $awayMatch): self
    {
        if (!$this->awayMatchs->contains($awayMatch)) {
            $this->awayMatchs[] = $awayMatch;
            $awayMatch->setTeam2($this);
        }

        return $this;
    }

    public function removeAwayMatch(Matchs $awayMatch): self
    {
        if ($this->awayMatchs->contains($awayMatch)) {
            $this->awayMatchs->removeElement($awayMatch);
            // set the owning side to null (unless already changed)
            if ($awayMatch->getTeam2() === $this) {
                $awayMatch->setTeam2(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Matchs[]
     */
    public function getWins(): Collection
    {
        return $this->wins;
    }

    public function addWin(Matchs $win): self
    {
        if (!$this->wins->contains($win)) {
            $this->wins[] = $win;
            $win->setWinner($this);
        }

        return $this;
    }

    public function removeWin(Matchs $win): self
    {
        if ($this->wins->contains($win)) {
            $this->wins->removeElement($win);
            // set the owning side to null (unless already changed)
            if ($win->getWinner() === $this) {
                $win->setWinner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Matchs[]
     */
    public function getDefeats(): Collection
    {
        return $this->defeats;
    }

    public function addDefeat(Matchs $defeat): self
    {
        if (!$this->defeats->contains($defeat)) {
            $this->defeats[] = $defeat;
            $defeat->setLooser($this);
        }

        return $this;
    }

    public function removeDefeat(Matchs $defeat): self
    {
        if ($this->defeats->contains($defeat)) {
            $this->defeats->removeElement($defeat);
            // set the owning side to null (unless already changed)
            if ($defeat->getLooser() === $this) {
                $defeat->setLooser(null);
            }
        }

        return $this;
    }

    public function getGroupName(): ?Groups
    {
        return $this->groupName;
    }

    public function setGroupName(?Groups $groupName): self
    {
        $this->groupName = $groupName;

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
}
