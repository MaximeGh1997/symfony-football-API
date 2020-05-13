<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Matchs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="homeMatchs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="awayMatchs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team2;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $scoreT1;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $scoreT2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="wins")
     */
    private $winner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="defeats")
     */
    private $looser;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $draw;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Stades", inversedBy="matchs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $stade;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groups", inversedBy="matchs")
     */
    private $groupName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Stages", inversedBy="matchs")
     */
    private $stage;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPlayed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="matchNbr", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Dates", mappedBy="matchNbr", cascade={"persist", "remove"})
     */
    private $date;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam1(): ?Teams
    {
        return $this->team1;
    }

    public function setTeam1(?Teams $team1): self
    {
        $this->team1 = $team1;

        return $this;
    }

    public function getTeam2(): ?Teams
    {
        return $this->team2;
    }

    public function setTeam2(?Teams $team2): self
    {
        $this->team2 = $team2;

        return $this;
    }

    public function getScoreT1(): ?float
    {
        return $this->scoreT1;
    }

    public function setScoreT1(?float $scoreT1): self
    {
        $this->scoreT1 = $scoreT1;

        return $this;
    }

    public function getScoreT2(): ?float
    {
        return $this->scoreT2;
    }

    public function setScoreT2(?float $scoreT2): self
    {
        $this->scoreT2 = $scoreT2;

        return $this;
    }

    public function getWinner(): ?Teams
    {
        return $this->winner;
    }

    public function setWinner(?Teams $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getLooser(): ?Teams
    {
        return $this->looser;
    }

    public function setLooser(?Teams $looser): self
    {
        $this->looser = $looser;

        return $this;
    }

    public function getDraw(): ?bool
    {
        return $this->draw;
    }

    public function setDraw(?bool $draw): self
    {
        $this->draw = $draw;

        return $this;
    }

    public function getStade(): ?Stades
    {
        return $this->stade;
    }

    public function setStade(?Stades $stade): self
    {
        $this->stade = $stade;

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

    public function getStage(): ?Stages
    {
        return $this->stage;
    }

    public function setStage(?Stages $stage): self
    {
        $this->stage = $stage;

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

    public function getIsPlayed(): ?bool
    {
        return $this->isPlayed;
    }

    public function setIsPlayed(?bool $isPlayed): self
    {
        $this->isPlayed = $isPlayed;

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

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setMatchNbr($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getMatchNbr() === $this) {
                $comment->setMatchNbr(null);
            }
        }

        return $this;
    }

    public function getDate(): ?Dates
    {
        return $this->date;
    }

    public function setDate(?Dates $date): self
    {
        $this->date = $date;

        // set (or unset) the owning side of the relation if necessary
        $newMatchNbr = null === $date ? null : $this;
        if ($date->getMatchNbr() !== $newMatchNbr) {
            $date->setMatchNbr($newMatchNbr);
        }

        return $this;
    }
}
